<?php

namespace App\Filament\Resources\Surats\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SuratForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('NIK')
                    ->required(),
                TextInput::make('Jenis_surat')
                    ->required(),
                Textarea::make('perihal')
                    ->required()
                    ->columnSpanFull(),
                DatePicker::make('tgl_pengajuan')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('Menunggu'),
            ]);
    }
}
