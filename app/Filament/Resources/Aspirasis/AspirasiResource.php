<?php

namespace App\Filament\Resources\Aspirasis;

use App\Filament\Resources\Aspirasis\Pages\CreateAspirasi;
use App\Filament\Resources\Aspirasis\Pages\EditAspirasi;
use App\Filament\Resources\Aspirasis\Pages\ListAspirasis;
use App\Filament\Resources\Aspirasis\Pages\ViewAspirasi;
use App\Filament\Resources\Aspirasis\Schemas\AspirasiForm;
use App\Filament\Resources\Aspirasis\Schemas\AspirasiInfolist;
use App\Filament\Resources\Aspirasis\Tables\AspirasisTable;
use App\Models\Aspirasi;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AspirasiResource extends Resource
{
    protected static ?string $model = Aspirasi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Layanan Lingkungan';

    protected static ?string $navigationLabel = 'Aspirasi Warga';

    protected static ?string $modelLabel = 'Aspirasi';

    protected static ?string $pluralModelLabel = 'Aspirasi Warga';

    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['super_admin', 'admin_rt', 'sekretaris']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        $query = Aspirasi::query()->whereDoesntHave('responAspirasi');
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

    protected static ?string $recordTitleAttribute = 'Isi_Aspirasi';

    public static function form(Schema $schema): Schema
    {
        return AspirasiForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AspirasiInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AspirasisTable::configure($table);
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
            'index' => ListAspirasis::route('/'),
            'create' => CreateAspirasi::route('/create'),
            'view' => ViewAspirasi::route('/{record}'),
            'edit' => EditAspirasi::route('/{record}/edit'),
        ];
    }
}
