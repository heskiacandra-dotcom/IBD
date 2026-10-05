<?php

namespace App\Filament\Resources\DataRts\Pages;

use App\Filament\Resources\DataRts\DataRtResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDataRts extends ListRecords
{
    protected static string $resource = DataRtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Unit RT Baru')
                ->icon('heroicon-m-plus-circle')
                ->slideOver(),
        ];
    }
}
