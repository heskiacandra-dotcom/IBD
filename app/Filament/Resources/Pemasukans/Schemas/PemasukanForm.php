<?php

namespace App\Filament\Resources\Pemasukans\Schemas;

use App\Models\KartuKeluarga;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PemasukanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Warga & Tagihan')
                    ->description('Pilih kepala keluarga dan peruntukan tagihan kas RT')
                    ->schema([
                        Select::make('ID_KK')
                            ->label('Kepala Keluarga / Warga')
                            ->options(function () {
                                return KartuKeluarga::with('warga')
                                    ->get()
                                    ->mapWithKeys(function ($kk) {
                                        $warga = $kk->warga?->first();
                                        $nama = $warga?->Nama_Lengkap ?? 'Keluarga KK ' . ($kk->nomor_kk ?? $kk->ID_KK);
                                        return [$kk->ID_KK => "{$nama} (No. KK: " . ($kk->nomor_kk ?? $kk->ID_KK) . ")"];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->required(),

                        TextInput::make('nama_tagihan')
                            ->label('Nama / Uraian Tagihan')
                            ->placeholder('Contoh: Iuran Keamanan & Kebersihan Oktober 2026')
                            ->required(),

                        Select::make('Kategori')
                            ->label('Kategori Tagihan')
                            ->options([
                                'Iuran Warga' => 'Iuran Warga (Rutin Bulanan)',
                                'Sumbangan Sosial' => 'Sumbangan Sosial / Sukarela',
                                'Sewa Lapangan & Fasilitas' => 'Sewa Fasilitas RT',
                                'Lain-lain' => 'Pemasukan Lain-lain',
                            ])
                            ->default('Iuran Warga')
                            ->required(),

                        DatePicker::make('tgl_jatuh_tmp')
                            ->label('Tanggal Jatuh Tempo')
                            ->default(now()->endOfMonth())
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Nominal & Status Pembayaran')
                    ->description('Rincian jumlah iuran dan status pelunasan kas')
                    ->schema([
                        TextInput::make('nominal')
                            ->label('Nominal Tagihan')
                            ->prefix('Rp')
                            ->numeric()
                            ->default(75000)
                            ->required(),

                        Select::make('status_tagihan')
                            ->label('Status Pembayaran')
                            ->options([
                                'Belum Lunas' => 'Belum Lunas',
                                'Lunas' => 'Lunas',
                                'pending_approval' => 'Menunggu Verifikasi',
                            ])
                            ->default('Belum Lunas')
                            ->required(),

                        Select::make('metode_pembayaran')
                            ->label('Metode Pembayaran')
                            ->options([
                                'tunai' => 'Kas Tunai (Langsung ke Bendahara)',
                                'bank_transfer' => 'Transfer Rekening Bank',
                                'qris' => 'QRIS (Payment Gateway)',
                            ])
                            ->default('tunai'),

                        FileUpload::make('bukti_bayar')
                            ->label('Lampiran Bukti Transfer (Opsional)')
                            ->image()
                            ->disk('public')
                            ->directory('uploads/bukti')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}

