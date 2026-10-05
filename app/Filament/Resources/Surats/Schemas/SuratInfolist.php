<?php

namespace App\Filament\Resources\Surats\Schemas;

use App\Models\Surat;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SuratInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Warga Pemohon')
                    ->description('Data kependudukan warga yang mengajukan surat pengantar')
                    ->icon('heroicon-m-user-circle')
                    ->schema([
                        TextEntry::make('warga.Nama_Lengkap')
                            ->label('Nama Pemohon')
                            ->weight('bold')
                            ->icon('heroicon-m-user'),

                        TextEntry::make('NIK')
                            ->label('NIK e-KTP')
                            ->fontFamily('mono')
                            ->copyable()
                            ->icon('heroicon-m-identification'),

                        TextEntry::make('warga.no_telp')
                            ->label('Kontak WhatsApp')
                            ->placeholder('-')
                            ->icon('heroicon-m-phone')
                            ->url(fn ($state) => $state ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $state) : null, true),

                        TextEntry::make('warga.dataRt.nomor_rt')
                            ->label('Unit Wilayah')
                            ->formatStateUsing(fn ($state) => $state ? 'RT ' . str_pad($state, 2, '0', STR_PAD_LEFT) . ' / RW 01' : 'RT 05 / RW 01')
                            ->badge()
                            ->color('gray')
                            ->icon('heroicon-m-home'),

                        TextEntry::make('warga.kartuKeluarga.Alamat')
                            ->label('Alamat Domisili')
                            ->placeholder('Harmoni Residence Blok RT 05')
                            ->columnSpanFull()
                            ->icon('heroicon-m-map-pin'),
                    ])
                    ->columns(2),

                Section::make('Rincian Surat Pengantar')
                    ->description('Peruntukan pengantar resmi dari pengurus RT')
                    ->icon('heroicon-m-document-text')
                    ->schema([
                        TextEntry::make('Jenis_surat')
                            ->label('Layanan Surat')
                            ->weight('bold')
                            ->color('primary')
                            ->icon('heroicon-m-document-check')
                            ->columnSpanFull(),

                        TextEntry::make('nomor_surat')
                            ->label('Nomor Registrasi Resmi')
                            ->placeholder('Belum Diterbitkan (Menunggu Approval)')
                            ->badge(fn ($state) => filled($state))
                            ->color(fn ($state) => filled($state) ? 'info' : 'gray')
                            ->fontFamily(fn ($state) => filled($state) ? 'mono' : null)
                            ->icon(fn ($state) => filled($state) ? 'heroicon-m-check-badge' : 'heroicon-m-clock')
                            ->copyable(fn ($state) => filled($state)),

                        TextEntry::make('status')
                            ->label('Status Pengajuan')
                            ->badge()
                            ->icon(fn (string $state): string => match (strtolower($state)) {
                                'disetujui', 'selesai', 'approved' => 'heroicon-m-check-circle',
                                'menunggu', 'pending' => 'heroicon-m-clock',
                                'ditolak', 'rejected' => 'heroicon-m-x-circle',
                                default => 'heroicon-m-question-mark-circle',
                            })
                            ->color(fn (string $state): string => match (strtolower($state)) {
                                'disetujui', 'selesai', 'approved' => 'success',
                                'menunggu', 'pending' => 'warning',
                                'ditolak', 'rejected' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match (strtolower($state)) {
                                'disetujui', 'selesai', 'approved' => 'Disetujui / Terbit',
                                'menunggu', 'pending' => 'Menunggu Tindakan',
                                'ditolak', 'rejected' => 'Ditolak',
                                default => ucfirst($state),
                            }),

                        TextEntry::make('tgl_pengajuan')
                            ->label('Tanggal Permohonan')
                            ->date('d F Y')
                            ->icon('heroicon-m-calendar'),

                        TextEntry::make('created_at')
                            ->label('Waktu Masuk Sistem')
                            ->dateTime('d M Y, H:i \W\I\B')
                            ->icon('heroicon-m-clock'),

                        TextEntry::make('perihal')
                            ->label('Keperluan / Uraian Permohonan')
                            ->icon('heroicon-m-chat-bubble-bottom-center-text')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
