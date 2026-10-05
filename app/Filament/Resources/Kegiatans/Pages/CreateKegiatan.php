<?php

namespace App\Filament\Resources\Kegiatans\Pages;

use App\Filament\Resources\Kegiatans\KegiatanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKegiatan extends CreateRecord
{
    protected static string $resource = KegiatanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['ID_RT'])) {
            $data['ID_RT'] = auth('web')->user()?->ID_RT ?? auth()->user()?->ID_RT ?? 1;
        }

        return $data;
    }
}
