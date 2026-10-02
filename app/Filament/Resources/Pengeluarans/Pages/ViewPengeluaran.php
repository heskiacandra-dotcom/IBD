<?php

namespace App\Filament\Resources\Pengeluarans\Pages;

use App\Filament\Resources\Pengeluarans\PengeluaranResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPengeluaran extends ViewRecord
{
    protected static string $resource = PengeluaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
