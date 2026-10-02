<?php

namespace App\Filament\Resources\Wargas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ID_KK'),
                TextInput::make('ID_RT')
                    ->numeric(),
                TextInput::make('Nama_Lengkap')
                    ->required(),
                TextInput::make('no_telp')
                    ->tel(),
                TextInput::make('Username'),
                TextInput::make('Password_hash'),
                DatePicker::make('tgl_lahir'),
                TextInput::make('role')
                    ->required()
                    ->default('warga'),
            ]);
    }
}
