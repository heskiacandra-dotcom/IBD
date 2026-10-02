<?php

namespace App\Filament\Resources\KartuKeluargas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KartuKeluargasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ID_KK')
                    ->label('Nomor KK')
                    ->weight('bold')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('Alamat')
                    ->label('Alamat Domisili')
                    ->limit(55)
                    ->searchable(),
                TextColumn::make('warga_count')
                    ->label('Jumlah Anggota')
                    ->counts('warga')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => $state . ' Jiwa')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Terdaftar Sejak')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('ID_KK', 'asc')
            ->recordActions([
                ViewAction::make()->slideOver(),
                EditAction::make()->slideOver(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
