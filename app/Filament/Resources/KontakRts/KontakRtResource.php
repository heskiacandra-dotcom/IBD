<?php

namespace App\Filament\Resources\KontakRts;

use App\Filament\Resources\KontakRts\Pages\ListKontakRts;
use App\Filament\Resources\KontakRts\Schemas\KontakRtForm;
use App\Filament\Resources\KontakRts\Tables\KontakRtsTable;
use App\Models\KontakRt;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KontakRtResource extends Resource
{
    protected static ?string $model = KontakRt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem & Wilayah';

    protected static ?string $navigationLabel = 'Kontak & WhatsApp RT';

    protected static ?string $modelLabel = 'Kontak RT';

    protected static ?string $pluralModelLabel = 'Kontak & WhatsApp RT';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        $role = auth('web')->user()?->role ?? auth()->user()?->role;
        return in_array($role, ['super_admin', 'admin_rt', 'sekretaris']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return KontakRtForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KontakRtsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKontakRts::route('/'),
        ];
    }
}
