<?php

namespace App\Filament\Resources\Inventaris;

use App\Filament\Resources\Inventaris\Pages\ListInventaris;
use App\Filament\Resources\Inventaris\Tables\InventarisTable;
use App\Models\Inventaris;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InventarisResource extends Resource
{
    protected static ?string $model = Inventaris::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|UnitEnum|null $navigationGroup = 'Layanan Lingkungan';

    protected static ?string $navigationLabel = 'Inventaris Aset';

    protected static ?string $modelLabel = 'Inventaris';

    protected static ?string $pluralModelLabel = 'Inventaris Aset';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_rt', 'bendahara', 'sekretaris']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function table(Table $table): Table
    {
        return InventarisTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventaris::route('/'),
        ];
    }
}
