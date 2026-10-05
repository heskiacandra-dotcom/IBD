<?php

namespace App\Filament\Resources\DataRts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DataRtInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('ID_RT')
                    ->label('ID RT'),

                TextEntry::make('nomor_rt')
                    ->label('Unit RT / RW')
                    ->formatStateUsing(fn ($record) => "RT {$record->nomor_rt} / RW {$record->nomor_rw}")
                    ->badge()
                    ->color('primary'),

                TextEntry::make('nama_ketua_rt')
                    ->label('Ketua RT')
                    ->weight('bold'),

                TextEntry::make('kelurahan')
                    ->label('Kelurahan'),

                TextEntry::make('kecamatan')
                    ->label('Kecamatan'),

                TextEntry::make('kota')
                    ->label('Kota / Kabupaten'),
            ]);
    }
}
