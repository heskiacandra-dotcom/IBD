<?php

namespace App\Filament\Resources\Pemasukans\Tables;

use App\Models\Approval;
use App\Models\Pemasukan;
use App\Models\Warga;
use App\Services\MidtransService;
use App\Services\WhatsAppGatewayService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PemasukansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Tagihan_Id')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => '#' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->sortable()
                    ->width('70px'),

                TextColumn::make('nama_kepala_keluarga')
                    ->label('Warga / No. KK')
                    ->weight('bold')
                    ->description(fn (Pemasukan $record) => 'KK: ' . ($record->ID_KK ?? '-'))
                    ->searchable(['ID_KK', 'nama_tagihan'])
                    ->wrap(),

                TextColumn::make('nama_tagihan')
                    ->label('Rincian Tagihan')
                    ->weight('semibold')
                    ->description(fn (Pemasukan $record) => ($record->Kategori ?? 'Iuran Warga') . ($record->tgl_jatuh_tmp ? ' • Tempo: ' . \Carbon\Carbon::parse($record->tgl_jatuh_tmp)->format('d M Y') : ''))
                    ->searchable()
                    ->wrap(),

                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->weight('bold')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('status_tagihan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'lunas' => 'success',
                        'pending_approval', 'menunggu verifikasi', 'menunggu', 'pending' => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => match (strtolower($state)) {
                        'lunas' => 'Lunas',
                        'pending_approval', 'menunggu verifikasi', 'menunggu', 'pending' => 'Verifikasi',
                        default => 'Belum Lunas',
                    }),

                TextColumn::make('bukti_bayar')
                    ->label('Bukti')
                    ->alignCenter()
                    ->badge()
                    ->formatStateUsing(fn ($state, Pemasukan $record) => $record->hasValidBuktiBayar() ? 'Ada' : '-')
                    ->color(fn ($state, Pemasukan $record) => $record->hasValidBuktiBayar() ? 'info' : 'gray'),

                TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn ($state, Pemasukan $record) => match(strtolower($state ?? '')) {
                        'qris', 'qris_midtrans', 'midtrans' => '⚡ QRIS',
                        'gopay' => 'GoPay',
                        'shopeepay' => 'ShopeePay',
                        'bank_transfer', 'manual_transfer' => 'Transfer Bank',
                        'tunai' => 'Kas Tunai',
                        default => $record->hasValidBuktiBayar() ? 'Transfer' : 'Tunai',
                    })
                    ->color(fn ($state) => match(strtolower($state ?? '')) {
                        'qris', 'qris_midtrans', 'midtrans', 'gopay', 'shopeepay' => 'success',
                        'bank_transfer', 'manual_transfer' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status_tagihan')
                    ->label('Filter Status')
                    ->options([
                        'Lunas' => 'Lunas',
                        'Belum Lunas' => 'Belum Lunas',
                        'pending_approval' => 'Menunggu Verifikasi',
                    ]),
                SelectFilter::make('Kategori')
                    ->label('Filter Kategori')
                    ->options([
                        'Iuran Warga' => 'Iuran Warga',
                        'Sumbangan Sosial' => 'Sumbangan Sosial',
                        'Sewa Lapangan & Fasilitas' => 'Sewa Fasilitas',
                    ]),
            ])
            ->recordActions([
                Action::make('preview_bukti')
                    ->label('Bukti')
                    ->icon('heroicon-m-document-magnifying-glass')
                    ->color('info')
                    ->visible(fn (Pemasukan $record) => $record->hasValidBuktiBayar())
                    ->modalHeading('Verifikasi Bukti Transfer Warga')
                    ->modalWidth('2xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Pemasukan $record) => view('filament.modals.preview-bukti-pemasukan', ['record' => $record]))
                    ->extraModalFooterActions(function (Pemasukan $record): array {
                        $isLunas = strtolower($record->status_tagihan ?? '') === 'lunas';

                        if ($isLunas) {
                            return [];
                        }

                        return [
                            Action::make('approve_pembayaran')
                                ->label('Setujui Pembayaran')
                                ->icon('heroicon-m-check-circle')
                                ->color('success')
                                ->requiresConfirmation()
                                ->modalHeading('Setujui Pembayaran Iuran')
                                ->modalDescription(fn () => "Apakah Anda yakin ingin memverifikasi tagihan #TAG-{$record->Tagihan_Id} ({$record->nama_tagihan}) sebesar Rp " . number_format($record->nominal, 0, ',', '.') . " sebagai Lunas?")
                                ->action(function () use ($record) {
                                    \Illuminate\Support\Facades\DB::transaction(function () use ($record) {
                                        $record->update(['status_tagihan' => 'lunas']);

                                        Approval::where('tabel_sumber', 'PEMASUKAN')
                                            ->where('id_referensi', (string) $record->Tagihan_Id)
                                            ->update([
                                                'status' => 'approved',
                                                'tgl_approval' => now(),
                                                'Admin_ID' => auth()->id() ?? 3,
                                            ]);
                                    });

                                    try {
                                        $warga = Warga::where('ID_KK', $record->ID_KK)->first();
                                        if ($warga) {
                                            app(WhatsAppGatewayService::class)->notifyPembayaranLunas($warga, $record);
                                        }
                                    } catch (\Throwable $e) {}

                                    Notification::make()
                                        ->title('Verifikasi Berhasil')
                                        ->body('Pembayaran iuran berhasil diverifikasi lunas.')
                                        ->success()
                                        ->send();
                                }),

                            Action::make('reject_pembayaran')
                                ->label('Tolak Pembayaran')
                                ->icon('heroicon-m-x-circle')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->modalHeading('Tolak Pembayaran Iuran')
                                ->modalDescription('Wajib mengisi catatan alasan penolakan agar warga dapat memperbaikinya.')
                                ->form([
                                    Textarea::make('catatan')
                                        ->label('Alasan Penolakan')
                                        ->placeholder('Contoh: Nominal tidak sesuai atau bukti transfer buram...')
                                        ->required()
                                        ->rows(3),
                                ])
                                ->action(function (array $data) use ($record) {
                                    \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {
                                        $record->update(['status_tagihan' => 'belum_lunas']);

                                        $approval = Approval::where('tabel_sumber', 'PEMASUKAN')
                                            ->where('id_referensi', (string) $record->Tagihan_Id)
                                            ->first();

                                        if ($approval) {
                                            $approval->update([
                                                'status' => 'rejected',
                                                'catatan' => $data['catatan'],
                                                'tgl_approval' => now(),
                                                'Admin_ID' => auth()->id() ?? 3,
                                            ]);
                                        }
                                    });

                                    Notification::make()
                                        ->title('Pembayaran Ditolak')
                                        ->body('Pembayaran iuran berhasil ditolak.')
                                        ->danger()
                                        ->send();
                                }),
                        ];
                    }),

                Action::make('mark_as_paid')
                    ->label('Lunas')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => strtolower($record->status_tagihan) !== 'lunas')
                    ->action(function ($record) {
                        $record->update(['status_tagihan' => 'Lunas']);

                        Approval::where('tabel_sumber', 'PEMASUKAN')
                            ->where('id_referensi', (string) $record->Tagihan_Id)
                            ->update([
                                'status' => 'Disetujui',
                                'tgl_approval' => now(),
                                'Admin_ID' => auth()->id() ?? 3,
                            ]);

                        try {
                            $warga = Warga::where('ID_KK', $record->ID_KK)->first();
                            if ($warga) {
                                app(WhatsAppGatewayService::class)->notifyPembayaranLunas($warga, $record);
                            }
                        } catch (\Throwable $e) {}

                        Notification::make()
                            ->title('Pembayaran Diverifikasi')
                            ->body("Tagihan #TAG-{$record->Tagihan_Id} berhasil disahkan Lunas.")
                            ->success()
                            ->send();
                    }),

                Action::make('cancel_or_refund_midtrans')
                    ->label('Batalkan')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('danger')
                    ->visible(fn (Pemasukan $record) => strtolower($record->status_tagihan ?? '') === 'lunas')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Pembayaran Tagihan')
                    ->modalDescription(fn (Pemasukan $record) => "Tagihan #TAG-{$record->Tagihan_Id} ({$record->nama_tagihan}) sebesar Rp " . number_format($record->nominal, 0, ',', '.') . " akan diubah menjadi Belum Lunas" . ($record->order_id ? ", dan transaksi di server Midtrans akan otomatis dibatalkan." : "."))
                    ->form([
                        Textarea::make('alasan')
                            ->label('Alasan Pembatalan')
                            ->placeholder('Contoh: Warga salah bayar atau tagihan dibatalkan...')
                            ->default('Pembatalan transaksi oleh pengurus RT')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (array $data, Pemasukan $record) {
                        $result = app(MidtransService::class)->cancelOrRefundTransaction($record, $data['alasan']);

                        Notification::make()
                            ->title('Pembayaran Dibatalkan')
                            ->body($result['message'])
                            ->warning()
                            ->send();
                    }),

                ActionGroup::make([
                    ViewAction::make()->slideOver(),
                    EditAction::make()->slideOver(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

