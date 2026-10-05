<?php

namespace App\Filament\Resources\KontakRts\Tables;

use App\Models\KontakRt;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KontakRtsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('urutan')
                    ->label('#')
                    ->sortable()
                    ->width('50px'),

                TextColumn::make('role')
                    ->label('Jabatan / Layanan')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nama_kontak')
                    ->label('Nama Petugas / PIC')
                    ->searchable(),

                TextColumn::make('nomor_telepon')
                    ->label('Nomor Telepon / WA')
                    ->icon('heroicon-m-phone')
                    ->fontFamily('mono')
                    ->copyable()
                    ->searchable(),

                IconColumn::make('is_whatsapp')
                    ->label('WhatsApp')
                    ->boolean()
                    ->trueIcon('heroicon-s-chat-bubble-left-right')
                    ->trueColor('success')
                    ->falseIcon('heroicon-m-phone-x-mark')
                    ->falseColor('gray'),

                TextColumn::make('lokasi_tampilan')
                    ->label('Penempatan')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'footer_darurat' => 'Darurat 24 Jam',
                        'sekretariat' => 'Sekretariat RT',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'footer_darurat' => 'rose',
                        'sekretariat' => 'emerald',
                        default => 'gray',
                    }),

                IconColumn::make('is_aktif')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('urutan', 'asc')
            ->recordActionsColumnLabel('Aksi')
            ->recordActionsAlignment('end')
            ->recordActions([
                Action::make('test_whatsapp')
                    ->label('Chat WA')
                    ->tooltip('Buka obrolan WhatsApp dengan nomor ini')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color('success')
                    ->button()
                    ->size('sm')
                    ->url(fn (KontakRt $record) => $record->whatsapp_url)
                    ->openUrlInNewTab()
                    ->visible(fn (KontakRt $record) => $record->is_whatsapp && filled($record->whatsapp_url)),

                EditAction::make()
                    ->slideOver()
                    ->label('Edit Nomor & Info')
                    ->icon('heroicon-m-pencil-square'),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Kontak RT Tersimpan')
            ->emptyStateDescription('Tambahkan nomor kontak pengurus dan posko darurat RT agar warga dapat menghubungi secara langsung melalui telepon dan WhatsApp.')
            ->emptyStateIcon('heroicon-o-phone');
    }
}
