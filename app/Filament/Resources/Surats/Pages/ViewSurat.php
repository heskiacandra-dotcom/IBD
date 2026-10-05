<?php

namespace App\Filament\Resources\Surats\Pages;

use App\Filament\Resources\Surats\SuratResource;
use App\Models\Surat;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSurat extends ViewRecord
{
    protected static string $resource = SuratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve & Terbitkan')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->button()
                ->requiresConfirmation()
                ->modalHeading('Approve & Terbitkan Surat Pengantar')
                ->modalDescription(function () {
                    /** @var Surat $record */
                    $record = $this->getRecord();
                    $nomorPreview = $record->nomor_surat ?: Surat::generateNomorSurat($record->surat_id, $record->warga?->ID_RT);
                    $pemohon = $record->warga?->Nama_Lengkap ? " dari {$record->warga->Nama_Lengkap}" : '';
                    return "Apakah Anda yakin ingin menyetujui permohonan surat{$pemohon}? Nomor resmi: {$nomorPreview}";
                })
                ->modalSubmitActionLabel('Ya, Approve')
                ->visible(function () {
                    /** @var Surat $record */
                    $record = $this->getRecord();
                    return !in_array(strtolower(trim((string) $record->status)), ['approved', 'disetujui', 'selesai']);
                })
                ->action(function () {
                    /** @var Surat $record */
                    $record = $this->getRecord();
                    $nomorSurat = $record->approve(auth()->id());

                    Notification::make()
                        ->title('Surat Berhasil Di-Approve')
                        ->body("Surat Pengantar telah disahkan dengan Nomor Resmi: {$nomorSurat}")
                        ->success()
                        ->send();
                }),

            Action::make('reject')
                ->label('Tolak Permohonan')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->button()
                ->modalHeading('Tolak Permohonan Surat')
                ->modalDescription(function () {
                    /** @var Surat $record */
                    $record = $this->getRecord();
                    $pemohon = $record->warga?->Nama_Lengkap ? " dari {$record->warga->Nama_Lengkap}" : '';
                    return "Masukkan alasan penolakan permohonan surat{$pemohon} agar warga dapat memperbaiki data yang diperlukan.";
                })
                ->modalSubmitActionLabel('Tolak Permohonan')
                ->form([
                    \Filament\Forms\Components\Textarea::make('alasan_penolakan')
                        ->label('Alasan Penolakan')
                        ->placeholder('Contoh: Berkas persyaratan belum lengkap atau pemohon belum terdaftar dalam KK aktif.')
                        ->required()
                        ->rows(3),
                ])
                ->visible(function () {
                    /** @var Surat $record */
                    $record = $this->getRecord();
                    return in_array(strtolower(trim((string) $record->status)), ['pending', 'menunggu', '']);
                })
                ->action(function (array $data) {
                    /** @var Surat $record */
                    $record = $this->getRecord();
                    $record->reject($data['alasan_penolakan'], auth()->id());

                    Notification::make()
                        ->title('Permohonan Surat Ditolak')
                        ->body("Permohonan surat #SRT-{$record->surat_id} ditolak: {$data['alasan_penolakan']}")
                        ->danger()
                        ->send();
                }),

            Action::make('download_pdf')
                ->label('Cetak PDF')
                ->icon('heroicon-m-printer')
                ->color('info')
                ->button()
                ->url(fn () => route('surat.pdf.download', ['id' => $this->getRecord()->surat_id]))
                ->openUrlInNewTab()
                ->visible(function () {
                    /** @var Surat $record */
                    $record = $this->getRecord();
                    return in_array(strtolower(trim((string) $record->status)), ['approved', 'disetujui', 'selesai']);
                }),

            EditAction::make(),
        ];
    }
}
