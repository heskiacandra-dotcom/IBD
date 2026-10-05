<?php

namespace App\Filament\Resources\Aspirasis\Schemas;

use App\Models\Warga;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AspirasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Aspirasi Warga')
                    ->description('Kelola dan catat pesan aspirasi atau saran warga lingkungan RT')
                    ->schema([
                        Select::make('NIK')
                            ->label('Warga Pengirim')
                            ->options(function () {
                                return Warga::all()->mapWithKeys(function ($w) {
                                    return [$w->NIK => "{$w->Nama_Lengkap} (NIK: {$w->NIK})"];
                                })->toArray();
                            })
                            ->searchable()
                            ->required(),

                        Textarea::make('Isi_Aspirasi')
                            ->label('Isi Pesan Aspirasi / Aduan')
                            ->placeholder('Tuliskan rincian aspirasi, saran, atau aduan fasilitas lingkungan...')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
