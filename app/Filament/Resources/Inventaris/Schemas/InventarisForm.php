<?php

namespace App\Filament\Resources\Inventaris\Schemas;

use App\Models\DataRt;
use App\Models\Inventaris;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InventarisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('Nama_Barang')
                    ->label('Nama Barang / Fasilitas')
                    ->placeholder('Contoh: Tenda Terop RT, Kursi Lipat, Sound System')
                    ->required()
                    ->maxLength(150),

                TextInput::make('Kode_Barang')
                    ->label('Kode Barang / Aset')
                    ->placeholder('Contoh: AST-TND-01')
                    ->required()
                    ->unique(Inventaris::class, 'Kode_Barang', ignoreRecord: true)
                    ->maxLength(50),

                TextInput::make('Jumlah')
                    ->label('Jumlah Stok Fisik')
                    ->numeric()
                    ->minValue(0)
                    ->default(1)
                    ->required(),

                TextInput::make('Lokasi')
                    ->label('Lokasi Penyimpanan')
                    ->placeholder('Contoh: Balai RT, Pos Ronda, Gudang RW')
                    ->default('Balai RT')
                    ->required()
                    ->maxLength(150),

                Select::make('ID_RT')
                    ->label('Unit RT')
                    ->options(fn () => DataRt::all()->mapWithKeys(fn ($rt) => [
                        $rt->ID_RT => 'RT ' . str_pad($rt->nomor_rt ?? $rt->ID_RT, 2, '0', STR_PAD_LEFT)
                    ]))
                    ->default(fn () => auth()->user()?->ID_RT ?? 1)
                    ->visible(fn () => (auth()->user()?->role ?? '') === 'super_admin'),

                Hidden::make('ID_RT')
                    ->default(fn () => auth()->user()?->ID_RT ?? 1)
                    ->visible(fn () => (auth()->user()?->role ?? '') !== 'super_admin'),
            ]);
    }
}
