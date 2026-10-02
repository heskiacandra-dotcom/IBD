<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Admin;
use App\Models\Warga;
use Database\Seeders\ErdRuangWargaSeeder;

class FilamentAdminPanelTest extends TestCase
{
    use DatabaseMigrations;

    protected Admin $admin;
    protected Warga $warga;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);

        $this->admin = Admin::first();
        $this->warga = Warga::first();
    }

    public function test_guest_is_redirected_to_unified_login_page()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_admin_login_url_redirects_to_unified_login_page()
    {
        $response = $this->get('/admin/login');
        $response->assertRedirect('/login');

        $unifiedLogin = $this->get('/login');
        $unifiedLogin->assertStatus(200);
        $unifiedLogin->assertSee('RuangWarga');
    }

    public function test_authenticated_admin_can_access_filament_dashboard()
    {
        $response = $this->actingAs($this->admin, 'web')->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Smart RT');
        $response->assertSee('Dashboard');
    }

    public function test_authenticated_admin_can_access_filament_resources()
    {
        // Warga Resource Table
        $this->actingAs($this->admin, 'web')->get('/admin/wargas')->assertStatus(200);

        // Surat Resource Table
        $this->actingAs($this->admin, 'web')->get('/admin/surats')->assertStatus(200);

        // Pemasukan Resource Table
        $this->actingAs($this->admin, 'web')->get('/admin/pemasukans')->assertStatus(200);

        // Pengeluaran Resource Table
        $this->actingAs($this->admin, 'web')->get('/admin/pengeluarans')->assertStatus(200);

        // Kegiatan Resource Table
        $this->actingAs($this->admin, 'web')->get('/admin/kegiatans')->assertStatus(200);

        // Aspirasi Resource Table
        $this->actingAs($this->admin, 'web')->get('/admin/aspirasis')->assertStatus(200);

        // Approval Resource Table (Pusat Approval)
        $this->actingAs($this->admin, 'web')->get('/admin/approvals')->assertStatus(200);

        // Inventaris Resource Table (Inventaris Aset)
        $this->actingAs($this->admin, 'web')->get('/admin/inventaris')->assertStatus(200);

        // Kartu Keluarga Resource Table
        $this->actingAs($this->admin, 'web')->get('/admin/kartu-keluargas')->assertStatus(200);

        // Data RT Resource Table (Super Admin)
        $this->actingAs($this->admin, 'web')->get('/admin/data-rts')->assertStatus(200);

        // Admin Resource Table (Super Admin)
        $this->actingAs($this->admin, 'web')->get('/admin/admins')->assertStatus(200);
    }

    public function test_admin_can_login_via_unified_login_page()
    {
        $response = $this->post('/login', [
            'identity' => 'ketua_rt05',
            'password' => 'Rahasia123!',
        ]);

        $response->assertRedirect('/cms');
        $this->get('/cms')->assertRedirect('/admin');
        $this->assertAuthenticatedAs(Admin::where('Username', 'ketua_rt05')->first(), 'web');
    }

    public function test_superadmin_can_login_and_navigate_between_filament_pages()
    {
        $response = $this->post('/login', [
            'identity' => 'superadmin',
            'password' => 'Rahasia123!',
        ]);

        $response->assertRedirect('/cms');
        $this->get('/cms')->assertRedirect('/admin');

        $adminDashboard = $this->get('/admin');
        $adminDashboard->assertStatus(200);

        // Navigate to Approvals
        $approvalsPage = $this->get('/admin/approvals');
        $approvalsPage->assertStatus(200);

        // Navigate to Data RT
        $dataRtPage = $this->get('/admin/data-rts');
        $dataRtPage->assertStatus(200);

        // Navigate to Inventaris
        $inventarisPage = $this->get('/admin/inventaris');
        $inventarisPage->assertStatus(200);
    }

    public function test_warga_cannot_access_filament_admin_panel()
    {
        $response = $this->actingAs($this->warga, 'warga')->get('/admin');
        $response->assertRedirect('/login');
    }
}

