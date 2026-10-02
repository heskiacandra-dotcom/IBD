<?php

namespace App\Filament\Resources\Surats\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SuratInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('NIK'),
                TextEntry::make('Jenis_surat'),
                TextEntry::make('perihal')
                    ->columnSpanFull(),
                TextEntry::make('tgl_pengajuan')
                    ->date(),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
