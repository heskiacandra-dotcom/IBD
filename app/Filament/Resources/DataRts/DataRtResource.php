<?php

namespace App\Filament\Resources\DataRts;

use App\Filament\Resources\DataRts\Pages\ListDataRts;
use App\Filament\Resources\DataRts\Tables\DataRtsTable;
use App\Models\DataRt;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DataRtResource extends Resource
{
    protected static ?string $model = DataRt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem & Wilayah';

    protected static ?string $navigationLabel = 'Kelola Data RT';

    protected static ?string $modelLabel = 'Data RT';

    protected static ?string $pluralModelLabel = 'Kelola Data RT';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'super_admin';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function table(Table $table): Table
    {
        return DataRtsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDataRts::route('/'),
        ];
    }
}
