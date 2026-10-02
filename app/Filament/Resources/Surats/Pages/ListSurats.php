<?php

namespace App\Filament\Resources\Surats\Pages;

use App\Filament\Resources\Surats\SuratResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSurats extends ListRecords
{
    protected static string $resource = SuratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Surat'),
            'pending' => Tab::make('Menunggu Approval')
                ->badge(fn () => $this->getModel()::whereIn('status', ['pending', 'menunggu', 'Pending'])->count() ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['pending', 'menunggu', 'Pending'])),
            'approved' => Tab::make('Surat Terbit')
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['approved', 'disetujui', 'selesai', 'Disetujui'])),
            'rejected' => Tab::make('Ditolak')
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['rejected', 'ditolak', 'Ditolak'])),
        ];
    }
}
