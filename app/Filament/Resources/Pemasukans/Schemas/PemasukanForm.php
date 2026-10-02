<?php

namespace App\Filament\Resources\Pemasukans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PemasukanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ID_KK'),
                TextInput::make('Kategori')
                    ->required(),
                TextInput::make('ID_RT')
                    ->numeric(),
                TextInput::make('nama_tagihan')
                    ->required(),
                TextInput::make('nominal')
                    ->required()
                    ->numeric(),
                DatePicker::make('tgl_jatuh_tmp')
                    ->required(),
                TextInput::make('status_tagihan')
                    ->required()
                    ->default('Belum Lunas'),
            ]);
    }
}
