<?php

namespace App\Filament\Resources\Wargas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WargaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('NIK'),
                TextEntry::make('ID_KK')
                    ->placeholder('-'),
                TextEntry::make('ID_RT')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('Nama_Lengkap'),
                TextEntry::make('no_telp')
                    ->placeholder('-'),
                TextEntry::make('Username')
                    ->placeholder('-'),
                TextEntry::make('Password_hash')
                    ->placeholder('-'),
                TextEntry::make('tgl_lahir')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('role'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
