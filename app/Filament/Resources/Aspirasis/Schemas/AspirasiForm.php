<?php

namespace App\Filament\Resources\Aspirasis\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AspirasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('NIK'),
                Textarea::make('Isi_Aspirasi')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
