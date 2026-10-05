<?php

namespace App\Filament\Resources\Wargas\Pages;

use App\Filament\Resources\Wargas\WargaResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateWarga extends CreateRecord
{
    protected static string $resource = WargaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['NIK'])) {
            $data['NIK'] = (string) $data['NIK'];
        }

        if (!empty($data['ID_KK'])) {
            $data['ID_KK'] = (string) $data['ID_KK'];
        }

        // Jika Username dikosongkan, otomatis gunakan NIK
        if (empty($data['Username']) && !empty($data['NIK'])) {
            $data['Username'] = (string) $data['NIK'];
        }

        // Jika Password_hash belum terisi, default diatur ke hash '12345678'
        if (empty($data['Password_hash'])) {
            $data['Password_hash'] = Hash::make('12345678');
        }

        return $data;
    }
}
