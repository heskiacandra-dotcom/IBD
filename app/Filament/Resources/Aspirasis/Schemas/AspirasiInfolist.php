<?php

namespace App\Filament\Resources\Aspirasis\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AspirasiInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('NIK')
                    ->placeholder('-'),
                TextEntry::make('Isi_Aspirasi')
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
