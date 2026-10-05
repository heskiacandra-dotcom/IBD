<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use App\Models\Admin;
use App\Models\Approval;
use App\Models\Aspirasi;
use App\Models\Inventaris;
use App\Models\Kegiatan;
use App\Models\PembelianInventaris;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\ResponAspirasi;
use App\Models\Surat;
use App\Models\OfficialLetter;
use App\Models\Warga;
use App\Filament\Resources\Pemasukans\Pages\ListPemasukans;
use App\Filament\Resources\Surats\Pages\ListSurats;
use App\Filament\Resources\Kegiatans\Pages\ListKegiatans;
use App\Filament\Resources\Inventaris\Pages\ListInventaris;
use App\Filament\Resources\Aspirasis\Pages\ListAspirasis;
use Livewire\Livewire;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CmsAndPortalSynchronizationTest extends TestCase
{
    use DatabaseMigrations;

    protected Admin $bendahara;
    protected Admin $sekretaris;
    protected Admin $adminRt;
    protected Warga $warga;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);

        $this->bendahara = Admin::where('role', 'bendahara')->first() ?? Admin::create([
            'Username' => 'bendahara_sync',
            'Password_hash' => bcrypt('Rahasia123!'),
            'role' => 'bendahara',
            'ID_RT' => 1,
        ]);

        $this->sekretaris = Admin::where('role', 'sekretaris')->first() ?? Admin::create([
            'Username' => 'sekretaris_sync',
            'Password_hash' => bcrypt('Rahasia123!'),
            'role' => 'sekretaris',
            'ID_RT' => 1,
        ]);

        $this->adminRt = Admin::where('role', 'admin_rt')->first();
        $this->warga = Warga::first();
    }

    /**
     * Test 1: Sinkronisasi Surat Pengantar (Pengajuan Warga -> Verifikasi/Penolakan CMS -> Tampilan Portal & Unduh PDF)
     */
    public function test_surat_pengantar_synchronization(): void
    {
        // 1. Warga mengajukan surat dari portal
        $postRes = $this->actingAs($this->warga, 'warga')->postJson(route('portal.submit-surat'), [
            'jenis_surat' => 'Surat Pengantar SKCK Polsek',
            'keperluan' => 'Pendaftaran CPNS Kemenkeu 2026',
        ]);
        $postRes->assertStatus(200);

        $surat = Surat::where('NIK', $this->warga->NIK)
            ->where('Jenis_surat', 'Surat Pengantar SKCK Polsek')
            ->first();
        $this->assertNotNull($surat);

        // 2. Sekretaris membuka CMS dan menerbitkan surat
        $this->actingAs($this->sekretaris, 'web');
        Livewire::test(ListSurats::class)
            ->callAction(TestAction::make('approve_terbitkan')->table($surat))
            ->assertHasNoFormErrors();

        $freshSurat = $surat->fresh();
        $this->assertContains(strtolower($freshSurat->status), ['approved', 'disetujui']);
        $this->assertNotNull($freshSurat->nomor_surat);

        // 3. Warga membuka portal dan melihat nomor surat resmi serta tautan unduh PDF
        $portalRes = $this->actingAs($this->warga, 'warga')->get(route('portal.home'));
        $portalRes->assertStatus(200);
        $portalRes->assertSee($freshSurat->nomor_surat);
        $portalRes->assertSee('Disetujui RT');
        $portalRes->assertSee(route('surat.pdf.download', ['id' => $freshSurat->surat_id]));

        // 4. Test skenario penolakan surat dengan catatan
        $suratTolak = Surat::create([
            'NIK' => $this->warga->NIK,
            'Jenis_surat' => 'Surat Keterangan Usaha Mikro',
            'perihal' => 'Pengajuan KUR Bank',
            'tgl_pengajuan' => now(),
            'status' => 'pending',
        ]);

        $approvalTolak = Approval::create([
            'Admin_ID' => $this->sekretaris->Admin_ID,
            'tabel_sumber' => 'SURAT',
            'id_referensi' => (string) $suratTolak->surat_id,
            'status' => 'pending',
        ]);

        // Pengurus menolak permohonan dengan alasan di CMS
        DB::transaction(function () use ($suratTolak, $approvalTolak) {
            $suratTolak->update(['status' => 'rejected']);
            $approvalTolak->update([
                'status' => 'rejected',
                'catatan' => 'Foto tempat usaha belum dilampirkan.',
                'tgl_approval' => now(),
            ]);
        });

        $portalTolakRes = $this->actingAs($this->warga, 'warga')->get(route('portal.home'));
        $portalTolakRes->assertStatus(200);
        $portalTolakRes->assertSee('Foto tempat usaha belum dilampirkan.');
    }

    /**
     * Test 2: Sinkronisasi Iuran & Pembayaran Kas (Unggah Bukti Warga -> Verifikasi Lunas/Tolak Bendahara di CMS -> Tampilan Portal)
     */
    public function test_iuran_kas_synchronization(): void
    {
        // Buat tagihan iuran untuk warga
        $tagihan = Pemasukan::withoutGlobalScope('tenant')->create([
            'ID_KK' => $this->warga->ID_KK,
            'Kategori' => 'Iuran Warga',
            'ID_RT' => 1,
            'nama_tagihan' => 'Iuran Lingkungan Sinkronisasi',
            'nominal' => 85000,
            'tgl_jatuh_tmp' => now()->addDays(5),
            'status_tagihan' => 'belum_lunas',
        ]);

        // 1. Warga mengunggah bukti bayar di portal
        $fakeFile = UploadedFile::fake()->image('struk_bca.jpg', 600, 400);
        $uploadRes = $this->actingAs($this->warga, 'warga')->postJson(route('portal.konfirmasi-iuran'), [
            'tagihan_id' => $tagihan->Tagihan_Id,
            'bukti_bayar' => $fakeFile,
        ]);
        $uploadRes->assertStatus(200);

        // Status di database terupdate menjadi pending_approval
        $this->assertEquals('pending_approval', $tagihan->fresh()->status_tagihan);

        // 2. Warga melihat status "Menunggu Verifikasi" di portal
        $portalPending = $this->actingAs($this->warga, 'warga')->get(route('portal.home'));
        $portalPending->assertStatus(200);
        $portalPending->assertSee('Menunggu Verifikasi');

        // 3. Bendahara verifikasi lunas di CMS
        $this->actingAs($this->bendahara, 'web');
        Livewire::test(ListPemasukans::class)
            ->callAction(TestAction::make('mark_as_paid')->table($tagihan))
            ->assertHasNoFormErrors();

        $this->assertEquals('Lunas', $tagihan->fresh()->status_tagihan);

        // 4. Warga melihat status "Lunas Sah" di portal
        $portalLunas = $this->actingAs($this->warga, 'warga')->get(route('portal.home'));
        $portalLunas->assertStatus(200);
        $portalLunas->assertSee('Lunas Sah');
    }

    /**
     * Test 3: Sinkronisasi Agenda Kegiatan (Dibuat di CMS -> Muncul di Kalender Portal & Usulan Warga)
     */
    public function test_agenda_kegiatan_synchronization(): void
    {
        // 1. Pengurus RT buat agenda di CMS
        $kegiatan = Kegiatan::create([
            'ID_RT' => 1,
            'nama_kegiatan' => 'Rapat Pleno Warga Triwulan IV',
            'tgl_kegiatan' => now()->addDays(7),
            'lokasi' => 'Balai Warga Blok B',
            'deskripsi' => 'Pembahasan renovasi pos ronda dan persiapan musim hujan.',
            'status' => 'Sedang Berlangsung',
        ]);

        // 2. Warga membuka portal dan melihat agenda kegiatan aktif
        $portalAgenda = $this->actingAs($this->warga, 'warga')->get(route('portal.agenda'));
        $portalAgenda->assertStatus(200);
        $portalAgenda->assertSee('Rapat Pleno Warga Triwulan IV');
        $portalAgenda->assertSee('Sedang Berlangsung');

        // 3. Warga mengusulkan kegiatan baru dari portal
        $usulRes = $this->actingAs($this->warga, 'warga')->postJson(route('portal.usul-kegiatan'), [
            'nama_kegiatan' => 'Senam Sehat Lansia & Remaja',
            'tgl_kegiatan' => now()->addDays(14)->toDateString(),
            'lokasi' => 'Taman Depan Blok A',
            'deskripsi' => 'Instruktur senam dari puskesmas setempat.',
        ]);
        $usulRes->assertStatus(200);

        // Cek tersimpan di database dengan status Mendatang dan terdaftar di antrean Approval CMS
        $usulKegiatan = Kegiatan::where('nama_kegiatan', 'Senam Sehat Lansia & Remaja')->first();
        $this->assertNotNull($usulKegiatan);
        $this->assertEquals('Mendatang', $usulKegiatan->status);

        $approvalKegiatan = Approval::where('tabel_sumber', 'KEGIATAN')
            ->where('id_referensi', (string) $usulKegiatan->kegiatan_id)
            ->first();
        $this->assertNotNull($approvalKegiatan);
        $this->assertEquals('Menunggu', $approvalKegiatan->status);
    }

    /**
     * Test 4: Sinkronisasi Inventaris & Pengadaan (Pengadaan di CMS -> Stok Bertambah di Portal)
     */
    public function test_inventaris_katalog_synchronization(): void
    {
        $uniqueCode = 'AST-SYNC-' . rand(1000, 9999);
        $inventaris = Inventaris::create([
            'ID_RT' => 1,
            'Nama_Barang' => 'Tenda Kerucut Posyandu',
            'Kode_Barang' => $uniqueCode,
            'Jumlah' => 2,
            'Lokasi' => 'Gudang Balai RT',
        ]);

        // 1. Pengurus mencatat pengadaan tambahan 3 unit di CMS
        $this->actingAs($this->bendahara, 'web');
        Livewire::test(ListInventaris::class)
            ->callAction('catat_pengadaan', [
                'tipe_aset' => 'existing',
                'inventaris_id' => $inventaris->Inventaris_ID,
                'jumlah_pembelian' => 3,
                'total_biaya' => 2100000,
                'Metode_Bayar' => 'Transfer Bank',
                'keterangan' => 'Pengadaan 3 unit tambahan tenda kerucut.',
            ])
            ->assertHasNoFormErrors();

        // Stok di database bertambah menjadi 5
        $this->assertEquals(5, $inventaris->fresh()->Jumlah);

        // 2. Warga melihat katalog inventaris di portal dengan stok 5
        $portalLayanan = $this->actingAs($this->warga, 'warga')->get(route('portal.layanan'));
        $portalLayanan->assertStatus(200);
        $portalLayanan->assertSee('Tenda Kerucut Posyandu');
        $portalLayanan->assertSee('Tersedia (5 Unit)');
    }

    /**
     * Test 5: Sinkronisasi Aspirasi & Respon (Warga Mengirim -> Pengurus Menjawab di CMS -> Muncul di Feed Portal)
     */
    public function test_aspirasi_dan_respon_synchronization(): void
    {
        // 1. Warga mengirim aspirasi dari portal
        $aspRes = $this->actingAs($this->warga, 'warga')->postJson(route('portal.submit-aspirasi'), [
            'kategori' => 'Keamanan',
            'judul' => 'Portal Gerbang Masuk Rusak',
            'isi' => 'Pintu gerbang otomatis sebelah barat macet tidak bisa tertutup saat malam hari.',
        ]);
        $aspRes->assertStatus(200);

        $aspirasi = Aspirasi::where('NIK', $this->warga->NIK)
            ->latest('id_aspirasi')
            ->first();
        $this->assertNotNull($aspirasi);

        // 2. Ketua RT merespons aspirasi di CMS
        $this->actingAs($this->adminRt, 'web');
        Livewire::test(ListAspirasis::class)
            ->callAction(TestAction::make('balas_aspirasi')->table($aspirasi), [
                'nama_responden' => 'Bpk. Sukardi (Ketua RT 05)',
                'isi_respon' => 'Sudah kami panggilkan teknisi gerbang otomatis, perbaikan dilakukan siang ini.',
            ])
            ->assertHasNoFormErrors();

        // 3. Warga membuka halaman aspirasi di portal dan membaca respon resmi
        $portalAsp = $this->actingAs($this->warga, 'warga')->get(route('portal.aspirasi'));
        $portalAsp->assertStatus(200);
        $portalAsp->assertSee('Portal Gerbang Masuk Rusak');
        $portalAsp->assertSee('Ditanggapi Resmi RT');
        $portalAsp->assertSee('Bpk. Sukardi (Ketua RT 05)');
        $portalAsp->assertSee('Sudah kami panggilkan teknisi gerbang otomatis');
    }

    /**
     * Test 6: Verifikasi pengajuan surat oleh warga spesifik (Ibu Dewi Sartika)
     * Memastikan nama pemohon dan NIK tidak tertukar/fallback ke warga lain (misal Hendra Wijaya)
     */
    public function test_surat_pengajuan_oleh_ibu_dewi_sartika_sesuai_identitas_pemohon(): void
    {
        $dewi = Warga::where('Nama_Lengkap', 'like', '%Dewi Sartika%')->first();
        $this->assertNotNull($dewi);

        // 1. Submit surat pengantar atas nama Ibu Dewi Sartika
        $res = $this->actingAs($dewi, 'warga')->postJson(route('portal.submit-surat'), [
            'jenis_surat' => 'Surat Keterangan Domisili Warga',
            'keperluan' => 'Pendaftaran Sekolah Anak',
            'nik' => $dewi->NIK,
            'nama' => $dewi->Nama_Lengkap,
        ]);

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        // 2. Pastikan di tabel SURAT tercatat atas NIK Ibu Dewi
        $suratDewi = Surat::where('NIK', $dewi->NIK)
            ->where('perihal', 'Pendaftaran Sekolah Anak')
            ->first();
        $this->assertNotNull($suratDewi);
        $this->assertEquals($dewi->NIK, $suratDewi->NIK);
        $this->assertEquals('Ibu Dewi Sartika', $suratDewi->warga->Nama_Lengkap);

        // 3. Verifikasi di CMS ListSurats muncul nama Ibu Dewi Sartika
        $this->actingAs($this->sekretaris, 'web');
        Livewire::test(ListSurats::class)
            ->assertCanSeeTableRecords([$suratDewi])
            ->assertSee('Ibu Dewi Sartika')
            ->assertSee($dewi->NIK);
    }

    /**
     * Test 7: Sinkronisasi Transparansi Keuangan & Alokasi Kas (CMS Pengeluaran -> Portal Alokasi & Mutasi)
     */
    public function test_transparansi_keuangan_dan_alokasi_synchronization(): void
    {
        // 1. Buat pengeluaran baru di CMS
        $pengeluaran = Pengeluaran::withoutGlobalScope('tenant')->create([
            'ID_RT' => 1,
            'Kategori' => 'Keamanan Lingkungan',
            'Jumlah_Bayar' => 3500000,
            'Tgl_Bayar' => now()->toDateString(),
            'Keterangan' => 'Honor Tim Petugas Keamanan Malam Pos Ronda',
            'Metode_Bayar' => 'Transfer Bank',
        ]);

        // 2. Akses halaman transparansi di portal
        $res = $this->actingAs($this->warga, 'warga')->get(route('portal.transparansi'));
        $res->assertStatus(200);
        $res->assertSee('Honor Tim Petugas Keamanan Malam Pos Ronda');
        $res->assertSee('Keamanan Lingkungan & Honor Sekuriti');
        $res->assertSee('Rp 3.500.000');
    }

    /**
     * Test 8: Peminjaman Inventaris dan Tampilan Peminjam di CMS (Warga Pinjam di Portal -> Terlihat Peminjam di CMS -> Pengurus Kembalikan Aset)
     */
    public function test_peminjaman_inventaris_dan_tampilan_peminjam_di_cms(): void
    {
        $inventaris = Inventaris::first() ?? Inventaris::create([
            'ID_RT' => 1,
            'Nama_Barang' => 'Proyektor Epson Balai RT',
            'Kode_Barang' => 'AST-TEST-PROJ',
            'Jumlah' => 2,
            'Lokasi' => 'Gudang Balai RT',
        ]);

        $stokAwal = $inventaris->Jumlah;

        // 1. Warga mengajukan pinjam aset dari portal
        $res = $this->actingAs($this->warga, 'warga')->postJson(route('portal.pinjam-aset'), [
            'inventaris_id' => $inventaris->Inventaris_ID,
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDays(2)->toDateString(),
            'tujuan' => 'Rapat Karang Taruna Persiapan HUT RI',
        ]);

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        // 2. Verifikasi model Inventaris dapat mendeteksi peminjam aktif
        $daftarPeminjam = $inventaris->getDaftarPeminjam();
        $this->assertNotEmpty($daftarPeminjam);
        $this->assertTrue($daftarPeminjam->contains(function ($item) {
            return str_contains($item['nama'], $this->warga->Nama_Lengkap);
        }));

        $summary = $inventaris->getActiveBorrowersSummary();
        $this->assertNotNull($summary);
        $this->assertStringContainsString($this->warga->Nama_Lengkap, $summary);

        // 3. Admin RT membuka halaman Inventaris di CMS dan melihat nama peminjam aktif pada baris aset
        $this->actingAs($this->adminRt, 'web');
        Livewire::test(ListInventaris::class)
            ->assertCanSeeTableRecords([$inventaris])
            ->assertSee($this->warga->Nama_Lengkap);

        // 4. Simulasi persetujuan peminjaman (status menjadi borrowed)
        \App\Models\AssetLoan::where('nama_alat', $inventaris->Nama_Barang)
            ->where('status', 'pending')
            ->update(['status' => 'borrowed']);

        // 5. Pengurus mengeksekusi aksi 'kembalikan_aset' di tabel CMS
        Livewire::test(ListInventaris::class)
            ->callAction(TestAction::make('kembalikan_aset')->table($inventaris))
            ->assertHasNoErrors();

        // 6. Verifikasi status peminjaman berubah menjadi returned dan stok aset bertambah
        $loanUpdated = \App\Models\AssetLoan::where('nama_alat', $inventaris->Nama_Barang)->latest()->first();
        $this->assertEquals('returned', $loanUpdated->status);
        $this->assertEquals($stokAwal + 1, $inventaris->fresh()->Jumlah);
    }
}

