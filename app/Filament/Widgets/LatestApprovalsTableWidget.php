<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Approval;
use App\Models\Surat;
use App\Models\Pemasukan;

class LatestApprovalsTableWidget extends TableWidget
{
    protected static ?int $sort = 5;

    protected static ?string $heading = 'Antrean Pengajuan Menunggu Approval';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Approval::query()
                    ->whereIn('status', ['Menunggu', 'Pending', 'menunggu', 'pending'])
                    ->latest('id_approval')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('id_approval')
                    ->label('ID Tiket')
                    ->formatStateUsing(fn ($state) => '#APP-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->copyable()
                    ->sortable(),

                TextColumn::make('tabel_sumber')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match (strtoupper($state)) {
                        'SURAT' => 'info',
                        'PEMASUKAN' => 'success',
                        'INVENTARIS' => 'warning',
                        'KEGIATAN' => 'primary',
                        'PENGELUARAN' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('source_detail')
                    ->label('Uraian Pengajuan')
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('catatan')
                    ->label('Keterangan / Keperluan')
                    ->limit(50)
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Waktu Masuk')
                    ->dateTime('d M Y, H:i \W\I\B')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'disetujui' => 'success',
                        'menunggu', 'pending' => 'warning',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->recordActions([
                Action::make('review_tiket')
                    ->label('Review')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('primary')
                    ->slideOver()
                    ->modalHeading(fn (Approval $record) => 'Detail & Verifikasi Berkas Tiket #APP-' . str_pad($record->id_approval, 4, '0', STR_PAD_LEFT))
                    ->modalWidth('2xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Approval $record) => view('filament.modals.review-tiket-approval', ['record' => $record]))
                    ->extraModalFooterActions(function (Approval $record): array {
                        $isPending = in_array(strtolower($record->status), ['menunggu', 'pending']);
                        if (!$isPending) {
                            return [];
                        }

                        return [
                            Action::make('widget_drawer_approve')
                                ->label('Setujui Pengajuan')
                                ->icon('heroicon-m-check-circle')
                                ->color('success')
                                ->requiresConfirmation()
                                ->modalHeading('Konfirmasi Persetujuan')
                                ->modalDescription(fn () => "Apakah Anda yakin ingin menyetujui pengajuan {$record->source_detail}?")
                                ->action(function () use ($record) {
                                    $record->update([
                                        'status' => 'Disetujui',
                                        'tgl_approval' => now(),
                                        'Admin_ID' => auth()->id() ?? $record->Admin_ID ?? 1,
                                    ]);

                                    if ($record->tabel_sumber === 'SURAT') {
                                        Surat::where('surat_id', $record->id_referensi)
                                            ->orWhere('id_surat', $record->id_referensi)
                                            ->update(['status' => 'Disetujui']);
                                    }
                                    if ($record->tabel_sumber === 'PEMASUKAN') {
                                        Pemasukan::where('Tagihan_Id', $record->id_referensi)
                                            ->update(['status_tagihan' => 'Lunas']);
                                    }

                                    Notification::make()
                                        ->title('Pengajuan Disetujui')
                                        ->body("Pengajuan #APP-{$record->id_approval} ({$record->tabel_sumber}) telah disahkan.")
                                        ->success()
                                        ->send();
                                }),
                        ];
                    }),

                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Persetujuan')
                    ->modalDescription(fn (Approval $record) => "Apakah Anda yakin ingin menyetujui pengajuan {$record->source_detail}?")
                    ->action(function (Approval $record) {
                        $record->update([
                            'status' => 'Disetujui',
                            'tgl_approval' => now(),
                            'Admin_ID' => auth()->id() ?? $record->Admin_ID,
                        ]);

                        if ($record->tabel_sumber === 'SURAT') {
                            Surat::where('surat_id', $record->id_referensi)
                                ->orWhere('id_surat', $record->id_referensi)
                                ->update(['status' => 'Disetujui']);
                        }
                        if ($record->tabel_sumber === 'PEMASUKAN') {
                            Pemasukan::where('Tagihan_Id', $record->id_referensi)
                                ->update(['status_tagihan' => 'Lunas']);
                        }

                        Notification::make()
                            ->title('Pengajuan Berhasil Disetujui')
                            ->body("Pengajuan #APP-{$record->id_approval} ({$record->tabel_sumber}) telah disahkan.")
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Penolakan')
                    ->modalDescription(fn (Approval $record) => "Apakah Anda yakin ingin menolak pengajuan ini?")
                    ->action(function (Approval $record) {
                        $record->update([
                            'status' => 'Ditolak',
                            'tgl_approval' => now(),
                            'Admin_ID' => auth()->id() ?? $record->Admin_ID,
                        ]);

                        if ($record->tabel_sumber === 'SURAT') {
                            Surat::where('surat_id', $record->id_referensi)
                                ->orWhere('id_surat', $record->id_referensi)
                                ->update(['status' => 'Ditolak']);
                        }

                        Notification::make()
                            ->title('Pengajuan Ditolak')
                            ->body("Pengajuan #APP-{$record->id_approval} telah ditolak.")
                            ->danger()
                            ->send();
                    }),
            ])
            ->paginated(false);
    }
}
