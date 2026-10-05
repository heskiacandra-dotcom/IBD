<?php

namespace App\Filament\Resources\Pemasukans\Pages;

use App\Filament\Resources\Pemasukans\PemasukanResource;
use App\Models\Warga;
use Filament\Resources\Pages\CreateRecord;

class CreatePemasukan extends CreateRecord
{
    protected static string $resource = PemasukanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['ID_RT'])) {
            $userRt = auth('web')->user()?->ID_RT ?? auth()->user()?->ID_RT;
            if ($userRt) {
                $data['ID_RT'] = $userRt;
            } elseif (!empty($data['ID_KK'])) {
                $warga = Warga::where('ID_KK', $data['ID_KK'])->first();
                $data['ID_RT'] = $warga?->ID_RT ?? 1;
            } else {
                $data['ID_RT'] = 1;
            }
        }

        return $data;
    }
}
