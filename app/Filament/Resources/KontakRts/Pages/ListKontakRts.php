<?php

namespace App\Filament\Resources\KontakRts\Pages;

use App\Filament\Resources\KontakRts\KontakRtResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKontakRts extends ListRecords
{
    protected static string $resource = KontakRtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Kontak / Hotline Baru')
                ->icon('heroicon-m-plus-circle')
                ->slideOver(),
        ];
    }
}
