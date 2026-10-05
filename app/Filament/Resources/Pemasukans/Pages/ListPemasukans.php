<?php

namespace App\Filament\Resources\Pemasukans\Pages;

use App\Filament\Resources\Pemasukans\PemasukanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPemasukans extends ListRecords
{
    protected static string $resource = PemasukanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Catat Tagihan Baru')
                ->icon('heroicon-m-plus-circle'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Tagihan')
                ->badge(fn () => $this->getModel()::count())
                ->badgeColor('gray'),
            'pending' => Tab::make('Menunggu Verifikasi')
                ->badge(fn () => $this->getModel()::whereIn('status_tagihan', ['pending_approval', 'menunggu verifikasi', 'menunggu', 'pending'])->count() ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status_tagihan', ['pending_approval', 'menunggu verifikasi', 'menunggu', 'pending'])),
            'lunas' => Tab::make('Lunas')
                ->badge(fn () => $this->getModel()::whereRaw("LOWER(status_tagihan) = 'lunas'")->count() ?: null)
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereRaw("LOWER(status_tagihan) = 'lunas'")),
            'belum_lunas' => Tab::make('Belum Lunas')
                ->badge(fn () => $this->getModel()::whereIn('status_tagihan', ['belum_lunas', 'Belum Lunas'])->count() ?: null)
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status_tagihan', ['belum_lunas', 'Belum Lunas'])),
        ];
    }
}
