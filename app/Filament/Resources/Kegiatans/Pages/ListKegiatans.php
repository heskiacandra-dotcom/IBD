<?php

namespace App\Filament\Resources\Kegiatans\Pages;

use App\Filament\Resources\Kegiatans\KegiatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListKegiatans extends ListRecords
{
    protected static string $resource = KegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Buat Agenda Baru')
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Agenda')
                ->badge(fn () => $this->getModel()::count() ?: null),
            'mendatang' => Tab::make('Mendatang')
                ->badge(fn () => $this->getModel()::where('status', 'Mendatang')->count() ?: null)
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Mendatang')),
            'berlangsung' => Tab::make('Sedang Berlangsung')
                ->badge(fn () => $this->getModel()::where('status', 'Sedang Berlangsung')->count() ?: null)
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Sedang Berlangsung')),
            'selesai' => Tab::make('Selesai Terlaksana')
                ->badge(fn () => $this->getModel()::where('status', 'Selesai')->count() ?: null)
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Selesai')),
        ];
    }

}
