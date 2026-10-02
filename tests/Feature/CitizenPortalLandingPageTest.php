<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use App\Models\Surat;
use App\Models\Approval;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Inventaris;
use App\Models\PembelianInventaris;
use App\Models\Aspirasi;
use App\Models\ResponAspirasi;
use App\Models\Warga;
use App\Models\Admin;
use App\Models\KartuKeluarga;
use App\Models\DataRt;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CitizenPortalLandingPageTest extends TestCase
{
    use DatabaseMigrations;

    protected Warga $warga;
    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);

        // Ensure triggers are present for testing
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::unprepared("
                CREATE TRIGGER IF NOT EXISTS trg_auto_approval_surat_warga
                AFTER INSERT ON SURAT
                BEGIN
                    INSERT INTO APPROVAL (Admin_ID, tabel_sumber, id_referensi, status, catatan, tgl_approval, created_at, updated_at)
                    VALUES (NULL, 'SURAT', NEW.surat_id, 'pending', 'Pengajuan surat baru via portal warga', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
                END;
            ");

            DB::unprepared("
                CREATE TRIGGER IF NOT EXISTS trg_update_stok_inventaris_beli
                AFTER INSERT ON PEMBELIAN_INVENTARIS
                BEGIN
                    UPDATE INVENTARIS 
                    SET Jumlah = Jumlah + NEW.jumlah_pembelian 
                    WHERE Inventaris_ID = NEW.inventaris_id;
                END;
            ");
        }

        $this->warga = Warga::first();
        $this->admin = Admin::first();
    }

    /**
     * Requirement 1.1: Root Route Gateway ('/')
     * - Unauthenticated (Guest) -> strictly redirects to '/login'
     * - auth('warga') -> redirects to '/portal'
     * - auth('web') -> redirects to '/cms/dashboard'
     */
    public function test_root_route_gateway_redirects_correctly(): void
    {
        // 1. Guest
        $guestResponse = $this->get('/');
        $guestResponse->assertRedirect(route('login'));

        // 2. Citizen Authenticated
        $wargaResponse = $this->actingAs($this->warga, 'warga')->get('/');
        $wargaResponse->assertRedirect(route('portal.home'));

        // Clear auth state before testing admin guard
        auth('warga')->logout();
        session()->flush();

        // 3. Admin / Officer Authenticated
        $adminResponse = $this->actingAs($this->admin, 'web')->get('/');
        $adminResponse->assertRedirect(route('cms.dashboard'));
    }

    /**
     * Requirement 1.2: Citizen Portal is protected behind auth:warga middleware
     */
    public function test_citizen_portal_requires_warga_authentication(): void
    {
        // Guest visiting /portal gets redirected to /login
        $response = $this->get('/portal');
        $response->assertRedirect(route('login'));

        // Authenticated citizen can access /portal (200)
        $authResponse = $this->actingAs($this->warga, 'warga')->get('/portal');
        $authResponse->assertStatus(200);
    }

    /**
     * Requirement 3: Citizen Portal Layout Structure (4 Core Sections)
     */
    public function test_citizen_portal_renders_four_core_sections(): void
    {
        $response = $this->actingAs($this->warga, 'warga')->get('/portal');

        $response->assertStatus(200);

        // Section 1: Compact Header & Profile Pill
        $response->assertSee('Halo, ' . $this->warga->Nama_Lengkap, false);
        $response->assertSee('KK: ' . $this->warga->ID_KK, false);
        $response->assertSee('Edit Profil');
        $response->assertSee('Keluar');

        // Section 2: 4 Primary Quick-Action Matrix Cards
        $response->assertSee('Ajukan Surat Pengantar');
        $response->assertSee('Bayar Iuran RT');
        $response->assertSee('Peminjaman Alat');
        $response->assertSee('Suara Warga / Aspirasi');

        // Section 3: Activity & Status Tracking Tab
        $response->assertSee('Aktivitas');
        $response->assertSee('Surat Saya');
        $response->assertSee('Tagihan Iuran Saya');
        $response->assertSee('Aspirasi Saya');

        // Section 4: Financial Transparency & Community Events
        $response->assertSee('Transparansi Kas Lingkungan');
        $response->assertSee('Agenda Kegiatan Lingkungan');

        // Pure Light Mode verification (no dark mode container classes)
        $response->assertDontSee('bg-dark');
        $response->assertDontSee('card-dark');
    }

    /**
     * Requirement 2: Strict Citizen Data Privacy & Isolation
     * Citizen can only see their own household billing in PEMASUKAN
     */
    public function test_citizen_can_only_see_their_own_household_bills(): void
    {
        // Create another family with pending bill
        $otherKk = KartuKeluarga::firstOrCreate(
            ['ID_KK' => '3201059999990099'],
            ['Alamat' => 'Blok Z No. 99']
        );

        $otherBill = Pemasukan::withoutGlobalScope('tenant')->create([
            'ID_KK' => $otherKk->ID_KK,
            'Kategori' => 'Iuran Warga',
            'ID_RT' => $this->warga->ID_RT,
            'nama_tagihan' => 'Tagihan Rahasia Keluarga Lain',
            'nominal' => 250000,
            'tgl_jatuh_tmp' => now()->toDateString(),
            'status_tagihan' => 'Belum Lunas',
        ]);

        // Cek via API
        $response = $this->actingAs($this->warga, 'warga')->getJson(route('portal.cek-iuran'));
        $response->assertStatus(200);

        // Verify other family's bill is NOT visible in citizen's payload
        $response->assertDontSee('Tagihan Rahasia Keluarga Lain');

        // Cek via HTML Portal
        $portalResponse = $this->actingAs($this->warga, 'warga')->get('/portal');
        $portalResponse->assertStatus(200);
        $portalResponse->assertDontSee('Tagihan Rahasia Keluarga Lain');
    }

    /**
     * Requirement 4.1: Trigger trg_auto_approval_surat_warga automatically inserts ticket into APPROVAL
     */
    public function test_trigger_auto_approval_surat_warga_creates_approval_ticket(): void
    {
        // Submit Surat
        $payload = [
            'jenis_surat' => 'Surat Pengantar SKCK Kepolisian',
            'keperluan' => 'Pendaftaran pekerjaan BUMN',
        ];

        $response = $this->actingAs($this->warga, 'warga')->postJson(route('portal.submit-surat'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $suratId = $response->json('surat_id');
        $this->assertNotNull($suratId);

        // Verify SURAT record exists
        $this->assertDatabaseHas('SURAT', [
            'surat_id' => $suratId,
            'NIK' => $this->warga->NIK,
            'Jenis_surat' => 'Surat Pengantar SKCK Kepolisian',
        ]);

        // Verify APPROVAL record was automatically created
        $this->assertDatabaseHas('APPROVAL', [
            'tabel_sumber' => 'SURAT',
            'id_referensi' => (string) $suratId,
        ]);
    }

    /**
     * Requirement 4.2: Trigger trg_update_stok_inventaris_beli increments INVENTARIS.Jumlah
     */
    public function test_trigger_update_stok_inventaris_beli(): void
    {
        $inv = Inventaris::withoutGlobalScope('tenant')->first();
        $initialStock = $inv->Jumlah;
        $tambahStok = 5;

        // Insert into PEMBELIAN_INVENTARIS
        PembelianInventaris::withoutGlobalScope('tenant')->create([
            'inventaris_id' => $inv->Inventaris_ID,
            'Pengeluaran_id' => null,
            'tgl_pembelian' => now()->toDateString(),
            'total_biaya' => 500000,
            'jumlah_pembelian' => $tambahStok,
        ]);

        // Refresh model and assert stock incremented
        $inv->refresh();
        $this->assertEquals($initialStock + $tambahStok, $inv->Jumlah);
    }

    /**
     * Requirement 2 & 4: Bayar Iuran RT creates pending approval for Bendahara
     */
    public function test_bayar_iuran_allows_payment_proof_upload_and_creates_approval(): void
    {
        $tagihan = Pemasukan::withoutGlobalScope('tenant')
            ->where('ID_KK', $this->warga->ID_KK)
            ->where('ID_RT', $this->warga->ID_RT)
            ->first();

        if (!$tagihan) {
            $tagihan = Pemasukan::withoutGlobalScope('tenant')->create([
                'ID_KK' => $this->warga->ID_KK,
                'Kategori' => 'Iuran Warga',
                'ID_RT' => $this->warga->ID_RT,
                'nama_tagihan' => 'Iuran Uji Coba September',
                'nominal' => 75000,
                'tgl_jatuh_tmp' => now()->toDateString(),
                'status_tagihan' => 'Belum Lunas',
            ]);
        }

        $fakeFile = UploadedFile::fake()->image('bukti_transfer.jpg', 600, 400);

        $payload = [
            'tagihan_id' => $tagihan->Tagihan_Id,
            'bukti_bayar' => $fakeFile,
        ];

        $response = $this->actingAs($this->warga, 'warga')->postJson(route('portal.konfirmasi-iuran'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify record in APPROVAL table for Bendahara
        $this->assertDatabaseHas('APPROVAL', [
            'tabel_sumber' => 'PEMASUKAN',
            'id_referensi' => (string) $tagihan->Tagihan_Id,
            'Admin_ID' => 3,
        ]);
    }

    /**
     * Peminjaman Alat & Inventaris RT creates approval ticket
     */
    public function test_pinjam_inventaris_creates_approval_ticket(): void
    {
        $inv = Inventaris::withoutGlobalScope('tenant')->first();

        $payload = [
            'inventaris_id' => $inv->Inventaris_ID,
            'aset' => $inv->Nama_Barang,
            'tgl_mulai' => now()->addDay()->toDateString(),
            'tgl_selesai' => now()->addDays(2)->toDateString(),
            'tujuan' => 'Acara syukuran keluarga',
        ];

        $response = $this->actingAs($this->warga, 'warga')->postJson(route('portal.pinjam-aset'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('APPROVAL', [
            'tabel_sumber' => 'INVENTARIS',
            'id_referensi' => (string) $inv->Inventaris_ID,
        ]);
    }

    /**
     * Sampaikan Aspirasi saves to ASPIRASI table
     */
    public function test_sampaikan_aspirasi_saves_to_aspirasi_table(): void
    {
        $payload = [
            'kategori' => 'Fasilitas',
            'judul' => 'Lampu Jalan Balai RT',
            'isi' => 'Mohon lampu di depan balai RT diperbaiki karena padam.',
        ];

        $response = $this->actingAs($this->warga, 'warga')->postJson(route('portal.submit-aspirasi'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('ASPIRASI', [
            'NIK' => $this->warga->NIK,
        ]);
    }

    /**
     * Edit Profil Warga Mandiri
     */
    public function test_edit_profil_updates_citizen_information(): void
    {
        $payload = [
            'nama_lengkap' => $this->warga->Nama_Lengkap . ' Updated',
            'no_telp' => '089988776655',
            'password' => 'NewSecret2026!',
            'password_confirmation' => 'NewSecret2026!',
        ];

        $response = $this->actingAs($this->warga, 'warga')->postJson(route('portal.edit-profil'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->warga->refresh();
        $this->assertEquals($payload['nama_lengkap'], $this->warga->Nama_Lengkap);
        $this->assertEquals('089988776655', $this->warga->no_telp);
    }
}
