<?php

namespace App\Filament\Resources\Pengeluarans;

use App\Filament\Resources\Pengeluarans\Pages\CreatePengeluaran;
use App\Filament\Resources\Pengeluarans\Pages\EditPengeluaran;
use App\Filament\Resources\Pengeluarans\Pages\ListPengeluarans;
use App\Filament\Resources\Pengeluarans\Pages\ViewPengeluaran;
use App\Filament\Resources\Pengeluarans\Schemas\PengeluaranForm;
use App\Filament\Resources\Pengeluarans\Schemas\PengeluaranInfolist;
use App\Filament\Resources\Pengeluarans\Tables\PengeluaransTable;
use App\Models\Pengeluaran;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PengeluaranResource extends Resource
{
    protected static ?string $model = Pengeluaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingDown;

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan RT';

    protected static ?string $navigationLabel = 'Pengeluaran Kas';

    protected static ?string $modelLabel = 'Pengeluaran';

    protected static ?string $pluralModelLabel = 'Pengeluaran Kas';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        $role = auth('web')->user()?->role ?? auth()->user()?->role;
        return in_array($role, ['super_admin', 'admin_rt', 'bendahara']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        $role = auth('web')->user()?->role ?? auth()->user()?->role;
        return in_array($role, ['super_admin', 'admin_rt', 'bendahara']);
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $role = auth('web')->user()?->role ?? auth()->user()?->role;
        return in_array($role, ['super_admin', 'admin_rt', 'bendahara']);
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        $role = auth('web')->user()?->role ?? auth()->user()?->role;
        return in_array($role, ['super_admin', 'admin_rt', 'bendahara']);
    }

    protected static ?string $recordTitleAttribute = 'kategori';

    public static function form(Schema $schema): Schema
    {
        return PengeluaranForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PengeluaranInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengeluaransTable::configure($table);
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
            'index' => ListPengeluarans::route('/'),
            'create' => CreatePengeluaran::route('/create'),
            'view' => ViewPengeluaran::route('/{record}'),
            'edit' => EditPengeluaran::route('/{record}/edit'),
        ];
    }
}
