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
use App\Models\Warga;
use App\Filament\Resources\Pemasukans\Pages\ListPemasukans;
use App\Filament\Resources\Surats\Pages\ListSurats;
use App\Filament\Resources\Kegiatans\Pages\ListKegiatans;
use App\Filament\Resources\Inventaris\Pages\ListInventaris;
use App\Filament\Resources\Aspirasis\Pages\ListAspirasis;
use Livewire\Livewire;
use Illuminate\Support\Facades\DB;

class NeighborhoodOfficerOperationsTest extends TestCase
{
    use DatabaseMigrations;

    protected Admin $bendahara;
    protected Admin $sekretaris;
    protected Admin $adminRt;
    protected Admin $superAdmin;
    protected Warga $warga;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);

        $this->bendahara = Admin::where('role', 'bendahara')->first() ?? Admin::create([
            'Username' => 'bendahara_test',
            'Password_hash' => bcrypt('Rahasia123!'),
            'role' => 'bendahara',
            'ID_RT' => 1,
        ]);

        $this->sekretaris = Admin::where('role', 'sekretaris')->first() ?? Admin::create([
            'Username' => 'sekretaris_test',
            'Password_hash' => bcrypt('Rahasia123!'),
            'role' => 'sekretaris',
            'ID_RT' => 1,
        ]);

        $this->adminRt = Admin::where('role', 'admin_rt')->first();
        $this->superAdmin = Admin::where('role', 'super_admin')->first();
        $this->warga = Warga::first();
    }

    /**
     * Requirement 1: Bendahara / Admin RT verifikasi & tolak pembayaran kas RT secara atomik.
     */
    public function test_pemasukan_verifikasi_lunas_dan_tolak_atomik(): void
    {
        $this->actingAs($this->bendahara, 'web');

        // Create pending payment
        $tagihan = Pemasukan::withoutGlobalScope('tenant')->create([
            'ID_KK' => $this->warga->ID_KK,
            'Kategori' => 'Iuran Warga',
            'ID_RT' => 1,
            'nama_tagihan' => 'Iuran Keamanan Uji Coba',
            'nominal' => 75000,
            'tgl_jatuh_tmp' => now()->addDays(5),
            'status_tagihan' => 'pending_approval',
            'bukti_bayar' => 'uploads/bukti/test_bukti.jpg',
        ]);

        $approval = Approval::create([
            'Admin_ID' => $this->bendahara->Admin_ID,
            'tabel_sumber' => 'PEMASUKAN',
            'id_referensi' => (string) $tagihan->Tagihan_Id,
            'status' => 'pending',
            'catatan' => 'Konfirmasi pembayaran via portal',
        ]);

        // 1. Test approve
        Livewire::test(ListPemasukans::class)
            ->callTableAction('preview_bukti', $tagihan)
            ->callTableAction('mark_as_paid', $tagihan)
            ->assertHasNoTableActionErrors();

        $this->assertEquals('Lunas', $tagihan->fresh()->status_tagihan);
        $this->assertContains(strtolower($approval->fresh()->status), ['approved', 'disetujui']);

        // 2. Test reject
        $tagihan2 = Pemasukan::withoutGlobalScope('tenant')->create([
            'ID_KK' => $this->warga->ID_KK,
            'Kategori' => 'Iuran Warga',
            'ID_RT' => 1,
            'nama_tagihan' => 'Iuran Sampah Uji Coba',
            'nominal' => 35000,
            'tgl_jatuh_tmp' => now()->addDays(5),
            'status_tagihan' => 'pending_approval',
        ]);

        $approval2 = Approval::create([
            'Admin_ID' => $this->bendahara->Admin_ID,
            'tabel_sumber' => 'PEMASUKAN',
            'id_referensi' => (string) $tagihan2->Tagihan_Id,
            'status' => 'pending',
        ]);

        DB::transaction(function () use ($tagihan2, $approval2) {
            $tagihan2->update(['status_tagihan' => 'belum_lunas']);
            $approval2->update([
                'status' => 'rejected',
                'catatan' => 'Nominal transfer tidak sesuai',
                'tgl_approval' => now(),
            ]);
        });

        $this->assertEquals('belum_lunas', $tagihan2->fresh()->status_tagihan);
        $this->assertEquals('rejected', $approval2->fresh()->status);
        $this->assertEquals('Nominal transfer tidak sesuai', $approval2->fresh()->catatan);
    }

    /**
     * Requirement 2: Sekretaris / Admin RT verifikasi & terbitkan surat pengantar resmi ber-nomor otomatis.
     */
    public function test_sekretaris_terbitkan_surat_pengantar_dan_unduh_pdf(): void
    {
        $this->actingAs($this->sekretaris, 'web');

        // Create pending surat
        $surat = Surat::create([
            'NIK' => $this->warga->NIK,
            'Jenis_surat' => 'Surat Pengantar SKCK Kepolisian',
            'perihal' => 'Keperluan melamar pekerjaan BUMN',
            'tgl_pengajuan' => now(),
            'status' => 'pending',
            'nomor_surat' => null,
        ]);

        $approval = Approval::create([
            'Admin_ID' => $this->sekretaris->Admin_ID,
            'tabel_sumber' => 'SURAT',
            'id_referensi' => (string) $surat->surat_id,
            'status' => 'pending',
        ]);

        // Execute approve & issue action
        Livewire::test(ListSurats::class)
            ->callTableAction('approve_terbitkan', $surat)
            ->assertHasNoTableActionErrors();

        $freshSurat = $surat->fresh();
        $this->assertContains(strtolower($freshSurat->status), ['approved', 'disetujui']);
        $this->assertNotNull($freshSurat->nomor_surat);
        $this->assertStringContainsString('SP-RT', $freshSurat->nomor_surat);

        // Verify PDF download route
        $pdfResponse = $this->get(route('surat.pdf.download', ['id' => $freshSurat->surat_id]));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('Content-Type'));
    }

    /**
     * Requirement 3: Sekretaris / Admin RT kelola agenda kegiatan lingkungan & status publikasi.
     */
    public function test_kegiatan_lingkungan_creation_and_scoping(): void
    {
        $this->actingAs($this->sekretaris, 'web');

        $kegiatan = Kegiatan::create([
            'ID_RT' => 1,
            'nama_kegiatan' => 'Kerja Bakti Saluran Air Menyambut Musim Hujan',
            'tgl_kegiatan' => now()->addDays(3),
            'lokasi' => 'Balai Pertemuan Warga RT 05',
            'deskripsi' => 'Diharapkan setiap KK mengirimkan 1 perwakilan.',
            'status' => 'Mendatang',
        ]);

        $this->assertNotNull($kegiatan->kegiatan_id);
        $this->assertEquals('Mendatang', $kegiatan->status);

        Livewire::test(ListKegiatans::class)
            ->assertCanRenderTableColumn('nama_kegiatan')
            ->assertCanRenderTableColumn('status')
            ->assertSee('Kerja Bakti Saluran Air Menyambut Musim Hujan');
    }

    /**
     * Requirement 4: Pengelolaan inventaris & pencatatan pengadaan baru (Kas Keluar + Tambah Stok).
     */
    public function test_catat_pengadaan_inventaris_transaksional(): void
    {
        $this->actingAs($this->bendahara, 'web');

        $inventaris = Inventaris::create([
            'ID_RT' => 1,
            'Nama_Barang' => 'Kursi Lipat Chitose',
            'Kode_Barang' => 'AST-TEST-' . rand(1000, 9999),
            'Jumlah' => 20,
            'Lokasi' => 'Balai RT 05',
        ]);

        $initialStock = $inventaris->Jumlah;

        // Perform purchase action
        Livewire::test(ListInventaris::class)
            ->callAction('catat_pengadaan', [
                'tipe_aset' => 'existing',
                'inventaris_id' => $inventaris->Inventaris_ID,
                'jumlah_pembelian' => 10,
                'total_biaya' => 1500000,
                'Metode_Bayar' => 'Transfer Bank',
                'keterangan' => 'Pembelian tambahan 10 kursi lipat balai warga.',
            ])
            ->assertHasNoActionErrors();

        // Verify stock incremented
        $this->assertEquals($initialStock + 10, $inventaris->fresh()->Jumlah);

        // Verify kas keluar created
        $pengeluaran = Pengeluaran::where('Keterangan', 'Pembelian tambahan 10 kursi lipat balai warga.')->first();
        $this->assertNotNull($pengeluaran);
        $this->assertEquals(1500000, (float) $pengeluaran->Jumlah_Bayar);

        // Verify pembelian inventaris record
        $pembelian = PembelianInventaris::where('Pengeluaran_id', $pengeluaran->Pengeluaran_id)->first();
        $this->assertNotNull($pembelian);
        $this->assertEquals(10, $pembelian->jumlah_pembelian);
    }

    /**
     * Requirement 5: Tanggapan dan balas aspirasi warga tersimpan di RESPON_ASPIRASI.
     */
    public function test_tanggapi_dan_balas_aspirasi_warga(): void
    {
        $this->actingAs($this->adminRt, 'web');

        $aspirasi = Aspirasi::create([
            'NIK' => $this->warga->NIK,
            'Isi_Aspirasi' => 'Lampu penerangan jalan di gang 3 redup mohon diganti dengan watt lebih besar.',
        ]);

        $this->assertNull($aspirasi->responAspirasi);

        // Call table action balas_aspirasi
        Livewire::test(ListAspirasis::class)
            ->callTableAction('balas_aspirasi', $aspirasi, [
                'nama_responden' => 'Ketua RT 05 (Sukardi)',
                'isi_respon' => 'Terima kasih atas laporannya. Tim sarpras RT sudah menjadwalkan penggantian lampu LED 30 Watt besok malam.',
            ])
            ->assertHasNoTableActionErrors();

        // Verify saved in RESPON_ASPIRASI
        $freshAspirasi = $aspirasi->fresh();
        $this->assertNotNull($freshAspirasi->responAspirasi);
        $this->assertEquals('Ketua RT 05 (Sukardi)', $freshAspirasi->responAspirasi->nama_responden);
        $this->assertStringContainsString('penggantian lampu LED', $freshAspirasi->responAspirasi->isi_respon);
    }

    /**
     * Requirement 6: Pendaftaran Warga Baru di CMS (NIK 16 digit, ID_KK, ID_RT, Nama Lengkap).
     */
    public function test_pendaftaran_warga_baru_di_cms(): void
    {
        $this->actingAs($this->sekretaris, 'web');

        $randomNik = '327101' . rand(1000000000, 9999999999);
        $kk = \App\Models\KartuKeluarga::first();

        \Livewire\Livewire::test(\App\Filament\Resources\Wargas\Pages\CreateWarga::class)
            ->fillForm([
                'NIK' => $randomNik,
                'Nama_Lengkap' => 'Ahmad Fauzi Budiman',
                'ID_KK' => $kk->ID_KK,
                'ID_RT' => 1,
                'no_telp' => '081234567890',
                'role' => 'warga',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $wargaCreated = Warga::where('NIK', $randomNik)->first();
        $this->assertNotNull($wargaCreated);
        $this->assertEquals('Ahmad Fauzi Budiman', $wargaCreated->Nama_Lengkap);
        $this->assertEquals($kk->ID_KK, $wargaCreated->ID_KK);
        $this->assertEquals(1, $wargaCreated->ID_RT);
        // Pastikan default username terisi NIK dan password ter-hash default
        $this->assertEquals($randomNik, $wargaCreated->Username);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('12345678', $wargaCreated->Password_hash));
    }
}

