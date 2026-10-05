<?php

namespace App\Filament\Resources\Inventaris\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventarisInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rincian Aset & Inventaris RT')
                    ->description('Data spesifikasi barang dan ketersediaan stok fisik barang lingkungan RT')
                    ->schema([
                        TextEntry::make('Nama_Barang')
                            ->label('Nama Barang / Fasilitas')
                            ->weight('bold')
                            ->size('lg')
                            ->columnSpanFull(),

                        TextEntry::make('Kode_Barang')
                            ->label('Kode Barang / Aset')
                            ->badge()
                            ->color('primary')
                            ->copyable(),

                        TextEntry::make('Jumlah')
                            ->label('Ketersediaan Stok')
                            ->badge()
                            ->color(fn ($state) => (int) $state > 0 ? 'success' : 'danger')
                            ->formatStateUsing(fn ($state) => (int) $state > 0 ? "Tersedia ({$state} Unit)" : 'Stok Kosong (0 Unit)'),

                        TextEntry::make('Lokasi')
                            ->label('Lokasi Penyimpanan')
                            ->icon('heroicon-m-map-pin')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('dataRt.nomor_rt')
                            ->label('Unit RT')
                            ->formatStateUsing(fn ($state) => $state ? 'RT ' . str_pad($state, 2, '0', STR_PAD_LEFT) : 'RT 05')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('created_at')
                            ->label('Tanggal Registrasi')
                            ->date('d F Y')
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Pembaruan Terakhir')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}

