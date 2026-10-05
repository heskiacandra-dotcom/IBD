<?php

namespace App\Filament\Resources\KartuKeluargas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class KartuKeluargaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('ID_KK')
                    ->label('Nomor Kartu Keluarga')
                    ->copyable(),

                TextEntry::make('Alamat')
                    ->label('Alamat Domisili')
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('Terdaftar Sejak')
                    ->dateTime('d M Y, H:i \W\I\B')
                    ->placeholder('-'),
            ]);
    }
}
