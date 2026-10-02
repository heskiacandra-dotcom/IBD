<?php

namespace App\Filament\Resources\Surats\Tables;

use App\Models\Approval;
use App\Models\Surat;
use App\Services\WhatsAppGatewayService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SuratsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('surat_id')
                    ->label('ID Surat')
                    ->formatStateUsing(fn ($state) => '#SRT-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->copyable()
                    ->sortable(),

                TextColumn::make('warga.Nama_Lengkap')
                    ->label('Pemohon')
                    ->description(fn (Surat $record) => 'NIK: ' . $record->NIK)
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('Jenis_surat')
                    ->label('Jenis Surat')
                    ->searchable()
                    ->badge(),

                TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->placeholder('Belum Diterbitkan')
                    ->badge()
                    ->color(fn ($state) => filled($state) ? 'info' : 'gray')
                    ->searchable(),

                TextColumn::make('perihal')
                    ->label('Keperluan')
                    ->limit(40)
                    ->tooltip(fn ($state) => $state),

                TextColumn::make('tgl_pengajuan')
                    ->label('Tgl Pengajuan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'disetujui', 'selesai', 'approved' => 'success',
                        'menunggu', 'pending' => 'warning',
                        'ditolak', 'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('surat_id', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'pending' => 'Menunggu Tindakan (Pending)',
                        'approved' => 'Disetujui / Terbit',
                        'rejected' => 'Ditolak',
                    ]),
                SelectFilter::make('Jenis_surat')
                    ->label('Filter Jenis Surat')
                    ->options([
                        'Surat Pengantar Pembuatan KTP & KK Baru' => 'Pengantar KTP & KK',
                        'Surat Pengantar SKCK Kepolisian' => 'Pengantar SKCK',
                        'Surat Keterangan Domisili Tempat Tinggal' => 'Keterangan Domisili',
                        'Surat Keterangan Usaha (SKU) Mikro Lingkungan' => 'Keterangan Usaha (SKU)',
                        'Surat Keterangan Izin Keramaian Lingkungan' => 'Izin Keramaian',
                    ]),
            ])
            ->recordActions([
                Action::make('approve_terbitkan')
                    ->label('Setujui & Terbitkan')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui & Terbitkan Surat Pengantar')
                    ->modalDescription(function (Surat $record) {
                        $nomorPreview = $record->nomor_surat ?: Surat::generateNomorSurat($record->surat_id, $record->warga?->ID_RT);
                        return "Apakah Anda yakin ingin menyetujui permohonan surat ini? Sistem akan menerbitkan nomor resmi: {$nomorPreview}";
                    })
                    ->visible(fn (Surat $record) => !in_array(strtolower($record->status), ['approved', 'disetujui', 'selesai']))
                    ->action(function (Surat $record) {
                        $nomorSurat = $record->nomor_surat ?: Surat::generateNomorSurat($record->surat_id, $record->warga?->ID_RT);

                        $record->update([
                            'status' => 'approved',
                            'nomor_surat' => $nomorSurat,
                        ]);

                        // Sinkronisasi status di tabel APPROVAL
                        Approval::where('tabel_sumber', 'SURAT')
                            ->where('id_referensi', (string) $record->surat_id)
                            ->update([
                                'status' => 'approved',
                                'tgl_approval' => now(),
                                'Admin_ID' => auth()->id() ?? 1,
                            ]);

                        try {
                            if ($record->warga) {
                                app(WhatsAppGatewayService::class)->notifySuratDisetujui($record->warga, $record);
                            }
                        } catch (\Throwable $e) {}

                        Notification::make()
                            ->title('Surat Berhasil Diterbitkan')
                            ->body("Surat Pengantar telah disahkan dengan Nomor Resmi: {$nomorSurat}")
                            ->success()
                            ->send();
                    }),

                Action::make('tolak_permohonan')
                    ->label('Tolak')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->modalHeading('Tolak Permohonan Surat')
                    ->modalDescription('Masukkan alasan penolakan agar warga pemohon mengetahui perbaikan yang diperlukan.')
                    ->form([
                        Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->placeholder('Contoh: Berkas persyaratan belum lengkap atau pemohon belum terdaftar dalam KK aktif.')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(fn (Surat $record) => !in_array(strtolower($record->status), ['approved', 'disetujui', 'selesai']))
                    ->action(function (Surat $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                        ]);

                        // Sinkronisasi status di tabel APPROVAL
                        $approval = Approval::where('tabel_sumber', 'SURAT')
                            ->where('id_referensi', (string) $record->surat_id)
                            ->first();

                        if ($approval) {
                            $approval->update([
                                'status' => 'rejected',
                                'catatan' => ($approval->catatan ? $approval->catatan . ' | ' : '') . 'Alasan Penolakan: ' . $data['alasan_penolakan'],
                                'tgl_approval' => now(),
                                'Admin_ID' => auth()->id() ?? 1,
                            ]);
                        }

                        Notification::make()
                            ->title('Permohonan Surat Ditolak')
                            ->body("Permohonan surat #SRT-{$record->surat_id} ditolak: {$data['alasan_penolakan']}")
                            ->danger()
                            ->send();
                    }),

                Action::make('download_pdf')
                    ->label('Unduh PDF')
                    ->icon('heroicon-m-printer')
                    ->color('primary')
                    ->url(fn (Surat $record) => route('surat.pdf.download', ['id' => $record->surat_id]))
                    ->openUrlInNewTab()
                    ->visible(fn (Surat $record) => in_array(strtolower($record->status), ['approved', 'disetujui', 'selesai'])),

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
