<?php

namespace App\Filament\Resources\Pengeluarans\Pages;

use App\Filament\Resources\Pengeluarans\PengeluaranResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePengeluaran extends CreateRecord
{
    protected static string $resource = PengeluaranResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['ID_RT'])) {
            $data['ID_RT'] = auth('web')->user()?->ID_RT ?? auth()->user()?->ID_RT ?? 1;
        }

        return $data;
    }
}
