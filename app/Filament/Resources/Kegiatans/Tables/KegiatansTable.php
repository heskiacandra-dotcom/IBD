<?php

namespace App\Filament\Resources\Kegiatans\Tables;

use App\Models\Kegiatan;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KegiatansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kegiatan_id')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => '#EVT-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('nama_kegiatan')
                    ->label('Nama Agenda & Rincian')
                    ->weight('bold')
                    ->description(fn (Kegiatan $record) => $record->deskripsi ? \Illuminate\Support\Str::limit($record->deskripsi, 55) : null)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('tgl_kegiatan')
                    ->label('Jadwal Pelaksanaan')
                    ->date('d M Y')
                    ->description(fn (Kegiatan $record) => $record->tgl_kegiatan ? \Carbon\Carbon::parse($record->tgl_kegiatan)->diffForHumans() : null)
                    ->sortable(),

                TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->icon('heroicon-m-map-pin')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Mendatang' => 'info',
                        'Sedang Berlangsung' => 'success',
                        'Selesai' => 'gray',
                        'Dibatalkan' => 'danger',
                        default => 'primary',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'Sedang Berlangsung' => 'Berlangsung',
                        'Selesai' => 'Selesai Terlaksana',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('dataRt.nomor_rt')
                    ->label('Unit RT')
                    ->formatStateUsing(fn ($state) => $state ? 'RT ' . str_pad($state, 2, '0', STR_PAD_LEFT) : 'RT 05')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Action::make('tandai_selesai')
                    ->label('Selesai')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Agenda Selesai')
                    ->modalDescription(fn (Kegiatan $record) => "Apakah kegiatan \"{$record->nama_kegiatan}\" telah selesai dilaksanakan?")
                    ->visible(fn (Kegiatan $record) => in_array($record->status, ['Mendatang', 'Sedang Berlangsung']))
                    ->action(function (Kegiatan $record) {
                        $record->update(['status' => 'Selesai']);
                        Notification::make()
                            ->title('Status Diperbarui')
                            ->body("Kegiatan \"{$record->nama_kegiatan}\" ditandai selesai.")
                            ->success()
                            ->send();
                    }),

                ActionGroup::make([
                    Action::make('tandai_berlangsung')
                        ->label('Mulai Sekarang')
                        ->icon('heroicon-m-play')
                        ->color('info')
                        ->visible(fn (Kegiatan $record) => $record->status === 'Mendatang')
                        ->action(function (Kegiatan $record) {
                            $record->update(['status' => 'Sedang Berlangsung']);
                            Notification::make()
                                ->title('Agenda Dimulai')
                                ->body("Kegiatan \"{$record->nama_kegiatan}\" sekarang berstatus Sedang Berlangsung.")
                                ->info()
                                ->send();
                        }),

                    ViewAction::make()->slideOver()->label('Lihat Detail Agenda'),
                    EditAction::make()->slideOver()->label('Edit Data Agenda'),
                    DeleteAction::make()->label('Hapus Agenda'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
