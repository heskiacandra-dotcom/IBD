<?php

namespace App\Filament\Resources\Pengeluarans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PengeluaranInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('Kategori'),
                TextEntry::make('Keterangan'),
                TextEntry::make('Jumlah_Bayar')
                    ->numeric(),
                TextEntry::make('Tgl_Bayar')
                    ->date(),
                TextEntry::make('Metode_Bayar'),
                TextEntry::make('Bukti_Path')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
