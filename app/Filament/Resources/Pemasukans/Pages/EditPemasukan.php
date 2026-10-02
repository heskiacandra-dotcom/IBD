<?php

namespace App\Filament\Resources\Pemasukans\Pages;

use App\Filament\Resources\Pemasukans\PemasukanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPemasukan extends EditRecord
{
    protected static string $resource = PemasukanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
