<?php

namespace App\Filament\Resources\Pemasukans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PemasukanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('ID_KK')
                    ->placeholder('-'),
                TextEntry::make('Kategori'),
                TextEntry::make('ID_RT')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('nama_tagihan'),
                TextEntry::make('nominal')
                    ->numeric(),
                TextEntry::make('tgl_jatuh_tmp')
                    ->date(),
                TextEntry::make('status_tagihan'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
