<?php

namespace App\Filament\Resources\Inventaris\Tables;

use App\Models\Inventaris;
use App\Models\PembelianInventaris;
use App\Models\Pengeluaran;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class InventarisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Kode_Barang')
                    ->label('Kode Aset')
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('Nama_Barang')
                    ->label('Nama Barang / Fasilitas')
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('Jumlah')
                    ->label('Ketersediaan Stok')
                    ->badge()
                    ->color(fn ($state): string => (int) $state > 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => (int) $state > 0 ? "Tersedia ({$state} Unit)" : 'Stok Kosong (0 Unit)')
                    ->sortable(),

                TextColumn::make('Lokasi')
                    ->label('Lokasi Penyimpanan')
                    ->icon('heroicon-m-map-pin')
                    ->searchable(),

                TextColumn::make('dataRt.nomor_rt')
                    ->label('Unit RT')
                    ->formatStateUsing(fn ($state) => $state ? 'RT ' . str_pad($state, 2, '0', STR_PAD_LEFT) : 'RT 05')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Tgl Registrasi')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('Inventaris_ID', 'asc')
            ->recordActions([
                Action::make('tambah_stok')
                    ->label('Tambah Stok')
                    ->icon('heroicon-m-plus-circle')
                    ->color('success')
                    ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'admin_rt', 'bendahara']))
                    ->modalHeading(fn (Inventaris $record) => "Catat Pengadaan: {$record->Nama_Barang}")
                    ->modalWidth('xl')
                    ->form([
                        TextInput::make('jumlah_pembelian')
                            ->label('Jumlah Unit Tambahan')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        TextInput::make('total_biaya')
                            ->label('Total Biaya (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        Select::make('Metode_Bayar')
                            ->label('Metode Pembayaran')
                            ->options([
                                'Transfer Bank' => 'Transfer Bank (Kas RT)',
                                'Kas Tunai' => 'Kas Tunai RT',
                            ])
                            ->default('Transfer Bank')
                            ->required(),

                        Textarea::make('keterangan')
                            ->label('Keterangan Pengadaan')
                            ->default(fn (Inventaris $record) => "Pengadaan tambahan unit {$record->Nama_Barang} untuk inventaris RT.")
                            ->required()
                            ->rows(2),

                        FileUpload::make('bukti_nota')
                            ->label('Upload Bukti Nota / Kuitansi')
                            ->disk('public')
                            ->directory('uploads/bukti')
                            ->image()
                            ->maxSize(5120),
                    ])
                    ->action(function (Inventaris $record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $buktiPath = $data['bukti_nota'] ?? null;

                            // 1. Insert ke PENGELUARAN
                            $pengeluaran = Pengeluaran::create([
                                'ID_RT' => auth()->user()?->ID_RT ?? $record->ID_RT ?? 1,
                                'Kategori' => 'Inventaris',
                                'Keterangan' => $data['keterangan'],
                                'Jumlah_Bayar' => $data['total_biaya'],
                                'Tgl_Bayar' => now(),
                                'Metode_Bayar' => $data['Metode_Bayar'],
                                'Bukti_Path' => $buktiPath,
                            ]);

                            // 2. Insert ke PEMBELIAN_INVENTARIS (trigger trg_update_stok_inventaris_beli otomatis mengupdate stok Jumlah)
                            PembelianInventaris::create([
                                'inventaris_id' => $record->Inventaris_ID,
                                'Pengeluaran_id' => $pengeluaran->Pengeluaran_id,
                                'jumlah_pembelian' => $data['jumlah_pembelian'],
                                'total_biaya' => $data['total_biaya'],
                                'tgl_pembelian' => now(),
                            ]);
                        });

                        Notification::make()
                            ->title('Pengadaan Berhasil Disimpan')
                            ->body("Stok {$record->Nama_Barang} berhasil ditambah sebanyak {$data['jumlah_pembelian']} unit.")
                            ->success()
                            ->send();
                    }),

                ViewAction::make()->slideOver(),
                EditAction::make()->slideOver(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
