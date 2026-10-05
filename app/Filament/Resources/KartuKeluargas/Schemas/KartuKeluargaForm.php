<?php

namespace App\Filament\Resources\KartuKeluargas\Schemas;

use App\Models\KartuKeluarga;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KartuKeluargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ID_KK')
                    ->label('Nomor Kartu Keluarga (No. KK)')
                    ->required()
                    ->length(16)
                    ->regex('/^[0-9]{16}$/')
                    ->validationMessages([
                        'regex' => 'Nomor KK harus berupa 16 digit angka.',
                        'unique' => 'Nomor KK ini sudah terdaftar dalam sistem.',
                    ])
                    ->unique(KartuKeluarga::class, 'ID_KK', ignoreRecord: true)
                    ->placeholder('16 Digit Nomor KK...')
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->dehydrated()
                    ->columnSpanFull(),

                Textarea::make('Alamat')
                    ->label('Alamat Domisili Keluarga')
                    ->required()
                    ->placeholder('Contoh: Jl. Merpati No. 12 RT 05 / RW 01, Kelurahan Harmoni')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
