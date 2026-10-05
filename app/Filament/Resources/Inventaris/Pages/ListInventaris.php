<?php

namespace App\Filament\Resources\Inventaris\Pages;

use App\Filament\Resources\Inventaris\InventarisResource;
use App\Models\Inventaris;
use App\Models\PembelianInventaris;
use App\Models\Pengeluaran;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ListInventaris extends ListRecords
{
    protected static string $resource = InventarisResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Aset')
                ->badge(fn () => $this->getModel()::count() ?: null),
            'tersedia' => Tab::make('Tersedia')
                ->badge(fn () => $this->getModel()::where('Jumlah', '>', 0)->count() ?: null)
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('Jumlah', '>', 0)),
            'dipinjam' => Tab::make('Sedang Dipinjam')
                ->badge(fn () => $this->getModel()::whereHas('loans', fn ($q) => $q->whereIn('status', ['borrowed', 'disetujui']))->count() ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('loans', fn ($q) => $q->whereIn('status', ['borrowed', 'disetujui']))),
            'kosong' => Tab::make('Stok Kosong')
                ->badge(fn () => $this->getModel()::where('Jumlah', '<=', 0)->count() ?: null)
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('Jumlah', '<=', 0)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('semua_peminjam')
                ->label('Rekap Peminjam Aset')
                ->icon('heroicon-m-clipboard-document-list')
                ->color('gray')
                ->modalHeading('Rekapitulasi Peminjaman Aset RT')
                ->modalDescription('Pantau seluruh warga yang meminjam alat/fasilitas lingkungan RT beserta masa berlaku dan nomor kontak WhatsApp.')
                ->modalWidth('4xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup')
                ->modalContent(function () {
                    $loans = \App\Models\AssetLoan::latest('tgl_mulai')->get();
                    $approvals = \App\Models\Approval::where('tabel_sumber', 'INVENTARIS')->latest('id_approval')->get();
                    return view('filament.modals.rekap-semua-peminjam', compact('loans', 'approvals'));
                }),

            Action::make('catat_pengadaan')
                ->label('Catat Pengadaan / Pembelian')
                ->icon('heroicon-m-shopping-bag')
                ->color('primary')
                ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'admin_rt', 'bendahara']))
                ->modalHeading('Catat Pengadaan & Pembelian Aset RT')
                ->modalDescription('Transaksi ini akan otomatis mencatat pengeluaran kas RT dan menambahkan stok fisik ke katalog inventaris.')
                ->modalWidth('xl')
                ->form([
                    Radio::make('tipe_aset')
                        ->label('Jenis Pengadaan')
                        ->options([
                            'existing' => 'Tambah Unit Barang Yang Sudah Ada',
                            'new' => 'Daftarkan Barang / Aset Baru',
                        ])
                        ->default('existing')
                        ->live()
                        ->required(),

                    Select::make('inventaris_id')
                        ->label('Pilih Barang Inventaris')
                        ->options(function () {
                            $query = Inventaris::query();
                            if (auth()->user()?->role !== 'super_admin' && auth()->user()?->ID_RT) {
                                $query->where('ID_RT', auth()->user()->ID_RT);
                            }
                            return $query->pluck('Nama_Barang', 'Inventaris_ID');
                        })
                        ->searchable()
                        ->visible(fn ($get) => $get('tipe_aset') === 'existing')
                        ->required(fn ($get) => $get('tipe_aset') === 'existing'),

                    TextInput::make('Nama_Barang')
                        ->label('Nama Barang Baru')
                        ->placeholder('Contoh: Mesin Fogging Nyamuk DBD')
                        ->visible(fn ($get) => $get('tipe_aset') === 'new')
                        ->required(fn ($get) => $get('tipe_aset') === 'new'),

                    TextInput::make('Kode_Barang')
                        ->label('Kode Barang / Aset')
                        ->placeholder('Contoh: AST-FGG-01')
                        ->default(fn () => 'AST-' . strtoupper(bin2hex(random_bytes(2))))
                        ->visible(fn ($get) => $get('tipe_aset') === 'new')
                        ->required(fn ($get) => $get('tipe_aset') === 'new'),

                    TextInput::make('Lokasi')
                        ->label('Lokasi Penyimpanan')
                        ->placeholder('Contoh: Pos Satpam / Balai RW')
                        ->default('Balai RT')
                        ->visible(fn ($get) => $get('tipe_aset') === 'new')
                        ->required(fn ($get) => $get('tipe_aset') === 'new'),

                    TextInput::make('jumlah_pembelian')
                        ->label('Jumlah Pembelian (Unit)')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required(),

                    TextInput::make('total_biaya')
                        ->label('Total Biaya (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->placeholder('Contoh: 1500000')
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
                        ->label('Keterangan Pembelian')
                        ->placeholder('Contoh: Pengadaan 5 unit kursi lipat untuk keperluan rapat warga.')
                        ->required()
                        ->rows(2),

                    FileUpload::make('bukti_nota')
                        ->label('Upload Bukti Nota / Kuitansi')
                        ->disk('public')
                        ->directory('uploads/bukti')
                        ->image()
                        ->maxSize(5120),
                ])
                ->action(function (array $data) {
                    DB::transaction(function () use ($data) {
                        $userRtId = auth()->user()?->ID_RT ?? 1;
                        $buktiPath = $data['bukti_nota'] ?? null;

                        // 1. Insert kas keluar ke tabel PENGELUARAN
                        $pengeluaran = Pengeluaran::create([
                            'ID_RT' => $userRtId,
                            'Kategori' => 'Inventaris',
                            'Keterangan' => $data['keterangan'],
                            'Jumlah_Bayar' => $data['total_biaya'],
                            'Tgl_Bayar' => now(),
                            'Metode_Bayar' => $data['Metode_Bayar'],
                            'Bukti_Path' => $buktiPath,
                        ]);

                        // 2. Resolve or Create INVENTARIS
                        if ($data['tipe_aset'] === 'new') {
                            $inventaris = Inventaris::create([
                                'ID_RT' => $userRtId,
                                'Nama_Barang' => $data['Nama_Barang'],
                                'Kode_Barang' => $data['Kode_Barang'],
                                'Jumlah' => 0,
                                'Lokasi' => $data['Lokasi'] ?? 'Balai RT',
                            ]);
                        } else {
                            $inventaris = Inventaris::findOrFail($data['inventaris_id']);
                        }

                        // 3. Insert ke PEMBELIAN_INVENTARIS (trigger trg_update_stok_inventaris_beli otomatis mengupdate stok Jumlah)
                        PembelianInventaris::create([
                            'inventaris_id' => $inventaris->Inventaris_ID,
                            'Pengeluaran_id' => $pengeluaran->Pengeluaran_id,
                            'jumlah_pembelian' => $data['jumlah_pembelian'],
                            'total_biaya' => $data['total_biaya'],
                            'tgl_pembelian' => now(),
                        ]);
                    });

                    Notification::make()
                        ->title('Pengadaan Aset Berhasil')
                        ->body("Pembelian inventaris berhasil dicatat dalam kas keluar dan stok telah diperbarui.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
