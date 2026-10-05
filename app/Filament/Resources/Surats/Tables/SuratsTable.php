<?php

namespace App\Filament\Resources\Surats\Tables;

use App\Models\Approval;
use App\Models\Surat;
use App\Services\WhatsAppGatewayService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
                    ->label('No. Ref')
                    ->formatStateUsing(fn ($state) => '#SRT-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->copyable()
                    ->fontFamily('mono')
                    ->color('gray')
                    ->weight('medium')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('warga.Nama_Lengkap')
                    ->label('Pemohon')
                    ->icon('heroicon-m-user')
                    ->weight('bold')
                    ->description(function (Surat $record) {
                        $rt = $record->warga?->dataRt?->nomor_rt ? 'RT ' . str_pad($record->warga->dataRt->nomor_rt, 2, '0', STR_PAD_LEFT) : 'RT 05';
                        $telp = $record->warga?->no_telp ? ' • ' . $record->warga->no_telp : '';
                        return "NIK: {$record->NIK} ({$rt}){$telp}";
                    })
                    ->searchable(['warga.Nama_Lengkap', 'NIK'])
                    ->wrap(),

                TextColumn::make('Jenis_surat')
                    ->label('Layanan & Keperluan')
                    ->icon(fn (string $state): string => match (true) {
                        str_contains(strtolower($state), 'ktp') || str_contains(strtolower($state), 'kk') => 'heroicon-m-identification',
                        str_contains(strtolower($state), 'skck') || str_contains(strtolower($state), 'kepolisian') => 'heroicon-m-shield-check',
                        str_contains(strtolower($state), 'domisili') => 'heroicon-m-home-modern',
                        str_contains(strtolower($state), 'usaha') || str_contains(strtolower($state), 'sku') => 'heroicon-m-building-storefront',
                        str_contains(strtolower($state), 'keramaian') || str_contains(strtolower($state), 'izin') => 'heroicon-m-megaphone',
                        default => 'heroicon-m-document-text',
                    })
                    ->iconColor(fn (string $state): string => match (true) {
                        str_contains(strtolower($state), 'ktp') || str_contains(strtolower($state), 'kk') => 'primary',
                        str_contains(strtolower($state), 'skck') || str_contains(strtolower($state), 'kepolisian') => 'success',
                        str_contains(strtolower($state), 'domisili') => 'info',
                        str_contains(strtolower($state), 'usaha') || str_contains(strtolower($state), 'sku') => 'warning',
                        str_contains(strtolower($state), 'keramaian') || str_contains(strtolower($state), 'izin') => 'danger',
                        default => 'gray',
                    })
                    ->weight('semibold')
                    ->formatStateUsing(function (string $state) {
                        $lower = strtolower($state);
                        if (str_contains($lower, 'ktp') || str_contains($lower, 'kk')) return 'Pengantar KTP & KK';
                        if (str_contains($lower, 'skck')) return 'Pengantar SKCK';
                        if (str_contains($lower, 'domisili') && str_contains($lower, 'usaha')) return 'Domisili Usaha';
                        if (str_contains($lower, 'domisili')) return 'Keterangan Domisili';
                        if (str_contains($lower, 'usaha') || str_contains($lower, 'sku')) return 'Keterangan Usaha (SKU)';
                        if (str_contains($lower, 'keramaian') || str_contains($lower, 'izin')) return 'Izin Keramaian';
                        return $state;
                    })
                    ->description(fn (Surat $record) => $record->perihal ? '“' . \Illuminate\Support\Str::limit($record->perihal, 55) . '”' : null)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('nomor_surat')
                    ->label('Nomor Registrasi')
                    ->formatStateUsing(fn ($state) => $state ?: 'Menunggu Terbit')
                    ->badge(fn ($state) => filled($state))
                    ->color(fn ($state) => filled($state) ? 'info' : 'gray')
                    ->fontFamily(fn ($state) => filled($state) ? 'mono' : null)
                    ->icon(fn ($state) => filled($state) ? 'heroicon-m-document-check' : 'heroicon-m-clock')
                    ->copyable(fn ($state) => filled($state))
                    ->searchable(),

                TextColumn::make('tgl_pengajuan')
                    ->label('Tanggal Masuk')
                    ->date('d M Y')
                    ->description(fn (Surat $record) => $record->tgl_pengajuan ? \Carbon\Carbon::parse($record->tgl_pengajuan)->diffForHumans() : null)
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(fn (string $state): string => match (strtolower($state)) {
                        'disetujui', 'selesai', 'approved' => 'heroicon-m-check-circle',
                        'menunggu', 'pending' => 'heroicon-m-clock',
                        'ditolak', 'rejected' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'disetujui', 'selesai', 'approved' => 'success',
                        'menunggu', 'pending' => 'warning',
                        'ditolak', 'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match (strtolower($state)) {
                        'disetujui', 'selesai', 'approved' => 'Disetujui',
                        'menunggu', 'pending' => 'Menunggu',
                        'ditolak', 'rejected' => 'Ditolak',
                        default => ucfirst($state),
                    }),
            ])
            ->defaultSort('surat_id', 'desc')
            ->recordAction('view')
            ->emptyStateHeading('Belum Ada Pengajuan Surat')
            ->emptyStateDescription('Semua permohonan surat pengantar dari warga akan tercatat dan dapat diverifikasi langsung di sini.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'pending' => 'Menunggu Tindakan (Pending)',
                        'approved' => 'Disetujui / Terbit',
                        'rejected' => 'Ditolak',
                    ])
                    ->query(function ($query, array $data) {
                        $value = $data['value'] ?? null;
                        if (!$value) {
                            return $query;
                        }
                        return match ($value) {
                            'pending' => $query->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['pending', 'menunggu']),
                            'approved' => $query->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['approved', 'disetujui', 'selesai']),
                            'rejected' => $query->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['rejected', 'ditolak']),
                            default => $query->where('status', $value),
                        };
                    }),
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
            ->recordActionsColumnLabel('Aksi')
            ->recordActionsAlignment('end')
            ->recordActions([
                Action::make('approve_terbitkan')
                    ->label('Approve')
                    ->tooltip('Setujui & Terbitkan Surat Pengantar')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->button()
                    ->size('sm')
                    ->requiresConfirmation()
                    ->modalHeading('Approve & Terbitkan Surat Pengantar')
                    ->modalDescription(function (Surat $record) {
                        $nomorPreview = $record->nomor_surat ?: Surat::generateNomorSurat($record->surat_id, $record->warga?->ID_RT);
                        $pemohon = $record->warga?->Nama_Lengkap ? " dari {$record->warga->Nama_Lengkap}" : '';
                        return "Apakah Anda yakin ingin menyetujui permohonan surat{$pemohon}? Sistem akan menerbitkan nomor resmi: {$nomorPreview}";
                    })
                    ->modalSubmitActionLabel('Ya, Approve')
                    ->visible(fn (Surat $record) => in_array(strtolower(trim((string) $record->status)), ['pending', 'menunggu', '']))
                    ->action(function (Surat $record) {
                        $nomorSurat = $record->approve(auth()->id());

                        Notification::make()
                            ->title('Surat Berhasil Di-Approve')
                            ->body("Surat Pengantar telah disahkan dengan Nomor Resmi: {$nomorSurat}")
                            ->success()
                            ->send();
                    }),

                Action::make('tolak_permohonan')
                    ->label('Tolak')
                    ->tooltip('Tolak Permohonan Surat')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->button()
                    ->size('sm')
                    ->modalHeading('Tolak Permohonan Surat')
                    ->modalDescription(function (Surat $record) {
                        $pemohon = $record->warga?->Nama_Lengkap ? " dari {$record->warga->Nama_Lengkap}" : '';
                        return "Masukkan alasan penolakan permohonan surat{$pemohon} agar warga dapat memperbaiki data yang diperlukan.";
                    })
                    ->modalSubmitActionLabel('Tolak Permohonan')
                    ->form([
                        Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->placeholder('Contoh: Berkas persyaratan belum lengkap atau pemohon belum terdaftar dalam KK aktif.')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(fn (Surat $record) => in_array(strtolower(trim((string) $record->status)), ['pending', 'menunggu', '']))
                    ->action(function (Surat $record, array $data) {
                        $record->reject($data['alasan_penolakan'], auth()->id());

                        Notification::make()
                            ->title('Permohonan Surat Ditolak')
                            ->body("Permohonan surat #SRT-{$record->surat_id} ditolak: {$data['alasan_penolakan']}")
                            ->danger()
                            ->send();
                    }),

                Action::make('download_pdf')
                    ->label('Cetak PDF')
                    ->tooltip('Cetak Berkas Surat Pengantar')
                    ->icon('heroicon-m-printer')
                    ->color('info')
                    ->button()
                    ->size('sm')
                    ->url(fn (Surat $record) => route('surat.pdf.download', ['id' => $record->surat_id]))
                    ->openUrlInNewTab()
                    ->visible(fn (Surat $record) => in_array(strtolower(trim((string) $record->status)), ['approved', 'disetujui', 'selesai'])),

                ActionGroup::make([
                    ViewAction::make()
                        ->slideOver()
                        ->label('Lihat Rincian')
                        ->icon('heroicon-m-eye')
                        ->extraModalFooterActions(function (Surat $record): array {
                            $isApproved = in_array(strtolower(trim((string) $record->status)), ['approved', 'disetujui', 'selesai']);

                            if ($isApproved) {
                                return [
                                    Action::make('drawer_download_pdf')
                                        ->label('Cetak PDF')
                                        ->icon('heroicon-m-printer')
                                        ->color('info')
                                        ->button()
                                        ->url(route('surat.pdf.download', ['id' => $record->surat_id]))
                                        ->openUrlInNewTab(),
                                ];
                            }

                            $isRejected = in_array(strtolower(trim((string) $record->status)), ['rejected', 'ditolak']);
                            if ($isRejected) {
                                return [];
                            }

                            return [
                                Action::make('drawer_approve')
                                    ->label('Approve & Terbitkan')
                                    ->icon('heroicon-m-check-circle')
                                    ->color('success')
                                    ->button()
                                    ->requiresConfirmation()
                                    ->modalHeading('Approve & Terbitkan Surat Pengantar')
                                    ->modalDescription(function () use ($record) {
                                        $nomorPreview = $record->nomor_surat ?: Surat::generateNomorSurat($record->surat_id, $record->warga?->ID_RT);
                                        $pemohon = $record->warga?->Nama_Lengkap ? " dari {$record->warga->Nama_Lengkap}" : '';
                                        return "Apakah Anda yakin ingin menyetujui permohonan surat{$pemohon}? Nomor resmi: {$nomorPreview}";
                                    })
                                    ->modalSubmitActionLabel('Ya, Approve')
                                    ->action(function () use ($record) {
                                        $nomorSurat = $record->approve(auth()->id());

                                        Notification::make()
                                            ->title('Surat Berhasil Di-Approve')
                                            ->body("Surat Pengantar telah disahkan dengan Nomor Resmi: {$nomorSurat}")
                                            ->success()
                                            ->send();
                                    }),

                                Action::make('drawer_reject')
                                    ->label('Tolak Permohonan')
                                    ->icon('heroicon-m-x-circle')
                                    ->color('danger')
                                    ->button()
                                    ->modalHeading('Tolak Permohonan Surat')
                                    ->modalDescription('Masukkan alasan penolakan agar warga pemohon mengetahui perbaikan yang diperlukan.')
                                    ->form([
                                        Textarea::make('alasan_penolakan')
                                            ->label('Alasan Penolakan')
                                            ->placeholder('Contoh: Berkas persyaratan belum lengkap atau pemohon belum terdaftar dalam KK aktif.')
                                            ->required()
                                            ->rows(3),
                                    ])
                                    ->action(function (array $data) use ($record) {
                                        $record->reject($data['alasan_penolakan'], auth()->id());

                                        Notification::make()
                                            ->title('Permohonan Surat Ditolak')
                                            ->body("Permohonan surat #SRT-{$record->surat_id} ditolak: {$data['alasan_penolakan']}")
                                            ->danger()
                                            ->send();
                                    }),
                            ];
                        }),

                    EditAction::make()
                        ->slideOver()
                        ->label('Edit Permohonan')
                        ->icon('heroicon-m-pencil-square'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->tooltip('Pilihan Lain'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
