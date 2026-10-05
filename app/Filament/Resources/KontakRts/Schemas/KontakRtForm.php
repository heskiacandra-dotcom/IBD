<?php

namespace App\Filament\Resources\KontakRts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class KontakRtForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('role')
                    ->label('Jabatan / Layanan / Hotline')
                    ->placeholder('Contoh: Ketua RT 05, Pos Ronda & Keamanan, Ambulans')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),

                TextInput::make('nama_kontak')
                    ->label('Nama Petugas / PIC')
                    ->placeholder('Contoh: Bpk. Sukardi Hartono / Piket Regu Sekuriti')
                    ->required()
                    ->maxLength(150),

                TextInput::make('nomor_telepon')
                    ->label('Nomor Telepon / WhatsApp')
                    ->placeholder('Contoh: 0812-3456-7890 atau 112')
                    ->required()
                    ->maxLength(50)
                    ->helperText('Dapat dimasukkan format 08xxxx, +62xxxx, atau nomor darurat pendek seperti 112.'),

                Toggle::make('is_whatsapp')
                    ->label('Tersambung ke WhatsApp')
                    ->default(true)
                    ->helperText('Bila aktif, tombol di portal warga akan langsung membuka obrolan chat WhatsApp (wa.me).'),

                Toggle::make('is_aktif')
                    ->label('Status Aktif di Portal')
                    ->default(true)
                    ->helperText('Tampilkan kontak ini pada portal warga.'),

                Select::make('lokasi_tampilan')
                    ->label('Penempatan di Portal Warga')
                    ->options([
                        'footer_darurat' => 'Pusat Kontak Cepat & Darurat (Grid Kartu Siaga)',
                        'sekretariat' => 'Sekretariat Lingkungan (Tombol Hubungi Sekretariat)',
                    ])
                    ->default('footer_darurat')
                    ->required(),

                TextInput::make('urutan')
                    ->label('Urutan Tampilan (1, 2, 3...)')
                    ->numeric()
                    ->default(1),

                Textarea::make('pesan_wa')
                    ->label('Pesan Awal Otomatis WhatsApp (Opsional)')
                    ->placeholder('Contoh: Halo Pengurus RT 05, saya warga ingin bertanya perihal layanan lingkungan.')
                    ->rows(3)
                    ->maxLength(255)
                    ->helperText('Teks pembuka yang otomatis terisi ketika warga menekan nomor WhatsApp ini.')
                    ->columnSpanFull(),
            ]);
    }
}
