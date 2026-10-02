<?php

namespace App\Filament\Resources\Kegiatans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KegiatanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('ID_RT')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('nama_kegiatan'),
                TextEntry::make('tgl_kegiatan')
                    ->date(),
                TextEntry::make('lokasi'),
                TextEntry::make('deskripsi')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
