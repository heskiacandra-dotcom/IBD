<?php

namespace App\Filament\Resources\KartuKeluargas;

use App\Filament\Resources\KartuKeluargas\Pages\ListKartuKeluargas;
use App\Filament\Resources\KartuKeluargas\Tables\KartuKeluargasTable;
use App\Models\KartuKeluarga;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KartuKeluargaResource extends Resource
{
    protected static ?string $model = KartuKeluarga::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|UnitEnum|null $navigationGroup = 'Kependudukan';

    protected static ?string $navigationLabel = 'Kartu Keluarga (KK)';

    protected static ?string $modelLabel = 'Kartu Keluarga';

    protected static ?string $pluralModelLabel = 'Kartu Keluarga (KK)';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_rt', 'sekretaris']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function table(Table $table): Table
    {
        return KartuKeluargasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKartuKeluargas::route('/'),
        ];
    }
}
