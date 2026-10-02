<?php

namespace App\Filament\Resources\Pengeluarans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PengeluaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('Kategori')
                    ->required(),
                TextInput::make('Keterangan')
                    ->required(),
                TextInput::make('Jumlah_Bayar')
                    ->required()
                    ->numeric(),
                DatePicker::make('Tgl_Bayar')
                    ->required(),
                TextInput::make('Metode_Bayar')
                    ->required()
                    ->default('Kas Tunai'),
                TextInput::make('Bukti_Path'),
            ]);
    }
}
