<?php

namespace App\Filament\Resources\Pengeluarans\Schemas;

use App\Models\DataRt;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PengeluaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rincian Belanja Kas RT')
                    ->description('Catat pengeluaran operasional dan belanja fasilitas lingkungan RT')
                    ->schema([
                        Select::make('Kategori')
                            ->label('Kategori Pengeluaran')
                            ->options([
                                'Keamanan' => 'Keamanan (Honor Ronda / Satpam / CCTV)',
                                'Kebersihan' => 'Kebersihan (Sampah / Kerja Bakti)',
                                'Perbaikan Fasum' => 'Perbaikan Fasum & Fasos',
                                'Sosial' => 'Sosial & Santunan Warga',
                                'Operasional' => 'Operasional RT / ATK',
                                'Inventaris' => 'Pengadaan Aset & Inventaris',
                                'Lainnya' => 'Pengeluaran Lainnya',
                            ])
                            ->default('Operasional')
                            ->required(),

                        DatePicker::make('Tgl_Bayar')
                            ->label('Tanggal Transaksi')
                            ->default(now())
                            ->required(),

                        TextInput::make('Jumlah_Bayar')
                            ->label('Nominal Pengeluaran')
                            ->prefix('Rp')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        Select::make('Metode_Bayar')
                            ->label('Metode Pembayaran')
                            ->options([
                                'Kas Tunai' => 'Kas Tunai',
                                'Transfer Bank' => 'Transfer Bank (Kas RT)',
                            ])
                            ->default('Kas Tunai')
                            ->required(),

                        Textarea::make('Keterangan')
                            ->label('Keperluan / Uraian Belanja')
                            ->placeholder('Contoh: Pembelian lampu penerangan jalan pos ronda 5 unit')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('Bukti_Path')
                            ->label('Foto Kuitansi / Bukti Nota Belanja')
                            ->image()
                            ->disk('public')
                            ->directory('uploads/bukti')
                            ->maxSize(5120)
                            ->columnSpanFull(),

                        Select::make('ID_RT')
                            ->label('Unit RT')
                            ->options(fn () => DataRt::all()->mapWithKeys(fn ($rt) => [
                                $rt->ID_RT => 'RT ' . str_pad($rt->nomor_rt ?? $rt->ID_RT, 2, '0', STR_PAD_LEFT)
                            ]))
                            ->default(fn () => auth()->user()?->ID_RT ?? 1)
                            ->visible(fn () => (auth()->user()?->role ?? '') === 'super_admin')
                            ->columnSpanFull(),

                        Hidden::make('ID_RT')
                            ->default(fn () => auth()->user()?->ID_RT ?? 1)
                            ->visible(fn () => (auth()->user()?->role ?? '') !== 'super_admin'),
                    ])
                    ->columns(2),
            ]);
    }
}
