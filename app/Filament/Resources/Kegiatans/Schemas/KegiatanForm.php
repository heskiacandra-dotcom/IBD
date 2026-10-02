<?php

namespace App\Filament\Resources\Kegiatans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class KegiatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('ID_RT')
                    ->default(fn () => auth()->user()?->ID_RT ?? 1),

                TextInput::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->placeholder('Contoh: Kerja Bakti Massal Kebersihan Saluran Air')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                DatePicker::make('tgl_kegiatan')
                    ->label('Tanggal Pelaksanaan')
                    ->required()
                    ->native(false)
                    ->displayFormat('d F Y'),

                TextInput::make('lokasi')
                    ->label('Lokasi Kegiatan')
                    ->placeholder('Contoh: Lapangan Utama & Pos Ronda RT 05')
                    ->required()
                    ->maxLength(255),

                Select::make('status')
                    ->label('Status Agenda')
                    ->options([
                        'Mendatang' => 'Mendatang (Akan Datang)',
                        'Sedang Berlangsung' => 'Sedang Berlangsung (Hari Ini)',
                        'Selesai' => 'Selesai Terlaksana',
                    ])
                    ->default('Mendatang')
                    ->required(),

                Textarea::make('deskripsi')
                    ->label('Deskripsi / Rincian Kegiatan')
                    ->placeholder('Jelaskan tujuan, perlengkapan yang perlu dibawa warga, dan susunan acara...')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
