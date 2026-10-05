<?php

namespace App\Filament\Resources\Inventaris\Tables;

use App\Models\Approval;
use App\Models\AssetLoan;
use App\Models\Inventaris;
use App\Models\PembelianInventaris;
use App\Models\Pengeluaran;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
                    ->label('Nama Aset & Lokasi')
                    ->weight('bold')
                    ->description(fn (Inventaris $record) => $record->Lokasi ? "Lokasi: {$record->Lokasi}" : null)
                    ->searchable()
                    ->wrap(),

                TextColumn::make('Jumlah')
                    ->label('Ketersediaan')
                    ->badge()
                    ->color(fn ($state): string => (int) $state > 0 ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => (int) $state > 0 ? "Tersedia ({$state} Unit)" : 'Stok Kosong (0 Unit)')
                    ->sortable(),

                TextColumn::make('peminjam_aktif')
                    ->label('Peminjam Aktif')
                    ->state(fn (Inventaris $record) => $record->getActiveBorrowersSummary() ?? 'Tersedia')
                    ->badge()
                    ->color(fn (Inventaris $record): string => $record->getActiveBorrowersSummary() ? 'warning' : 'success')
                    ->icon(fn (Inventaris $record): string => $record->getActiveBorrowersSummary() ? 'heroicon-m-user' : 'heroicon-m-check-circle')
                    ->tooltip('Klik tombol "Peminjam" untuk rincian data peminjam dan kontak WhatsApp.'),

                TextColumn::make('Lokasi')
                    ->label('Lokasi Penyimpanan')
                    ->icon('heroicon-m-map-pin')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('dataRt.nomor_rt')
                    ->label('Unit RT')
                    ->formatStateUsing(fn ($state) => $state ? 'RT ' . str_pad($state, 2, '0', STR_PAD_LEFT) : 'RT 05')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Tgl Registrasi')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('Inventaris_ID', 'asc')
            ->recordActions([
                Action::make('lihat_peminjam')
                    ->label('Peminjam')
                    ->icon('heroicon-m-user-group')
                    ->color('info')
                    ->modalHeading(fn (Inventaris $record) => "Daftar Peminjam: {$record->Nama_Barang} ({$record->Kode_Barang})")
                    ->modalDescription('Pantau riwayat peminjam aktif, kontak WhatsApp, dan periode pengembalian barang.')
                    ->modalWidth('3xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Inventaris $record) => view('filament.modals.daftar-peminjam-inventaris', [
                        'inventaris' => $record,
                        'peminjamList' => $record->getDaftarPeminjam(),
                    ])),

                Action::make('kembalikan_aset')
                    ->label('Kembalikan')
                    ->icon('heroicon-m-arrow-path-rounded-square')
                    ->color('warning')
                    ->visible(fn (Inventaris $record) => $record->getDaftarPeminjam()->where('can_return', true)->isNotEmpty())
                    ->requiresConfirmation()
                    ->modalHeading(fn (Inventaris $record) => "Pengembalian Aset: {$record->Nama_Barang}")
                    ->modalDescription('Apakah peminjam telah mengembalikan aset ini ke gudang/balai RT dalam kondisi baik? Aksi ini akan menambah stok fisik sebanyak 1 unit.')
                    ->action(function (Inventaris $record) {
                        DB::transaction(function () use ($record) {
                            // Update AssetLoan
                            AssetLoan::where(function ($q) use ($record) {
                                $q->where('kode_aset', $record->Kode_Barang)->orWhere('nama_alat', $record->Nama_Barang);
                            })->whereIn('status', ['borrowed', 'disetujui'])->update([
                                'status' => 'returned',
                                'tgl_kembali' => now(),
                                'kondisi_kembali' => 'Baik & Lengkap',
                            ]);

                            // Update Approval jika ada
                            Approval::where('tabel_sumber', 'INVENTARIS')
                                ->where('id_referensi', (string) $record->Inventaris_ID)
                                ->whereIn('status', ['Disetujui', 'approved'])
                                ->update(['status' => 'Selesai']);

                            // Tambah stok fisik kembali
                            $record->increment('Jumlah', 1);
                        });

                        Notification::make()
                            ->title('Barang Telah Dikembalikan')
                            ->body("Aset {$record->Nama_Barang} telah ditandai selesai dipinjam. Stok tersedia sekarang: {$record->fresh()->Jumlah} unit.")
                            ->success()
                            ->send();
                    }),

                ActionGroup::make([
                    Action::make('tambah_stok')
                        ->label('Pengadaan Stok')
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

                    ViewAction::make()->slideOver()->label('Lihat Detail Aset'),
                    EditAction::make()->slideOver()->label('Edit Data Aset'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

