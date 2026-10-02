<?php

namespace App\Filament\Resources\Admins\Tables;

use App\Filament\Resources\Admins\AdminResource;
use App\Models\Admin;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdminsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Admin_ID')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('Username')
                    ->label('Username / Akun')
                    ->searchable()
                    ->weight('bold')
                    ->icon('heroicon-m-user-circle'),

                TextColumn::make('role')
                    ->label('Peran / Jabatan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'primary',
                        'admin_rt' => 'success',
                        'bendahara' => 'warning',
                        'sekretaris' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'super_admin' => 'Super Admin RW',
                        'admin_rt' => 'Ketua RT',
                        'bendahara' => 'Bendahara RT',
                        'sekretaris' => 'Sekretaris RT',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                TextColumn::make('ID_RT')
                    ->label('Wilayah RT')
                    ->formatStateUsing(fn ($state) => $state ? 'RT 0' . $state : 'Seluruh RW')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Didaftarkan')
                    ->dateTime('d M Y, H:i \W\I\B')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Filter Jabatan')
                    ->options(function () {
                        if (auth()->user()?->role === 'admin_rt') {
                            return [
                                'admin_rt' => 'Ketua RT',
                                'bendahara' => 'Bendahara RT',
                                'sekretaris' => 'Sekretaris RT',
                            ];
                        }
                        return [
                            'super_admin' => 'Super Admin',
                            'admin_rt' => 'Ketua RT',
                            'bendahara' => 'Bendahara RT',
                            'sekretaris' => 'Sekretaris RT',
                        ];
                    }),
            ])
            ->recordActions([
                ViewAction::make()->slideOver(),
                EditAction::make()
                    ->slideOver()
                    ->visible(fn (Admin $record) => AdminResource::canEdit($record)),
                DeleteAction::make()
                    ->visible(fn (Admin $record) => AdminResource::canDelete($record))
                    ->modalHeading('Hapus Akses Pengurus')
                    ->modalDescription(fn (Admin $record) => "Apakah Anda yakin ingin menghapus akses pengurus \"{$record->Username}\"? Akun ini tidak akan dapat login lagi."),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->role === 'super_admin'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Pengurus Terdaftar')
            ->emptyStateDescription('Ketua RT dapat mendaftarkan Sekretaris dan Bendahara untuk membantu tata kelola lingkungan RT.')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
