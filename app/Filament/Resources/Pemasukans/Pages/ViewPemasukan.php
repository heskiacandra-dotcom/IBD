<?php

namespace App\Filament\Resources\Pemasukans\Pages;

use App\Filament\Resources\Pemasukans\PemasukanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPemasukan extends ViewRecord
{
    protected static string $resource = PemasukanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
