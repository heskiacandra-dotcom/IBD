<?php

namespace App\Filament\Resources\Aspirasis\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AspirasiInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Pengirim Aspirasi')
                    ->description('Data kependudukan warga yang menyampaikan aspirasi atau aduan')
                    ->schema([
                        TextEntry::make('warga.Nama_Lengkap')
                            ->label('Nama Lengkap Warga')
                            ->weight('bold'),

                        TextEntry::make('NIK')
                            ->label('NIK e-KTP')
                            ->copyable(),

                        TextEntry::make('warga.no_telp')
                            ->label('Nomor WhatsApp')
                            ->icon('heroicon-m-phone')
                            ->placeholder('-'),

                        TextEntry::make('warga.dataRt.nomor_rt')
                            ->label('Unit RT')
                            ->formatStateUsing(fn ($state) => $state ? 'RT ' . str_pad($state, 2, '0', STR_PAD_LEFT) : 'RT 05')
                            ->badge()
                            ->color('gray'),
                    ])
                    ->columns(2),

                Section::make('Isi Aspirasi & Aduan Warga')
                    ->schema([
                        TextEntry::make('id_aspirasi')
                            ->label('Kode Registrasi')
                            ->formatStateUsing(fn ($state) => '#ASP-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('created_at')
                            ->label('Waktu Pengiriman')
                            ->dateTime('d F Y, H:i \W\I\B'),

                        TextEntry::make('Isi_Aspirasi')
                            ->label('Pesan Aspirasi Lengkap')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Tanggapan Resmi Pengurus RT')
                    ->description('Jawaban, konfirmasi, atau tindak lanjut pengurus RT terhadap aspirasi ini')
                    ->schema([
                        TextEntry::make('responAspirasi.isi_respon')
                            ->label('Status Respon')
                            ->badge()
                            ->color(fn ($state) => filled($state) ? 'success' : 'warning')
                            ->formatStateUsing(fn ($state) => filled($state) ? 'Sudah Ditanggapi' : 'Menunggu Tanggapan'),

                        TextEntry::make('responAspirasi.nama_responden')
                            ->label('Nama Penjawab / Jabatan')
                            ->placeholder('-'),

                        TextEntry::make('responAspirasi.tanggal_respon')
                            ->label('Waktu Tanggapan')
                            ->dateTime('d F Y, H:i \W\I\B')
                            ->placeholder('-'),

                        TextEntry::make('responAspirasi.isi_respon')
                            ->label('Pernyataan / Klarifikasi Resmi')
                            ->placeholder('Belum ada tanggapan resmi dari pengurus RT. Klik tombol "Tanggapi" pada tabel untuk mengirim respon.')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}

