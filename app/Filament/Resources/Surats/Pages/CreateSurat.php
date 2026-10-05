<?php

namespace App\Filament\Resources\Surats\Pages;

use App\Filament\Resources\Surats\SuratResource;
use App\Models\Approval;
use App\Models\Surat;
use Filament\Resources\Pages\CreateRecord;

class CreateSurat extends CreateRecord
{
    protected static string $resource = SuratResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        if ($record->status === 'approved' && empty($record->nomor_surat)) {
            $record->update([
                'nomor_surat' => Surat::generateNomorSurat($record->surat_id, $record->warga?->ID_RT),
            ]);
        }

        Approval::firstOrCreate(
            ['tabel_sumber' => 'SURAT', 'id_referensi' => (string) $record->surat_id],
            [
                'status' => $record->status === 'approved' ? 'approved' : 'Menunggu',
                'catatan' => 'Permohonan surat: ' . $record->Jenis_surat,
                'Admin_ID' => auth()->id(),
                'tgl_approval' => $record->status === 'approved' ? now() : null,
            ]
        );
    }
}
