<?php

namespace App\Filament\Resources\Approvals;

use App\Filament\Resources\Approvals\Pages\ListApprovals;
use App\Filament\Resources\Approvals\Tables\ApprovalsTable;
use App\Models\Approval;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ApprovalResource extends Resource
{
    protected static ?string $model = Approval::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Ringkasan & Approval';

    protected static ?string $navigationLabel = 'Pusat Approval';

    protected static ?string $modelLabel = 'Approval';

    protected static ?string $pluralModelLabel = 'Pusat Approval';

    protected static ?int $navigationSort = -1;

    public static function canViewAny(): bool
    {
        $role = auth('web')->user()?->role ?? auth()->user()?->role;
        return in_array($role, ['super_admin', 'admin_rt', 'bendahara', 'sekretaris']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth('web')->user() ?? auth()->user();
        $query = Approval::query()->whereIn('status', ['Menunggu', 'Pending', 'menunggu', 'pending']);
        if ($user && $user->role !== 'super_admin' && $user->ID_RT) {
            $rtId = $user->ID_RT;
            $query->where(function ($q) use ($rtId) {
                $q->whereHas('pemasukan', fn ($p) => $p->withoutGlobalScope('tenant')->where('ID_RT', $rtId))
                  ->orWhereHas('pengeluaran', fn ($o) => $o->withoutGlobalScope('tenant')->where('ID_RT', $rtId))
                  ->orWhere(function ($sub) use ($rtId) {
                      $sub->where('tabel_sumber', 'SURAT')
                          ->whereIn('id_referensi', \App\Models\Surat::whereHas('warga', fn ($w) => $w->where('ID_RT', $rtId))->pluck('surat_id'));
                  });
            });
        }
        $count = $query->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return ApprovalsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApprovals::route('/'),
        ];
    }
}
