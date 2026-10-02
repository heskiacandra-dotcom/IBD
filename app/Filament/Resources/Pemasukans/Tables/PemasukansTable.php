<?php

namespace App\Filament\Resources\Pemasukans\Tables;

use App\Models\Approval;
use App\Models\Pemasukan;
use App\Models\Warga;
use App\Services\WhatsAppGatewayService;
use Filament\Actions\Action;
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
                    ->label('ID Tagihan')
                    ->formatStateUsing(fn ($state) => '#TAG-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->sortable(),

                ImageColumn::make('bukti_bayar')
                    ->label('Bukti')
                    ->state(fn (Pemasukan $record) => $record->hasValidBuktiBayar() ? $record->bukti_bayar_url : null)
                    ->square()
                    ->size(38)
                    ->extraImgAttributes(['class' => 'rounded-lg border border-slate-200 shadow-2xs object-cover'])
                    ->placeholder('Tanpa Bukti')
                    ->tooltip('Klik "Lihat Bukti" untuk pratinjau penuh'),

                TextColumn::make('nama_tagihan')
                    ->label('Nama Tagihan')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('ID_KK')
                    ->label('No. KK')
                    ->description(fn (Pemasukan $record) => $record->nama_kepala_keluarga)
                    ->searchable(),

                TextColumn::make('Kategori')
                    ->label('Kategori')
                    ->badge(),

                TextColumn::make('nominal')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('tgl_jatuh_tmp')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status_tagihan')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'lunas' => 'success',
                        'belum lunas', 'menunggu', 'pending', 'pending_approval', 'menunggu verifikasi' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status_tagihan')
                    ->label('Filter Status')
                    ->options([
                        'Lunas' => 'Lunas',
                        'Belum Lunas' => 'Belum Lunas',
                        'pending_approval' => 'Menunggu Verifikasi (Pending)',
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
                    ->label('Periksa Bukti')
                    ->icon('heroicon-o-eye')
                    ->color('info')
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

                                        // Sinkronisasi status di tabel APPROVAL
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

                                        // Sinkronisasi status di tabel APPROVAL
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
                    ->label('Verifikasi Lunas')
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
                            ->body("Tagihan #TAG-{$record->Tagihan_Id} berhasil disahkan.")
                            ->success()
                            ->send();
                    }),

                ViewAction::make()->slideOver(),
                EditAction::make()->slideOver(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
