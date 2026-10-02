<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\Warga;
use App\Models\Admin;
use App\Models\Kegiatan;
use Database\Seeders\ErdRuangWargaSeeder;
use Illuminate\Support\Carbon;

class CitizenPortalDedicatedPagesTest extends TestCase
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

    public function test_guest_is_redirected_to_login_for_all_dedicated_pages()
    {
        // Layanan Mandiri
        $this->get('/portal/layanan')->assertRedirect('/login');
        $this->get('/layanan')->assertRedirect(route('portal.layanan'));

        // Agenda Lingkungan
        $this->get('/portal/agenda')->assertRedirect('/login');
        $this->get('/agenda')->assertRedirect(route('portal.agenda'));
        $this->get('/kegiatan')->assertRedirect(route('portal.agenda'));

        // Transparansi Kas
        $this->get('/portal/transparansi')->assertRedirect('/login');
        $this->get('/transparansi')->assertRedirect(route('portal.transparansi'));
    }

    public function test_authenticated_warga_can_access_layanan_mandiri_page()
    {
        $response = $this->actingAs($this->warga, 'warga')->get('/portal/layanan');

        $response->assertStatus(200);
        $response->assertSee('Pusat Layanan Mandiri');
        $response->assertSee('Surat Pengantar RT Online');
        $response->assertSee('Bayar Iuran Bulanan RT');
        $response->assertSee($this->warga->Nama_Lengkap);
    }

    public function test_authenticated_warga_can_access_agenda_lingkungan_page()
    {
        $response = $this->actingAs($this->warga, 'warga')->get('/portal/agenda');

        $response->assertStatus(200);
        $response->assertSee('Agenda Kegiatan');
        $response->assertSee('Jadwal Ronda Malam Siskamling');
        $response->assertSee('Usulkan Ide Kegiatan Baru');
    }

    public function test_authenticated_warga_can_submit_kegiatan_proposal()
    {
        $response = $this->actingAs($this->warga, 'warga')->post('/portal/usul-kegiatan', [
            'nama_kegiatan' => 'Senam Pagi Kompak Warga',
            'deskripsi' => 'Senam bersama instruktur di lapangan balai warga RT 05 setiap minggu pagi.',
            'lokasi' => 'Lapangan Balai Warga',
            'tgl_kegiatan' => Carbon::now()->addDays(5)->toDateString(),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('KEGIATAN', [
            'nama_kegiatan' => 'Senam Pagi Kompak Warga',
            'lokasi' => 'Lapangan Balai Warga',
            'ID_RT' => $this->warga->ID_RT,
        ]);
    }

    public function test_authenticated_warga_can_access_transparansi_kas_page()
    {
        $response = $this->actingAs($this->warga, 'warga')->get('/portal/transparansi');

        $response->assertStatus(200);
        $response->assertSee('Transparansi Kas');
        $response->assertSee('Total Saldo Kas RT');
        $response->assertSee('Masuk Bulan Ini');
        $response->assertSee('Belanja Bulan Ini');
        $response->assertSee('Alokasi Pengeluaran Kas RT');
        $response->assertSee('Rekening Resmi Kas Lingkungan');
    }

    public function test_admin_can_preview_all_dedicated_pages()
    {
        $this->actingAs($this->admin, 'web')->get('/portal/layanan')->assertStatus(200);
        $this->actingAs($this->admin, 'web')->get('/portal/agenda')->assertStatus(200);
        $this->actingAs($this->admin, 'web')->get('/portal/transparansi')->assertStatus(200);
    }
}
