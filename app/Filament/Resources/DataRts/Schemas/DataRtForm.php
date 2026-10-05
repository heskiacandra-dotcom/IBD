<?php

namespace App\Filament\Resources\DataRts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DataRtForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nomor_rt')
                    ->label('Nomor RT')
                    ->placeholder('Contoh: 05')
                    ->required()
                    ->maxLength(10),

                TextInput::make('nomor_rw')
                    ->label('Nomor RW')
                    ->placeholder('Contoh: 01')
                    ->default('01')
                    ->required()
                    ->maxLength(10),

                TextInput::make('nama_ketua_rt')
                    ->label('Nama Ketua RT')
                    ->placeholder('Contoh: Bambang Pamungkas')
                    ->required()
                    ->maxLength(150),

                TextInput::make('no_telp_ketua_rt')
                    ->label('No. Telepon / WhatsApp Ketua RT')
                    ->placeholder('Contoh: 0812-3456-7890')
                    ->helperText('Nomor WhatsApp resmi Ketua RT untuk kontak warga')
                    ->tel(),

                TextInput::make('no_telp_sekretariat')
                    ->label('No. Telepon / WhatsApp Sekretariat')
                    ->placeholder('Contoh: 0812-3456-7890')
                    ->helperText('Nomor hotline balai warga RT')
                    ->tel()
                    ->columnSpanFull(),

                TextInput::make('kelurahan')
                    ->label('Kelurahan / Desa')
                    ->placeholder('Contoh: Harmoni')
                    ->required()
                    ->maxLength(100),

                TextInput::make('kecamatan')
                    ->label('Kecamatan')
                    ->placeholder('Contoh: Sukajadi')
                    ->required()
                    ->maxLength(100),

                TextInput::make('kota')
                    ->label('Kota / Kabupaten')
                    ->placeholder('Contoh: Jakarta Barat')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),
            ]);
    }
}
