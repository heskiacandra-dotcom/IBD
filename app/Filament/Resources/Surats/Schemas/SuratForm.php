<?php

namespace App\Filament\Resources\Surats\Schemas;

use App\Models\Warga;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SuratForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pemohon & Jenis Surat')
                    ->description('Pilih warga pemohon dan jenis surat pengantar yang diajukan')
                    ->icon('heroicon-m-document-plus')
                    ->schema([
                        Select::make('NIK')
                            ->label('Warga Pemohon')
                            ->prefixIcon('heroicon-m-user')
                            ->options(function () {
                                $query = Warga::query();
                                $user = auth('web')->user() ?? auth()->user();
                                if ($user && $user->role !== 'super_admin' && $user->ID_RT) {
                                    $query->where('ID_RT', $user->ID_RT);
                                }
                                return $query->get()->mapWithKeys(function ($warga) {
                                    return [$warga->NIK => "{$warga->Nama_Lengkap} (NIK: {$warga->NIK})"];
                                })->toArray();
                            })
                            ->searchable()
                            ->required(),

                        Select::make('Jenis_surat')
                            ->label('Jenis Surat Pengantar')
                            ->prefixIcon('heroicon-m-document-text')
                            ->options([
                                'Surat Pengantar Pembuatan KTP & KK Baru' => 'Pengantar Pembuatan KTP & KK Baru',
                                'Surat Pengantar SKCK Kepolisian' => 'Pengantar SKCK Kepolisian',
                                'Surat Keterangan Domisili Tempat Tinggal' => 'Keterangan Domisili Tempat Tinggal',
                                'Surat Keterangan Usaha (SKU) Mikro Lingkungan' => 'Keterangan Usaha (SKU) Mikro Lingkungan',
                                'Surat Keterangan Izin Keramaian Lingkungan' => 'Keterangan Izin Keramaian Lingkungan',
                            ])
                            ->required(),

                        TextInput::make('nomor_surat')
                            ->label('Nomor Registrasi Surat')
                            ->prefixIcon('heroicon-m-identification')
                            ->placeholder('Otomatis diterbitkan saat permohonan disetujui...')
                            ->helperText('Nomor resmi pengantar RT (dibuat otomatis jika dikosongkan).')
                            ->maxLength(100),

                        DatePicker::make('tgl_pengajuan')
                            ->label('Tanggal Pengajuan')
                            ->prefixIcon('heroicon-m-calendar')
                            ->default(now())
                            ->required(),

                        Select::make('status')
                            ->label('Status Pengajuan')
                            ->prefixIcon('heroicon-m-flag')
                            ->options([
                                'pending' => 'Menunggu Tindakan (Pending)',
                                'approved' => 'Disetujui / Terbit',
                                'rejected' => 'Ditolak',
                            ])
                            ->default('pending')
                            ->required(),

                        Textarea::make('perihal')
                            ->label('Keperluan / Perihal Surat')
                            ->placeholder('Contoh: Untuk persyaratan pendaftaran pekerjaan atau perbankan...')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
