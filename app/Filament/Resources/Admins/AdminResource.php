<?php

namespace App\Filament\Resources\Admins;

use App\Filament\Resources\Admins\Pages\ListAdmins;
use App\Filament\Resources\Admins\Schemas\AdminForm;
use App\Filament\Resources\Admins\Tables\AdminsTable;
use App\Models\Admin;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem & Wilayah';

    protected static ?string $navigationLabel = 'Pengurus & Admin';

    protected static ?string $modelLabel = 'Pengurus';

    protected static ?string $pluralModelLabel = 'Pengurus & Admin';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return auth()->user()?->role === 'admin_rt' ? 'Pengurus RT (Sekre & Bendahara)' : 'Pengurus & Admin';
    }

    public static function getModelLabel(): string
    {
        return auth()->user()?->role === 'admin_rt' ? 'Pengurus RT' : 'Pengurus / Admin';
    }

    public static function getPluralModelLabel(): string
    {
        return auth()->user()?->role === 'admin_rt' ? 'Pengurus RT' : 'Pengurus & Admin';
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_rt']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_rt']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        if ($user?->role === 'super_admin') {
            return true;
        }

        if ($user?->role === 'admin_rt') {
            return $record->ID_RT == $user->ID_RT;
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        if ($user?->role === 'super_admin') {
            return $record->Admin_ID !== $user->Admin_ID;
        }

        if ($user?->role === 'admin_rt') {
            return $record->ID_RT == $user->ID_RT
                && in_array($record->role, ['bendahara', 'sekretaris'])
                && $record->Admin_ID !== $user->Admin_ID;
        }

        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->role === 'admin_rt') {
            return $query->where('ID_RT', $user->ID_RT);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return AdminForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdmins::route('/'),
        ];
    }
}
