<?php

namespace App\Filament\Resources\Kegiatans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KegiatansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('tgl_kegiatan')
                    ->label('Tanggal Pelaksanaan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->icon('heroicon-m-map-pin')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Mendatang' => 'info',
                        'Sedang Berlangsung' => 'success',
                        'Selesai' => 'gray',
                        default => 'primary',
                    })
                    ->sortable(),

                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->toggleable(),

                TextColumn::make('dataRt.nomor_rt')
                    ->label('Unit RT')
                    ->formatStateUsing(fn ($state) => $state ? 'RT ' . str_pad($state, 2, '0', STR_PAD_LEFT) : 'RT 05')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('tgl_kegiatan', 'asc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Mendatang' => 'Mendatang',
                        'Sedang Berlangsung' => 'Sedang Berlangsung',
                        'Selesai' => 'Selesai Terlaksana',
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
