<?php

namespace App\Filament\Resources\Pemasukans;

use App\Filament\Resources\Pemasukans\Pages\CreatePemasukan;
use App\Filament\Resources\Pemasukans\Pages\EditPemasukan;
use App\Filament\Resources\Pemasukans\Pages\ListPemasukans;
use App\Filament\Resources\Pemasukans\Pages\ViewPemasukan;
use App\Filament\Resources\Pemasukans\Schemas\PemasukanForm;
use App\Filament\Resources\Pemasukans\Schemas\PemasukanInfolist;
use App\Filament\Resources\Pemasukans\Tables\PemasukansTable;
use App\Models\Pemasukan;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PemasukanResource extends Resource
{
    protected static ?string $model = Pemasukan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingUp;

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan RT';

    protected static ?string $navigationLabel = 'Pemasukan Kas';

    protected static ?string $modelLabel = 'Pemasukan';

    protected static ?string $pluralModelLabel = 'Pemasukan Kas';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_rt', 'bendahara']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    protected static ?string $recordTitleAttribute = 'nama_tagihan';

    public static function form(Schema $schema): Schema
    {
        return PemasukanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PemasukanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PemasukansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPemasukans::route('/'),
            'create' => CreatePemasukan::route('/create'),
            'view' => ViewPemasukan::route('/{record}'),
            'edit' => EditPemasukan::route('/{record}/edit'),
        ];
    }
}
