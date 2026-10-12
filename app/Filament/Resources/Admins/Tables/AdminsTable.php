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

                TextColumn::make('email')
                    ->label('Email Google / Kontak')
                    ->icon('heroicon-m-envelope')
                    ->searchable()
                    ->copyable()
                    ->placeholder('Belum diatur'),

                TextColumn::make('google_status')
                    ->label('Login Google')
                    ->badge()
                    ->state(function (Admin $record) {
                        if (filled($record->google_id)) {
                            return 'Google Terhubung';
                        }
                        if (filled($record->email)) {
                            return 'Google Siap';
                        }
                        return 'Sandi Saja';
                    })
                    ->color(fn (string $state) => match ($state) {
                        'Google Terhubung' => 'success',
                        'Google Siap' => 'info',
                        default => 'gray',
                    })
                    ->icon(fn (string $state) => match ($state) {
                        'Google Terhubung' => 'heroicon-m-check-badge',
                        'Google Siap' => 'heroicon-m-arrow-right-end-on-rectangle',
                        default => 'heroicon-m-key',
                    }),

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
                    ->formatStateUsing(function ($state, Admin $record) {
                        if (!$state) return 'Seluruh RW';
                        $rt = $record->dataRt;
                        if ($rt) {
                            return 'RT ' . str_pad($rt->nomor_rt, 2, '0', STR_PAD_LEFT) . ' / RW ' . str_pad($rt->nomor_rw, 2, '0', STR_PAD_LEFT);
                        }
                        return 'RT ' . str_pad($state, 2, '0', STR_PAD_LEFT);
                    })
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

                SelectFilter::make('ID_RT')
                    ->label('Filter Wilayah RT')
                    ->visible(fn () => auth()->user()?->role === 'super_admin')
                    ->options(fn () => \App\Models\DataRt::all()->mapWithKeys(fn ($rt) => [
                        $rt->ID_RT => 'RT ' . str_pad($rt->nomor_rt, 2, '0', STR_PAD_LEFT) . ' / RW ' . str_pad($rt->nomor_rw, 2, '0', STR_PAD_LEFT) . ' (' . ($rt->nama_ketua_rt ?? 'Ketua RT') . ')'
                    ])),
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
