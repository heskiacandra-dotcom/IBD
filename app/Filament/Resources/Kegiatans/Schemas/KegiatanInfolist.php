<?php

namespace App\Filament\Resources\Kegiatans\Schemas;

use App\Models\Kegiatan;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KegiatanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Agenda Kegiatan')
                    ->description('Detail jadwal, status, dan perincian kegiatan warga RT')
                    ->schema([
                        TextEntry::make('nama_kegiatan')
                            ->label('Nama Kegiatan')
                            ->weight('bold')
                            ->size('lg')
                            ->columnSpanFull(),

                        TextEntry::make('kegiatan_id')
                            ->label('Kode Event')
                            ->formatStateUsing(fn ($state) => '#EVT-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('status')
                            ->label('Status Pelaksanaan')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Mendatang' => 'info',
                                'Sedang Berlangsung' => 'success',
                                'Selesai' => 'gray',
                                'Dibatalkan' => 'danger',
                                default => 'primary',
                            })
                            ->formatStateUsing(fn (string $state) => match ($state) {
                                'Sedang Berlangsung' => 'Sedang Berlangsung',
                                'Selesai' => 'Selesai Terlaksana',
                                default => $state,
                            }),

                        TextEntry::make('tgl_kegiatan')
                            ->label('Tanggal Kegiatan')
                            ->date('d F Y')
                            ->helperText(fn (Kegiatan $record) => $record->tgl_kegiatan ? \Carbon\Carbon::parse($record->tgl_kegiatan)->diffForHumans() : null),

                        TextEntry::make('lokasi')
                            ->label('Lokasi Kegiatan')
                            ->icon('heroicon-m-map-pin')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('deskripsi')
                            ->label('Rincian / Deskripsi Agenda')
                            ->placeholder('Belum ada rincian catatan kegiatan.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Informasi Administrasi')
                    ->schema([
                        TextEntry::make('dataRt.nomor_rt')
                            ->label('Unit RT')
                            ->formatStateUsing(fn ($state) => $state ? 'RT ' . str_pad($state, 2, '0', STR_PAD_LEFT) : 'RT 05')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('created_at')
                            ->label('Didaftarkan Pada')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Pembaruan Terakhir')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(3)
                    ->collapsed(),
            ]);
    }
}

