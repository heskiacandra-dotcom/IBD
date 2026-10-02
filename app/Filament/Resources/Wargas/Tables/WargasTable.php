<?php

namespace App\Filament\Resources\Wargas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WargasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('NIK')
                    ->label('NIK')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('Nama_Lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('no_telp')
                    ->label('No. WhatsApp')
                    ->searchable()
                    ->icon('heroicon-m-phone'),
                TextColumn::make('ID_KK')
                    ->label('No. Kartu Keluarga')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('ID_RT')
                    ->label('RT')
                    ->formatStateUsing(fn ($state) => 'RT 0' . $state)
                    ->sortable(),
                TextColumn::make('tgl_lahir')
                    ->label('Tgl Lahir')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y, H:i \W\I\B')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('ID_RT')
                    ->label('Filter Wilayah RT')
                    ->options([
                        1 => 'RT 01',
                        2 => 'RT 02',
                        3 => 'RT 03',
                        4 => 'RT 04',
                        5 => 'RT 05',
                    ]),
            ])
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
