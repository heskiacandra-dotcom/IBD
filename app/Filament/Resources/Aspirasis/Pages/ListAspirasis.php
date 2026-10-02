<?php

namespace App\Filament\Resources\Aspirasis\Pages;

use App\Filament\Resources\Aspirasis\AspirasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAspirasis extends ListRecords
{
    protected static string $resource = AspirasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Aspirasi'),
            'pending' => Tab::make('Menunggu Tanggapan')
                ->badge(fn () => $this->getModel()::whereDoesntHave('responAspirasi')->count() ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDoesntHave('responAspirasi')),
            'answered' => Tab::make('Sudah Dijawab')
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('responAspirasi')),
        ];
    }
}
