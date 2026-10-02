<?php

namespace App\Filament\Resources\Surats;

use App\Filament\Resources\Surats\Pages\CreateSurat;
use App\Filament\Resources\Surats\Pages\EditSurat;
use App\Filament\Resources\Surats\Pages\ListSurats;
use App\Filament\Resources\Surats\Pages\ViewSurat;
use App\Filament\Resources\Surats\Schemas\SuratForm;
use App\Filament\Resources\Surats\Schemas\SuratInfolist;
use App\Filament\Resources\Surats\Tables\SuratsTable;
use App\Models\Surat;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SuratResource extends Resource
{
    protected static ?string $model = Surat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Layanan Lingkungan';

    protected static ?string $navigationLabel = 'Pengajuan Surat';

    protected static ?string $modelLabel = 'Surat';

    protected static ?string $pluralModelLabel = 'Pengajuan Surat';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_rt', 'sekretaris']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    protected static ?string $recordTitleAttribute = 'Jenis_surat';

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        $query = Surat::query()->whereIn('status', ['pending', 'menunggu', 'Pending']);
        if ($user && $user->role !== 'super_admin' && $user->ID_RT) {
            $query->whereHas('warga', fn ($q) => $q->where('ID_RT', $user->ID_RT));
        }
        $count = $query->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        if ($user && $user->role !== 'super_admin' && $user->ID_RT) {
            $query->whereHas('warga', function ($q) use ($user) {
                $q->where('ID_RT', $user->ID_RT);
            });
        }
        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return SuratForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SuratInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuratsTable::configure($table);
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
            'index' => ListSurats::route('/'),
            'create' => CreateSurat::route('/create'),
            'view' => ViewSurat::route('/{record}'),
            'edit' => EditSurat::route('/{record}/edit'),
        ];
    }
}
