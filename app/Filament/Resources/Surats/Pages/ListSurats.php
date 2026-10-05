<?php

namespace App\Filament\Resources\Surats\Pages;

use App\Filament\Resources\Surats\SuratResource;
use App\Filament\Resources\Surats\Widgets\SuratOverviewWidget;
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
            CreateAction::make()
                ->label('Buat Pengantar Surat')
                ->icon('heroicon-m-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SuratOverviewWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Pengajuan')
                ->icon('heroicon-m-inbox-stack')
                ->badge(fn () => $this->getModel()::count())
                ->badgeColor('gray'),
            'pending' => Tab::make('Menunggu Tindakan')
                ->icon('heroicon-m-clock')
                ->badge(fn () => $this->getModel()::whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['pending', 'menunggu'])->count() ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['pending', 'menunggu'])),
            'approved' => Tab::make('Disetujui / Terbit')
                ->icon('heroicon-m-check-badge')
                ->badge(fn () => $this->getModel()::whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['approved', 'disetujui', 'selesai'])->count() ?: null)
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['approved', 'disetujui', 'selesai'])),
            'rejected' => Tab::make('Ditolak')
                ->icon('heroicon-m-x-circle')
                ->badge(fn () => $this->getModel()::whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['rejected', 'ditolak'])->count() ?: null)
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(status)'), ['rejected', 'ditolak'])),
        ];
    }
}
