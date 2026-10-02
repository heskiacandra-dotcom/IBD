<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\Warga;
use App\Models\Admin;
use App\Models\Aspirasi;
use App\Models\ResponAspirasi;
use Database\Seeders\ErdRuangWargaSeeder;
use Illuminate\Support\Carbon;

class CitizenAspirasiTest extends TestCase
{
    use DatabaseMigrations;

    protected Warga $warga;
    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);

        $this->warga = Warga::first();
        $this->admin = Admin::first();
    }

    public function test_guest_is_redirected_to_login_when_accessing_aspirasi_page()
    {
        $response = $this->get('/portal/aspirasi');
        $response->assertRedirect('/login');

        $shortcutResponse = $this->get('/aspirasi');
        $shortcutResponse->assertRedirect(route('portal.aspirasi'));
    }

    public function test_authenticated_warga_can_access_aspirasi_page()
    {
        $response = $this->actingAs($this->warga, 'warga')->get('/portal/aspirasi');

        $response->assertStatus(200);
        $response->assertSee('Kotak Aspirasi');
        $response->assertSee('Total Masukan');
        $response->assertSee('Ditanggapi Resmi');
        $response->assertSee('Formulir Pengajuan Aspirasi Warga');
        $response->assertSee($this->warga->Nama_Lengkap);
    }

    public function test_warga_can_submit_aspirasi_and_view_it_in_feed()
    {
        $response = $this->actingAs($this->warga, 'warga')->post('/api/aspirasi', [
            'kategori' => 'Fasilitas Umum',
            'judul' => 'Perbaikan Paving Blok Depan Pos Ronda',
            'isi' => 'Paving blok di sekitar pos ronda ambles saat hujan lebat. Mohon perbaikan.',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('ASPIRASI', [
            'NIK' => $this->warga->NIK,
        ]);

        $viewResponse = $this->actingAs($this->warga, 'warga')->get('/portal/aspirasi');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Perbaikan Paving Blok Depan Pos Ronda');
        $viewResponse->assertSee('Paving blok di sekitar pos ronda ambles');
    }

    public function test_official_response_from_rt_is_clearly_displayed()
    {
        $aspirasi = Aspirasi::create([
            'NIK' => $this->warga->NIK,
            'Isi_Aspirasi' => '[Keamanan] Penambahan CCTV Gerbang: Mohon dipasang kamera CCTV tambahan.',
        ]);

        ResponAspirasi::create([
            'id_aspirasi' => $aspirasi->id_aspirasi,
            'nama_responden' => 'Bpk. Sukardi Hartono (Ketua RT 05)',
            'isi_respon' => 'Usulan disetujui, teknisi CCTV akan melakukan instalasi Sabtu pagi.',
            'tanggal_respon' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->warga, 'warga')->get('/portal/aspirasi');

        $response->assertStatus(200);
        $response->assertSee('Penambahan CCTV Gerbang');
        $response->assertSee('Tanggapan Resmi: Bpk. Sukardi Hartono (Ketua RT 05)');
        $response->assertSee('teknisi CCTV akan melakukan instalasi Sabtu pagi');
        $response->assertSee('Ditanggapi Resmi RT');
    }

    public function test_admin_can_also_preview_aspirasi_portal()
    {
        $response = $this->actingAs($this->admin, 'web')->get('/portal/aspirasi');

        $response->assertStatus(200);
        $response->assertSee('Kotak Aspirasi');
    }
}
