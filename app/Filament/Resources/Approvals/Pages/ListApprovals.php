<?php

namespace App\Filament\Resources\Approvals\Pages;

use App\Filament\Resources\Approvals\ApprovalResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListApprovals extends ListRecords
{
    protected static string $resource = ApprovalResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Tiket'),
            'pending' => Tab::make('Menunggu Tindakan')
                ->badge(fn () => $this->getModel()::whereIn('status', ['Menunggu', 'Pending', 'menunggu', 'pending'])->count() ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['Menunggu', 'Pending', 'menunggu', 'pending'])),
            'approved' => Tab::make('Disetujui')
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['Disetujui', 'approved', 'disetujui'])),
            'rejected' => Tab::make('Ditolak')
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['Ditolak', 'rejected', 'ditolak'])),
        ];
    }
}
