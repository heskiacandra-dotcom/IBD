<?php

namespace App\Filament\Resources\Pengeluarans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PengeluaransTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Pengeluaran_id')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => '#OUT-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->sortable(),
                TextColumn::make('Kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'keamanan' => 'warning',
                        'kebersihan' => 'success',
                        'perbaikan fasum', 'perbaikan' => 'danger',
                        'sosial' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('Keterangan')
                    ->label('Keterangan / Keperluan')
                    ->searchable()
                    ->limit(45),
                TextColumn::make('Jumlah_Bayar')
                    ->label('Nominal Keluar')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.'))
                    ->weight('bold')
                    ->color('danger')
                    ->sortable(),
                TextColumn::make('Tgl_Bayar')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('Metode_Bayar')
                    ->label('Metode')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
            ])
            ->defaultSort('Tgl_Bayar', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('Kategori')
                    ->label('Filter Kategori')
                    ->options([
                        'Keamanan' => 'Keamanan',
                        'Kebersihan' => 'Kebersihan',
                        'Perbaikan Fasum' => 'Perbaikan Fasum',
                        'Sosial' => 'Sosial',
                        'Operasional' => 'Operasional',
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
