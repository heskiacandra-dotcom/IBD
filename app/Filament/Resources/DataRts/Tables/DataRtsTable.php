<?php

namespace App\Filament\Resources\DataRts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DataRtsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ID_RT')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('nomor_rt')
                    ->label('Unit RT')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($record) => 'RT ' . $record->nomor_rt . ' / RW ' . $record->nomor_rw)
                    ->searchable(),
                TextColumn::make('nama_ketua_rt')
                    ->label('Ketua RT')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('kelurahan')
                    ->label('Kelurahan / Kecamatan')
                    ->formatStateUsing(fn ($record) => $record->kelurahan . ', ' . $record->kecamatan)
                    ->searchable(),
                TextColumn::make('kota')
                    ->label('Kota / Kabupaten')
                    ->searchable(),
                TextColumn::make('warga_count')
                    ->label('Populasi Warga')
                    ->counts('warga')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => $state . ' Jiwa')
                    ->sortable(),
            ])
            ->defaultSort('ID_RT', 'asc')
            ->recordActions([
                ViewAction::make()->slideOver(),
                EditAction::make()->slideOver(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => (auth('web')->user()?->role ?? auth()->user()?->role) === 'super_admin'),
                ]),
            ]);
    }
}
