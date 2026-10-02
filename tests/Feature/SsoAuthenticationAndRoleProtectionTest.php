<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use App\Models\Admin;
use App\Models\Warga;
use Illuminate\Support\Facades\Auth;

class SsoAuthenticationAndRoleProtectionTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);
    }

    /**
     * Requirement 1: Unified Login page in Pure Light Mode renders correctly.
     */
    public function test_login_page_renders_in_pure_light_mode(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('RuangWarga');
        $response->assertSee('SINGLE SIGN-ON');
    }

    /**
     * Requirement 2: Warga login via NIK redirects to Citizen Portal (/portal).
     */
    public function test_warga_login_via_nik_redirects_to_portal(): void
    {
        $response = $this->post('/login', [
            'identity' => '3201051208940001',
            'password' => 'WargaHarmoni2026!',
        ]);

        $response->assertRedirect('/portal');
        $this->assertTrue(Auth::check());
        $this->assertEquals('warga', Auth::user()->role);
        $this->assertEquals('3201051208940001', Auth::user()->NIK);
    }

    /**
     * Requirement 2: Warga login via Username redirects to Citizen Portal (/portal).
     */
    public function test_warga_login_via_username_redirects_to_portal(): void
    {
        $response = $this->post('/login', [
            'identity' => 'hendra_w',
            'password' => 'WargaHarmoni2026!',
        ]);

        $response->assertRedirect('/portal');
        $this->assertTrue(Auth::check());
        $this->assertEquals('warga', Auth::user()->role);
    }

    /**
     * Requirement 2: Super Admin login redirects to CMS Dashboard (/cms/dashboard).
     */
    public function test_super_admin_login_redirects_to_cms_dashboard(): void
    {
        $response = $this->post('/login', [
            'identity' => 'superadmin',
            'password' => 'Rahasia123!',
        ]);

        $response->assertRedirect('/cms');
        $this->assertTrue(Auth::check());
        $this->assertEquals('super_admin', Auth::user()->role);
    }

    /**
     * Requirement 2: Admin RT login redirects to CMS Dashboard (/cms).
     */
    public function test_admin_rt_login_redirects_to_cms_dashboard(): void
    {
        $response = $this->post('/login', [
            'identity' => 'ketua_rt05',
            'password' => 'Rahasia123!',
        ]);

        $response->assertRedirect('/cms');
        $this->assertTrue(Auth::check());
        $this->assertEquals('admin_rt', Auth::user()->role);
    }

    /**
     * Requirement 2: Bendahara login redirects to CMS Dashboard (/cms).
     */
    public function test_bendahara_login_redirects_to_cms_dashboard(): void
    {
        $response = $this->post('/login', [
            'identity' => 'bendahara_rt05',
            'password' => 'Rahasia123!',
        ]);

        $response->assertRedirect('/cms');
        $this->assertTrue(Auth::check());
        $this->assertEquals('bendahara', Auth::user()->role);
    }

    /**
     * Requirement 2: Sekretaris login redirects to CMS Dashboard (/cms).
     */
    public function test_sekretaris_login_redirects_to_cms_dashboard(): void
    {
        $response = $this->post('/login', [
            'identity' => 'sekretaris_rt05',
            'password' => 'Rahasia123!',
        ]);

        $response->assertRedirect('/cms');
        $this->assertTrue(Auth::check());
        $this->assertEquals('sekretaris', Auth::user()->role);
    }

    /**
     * Requirement 2: Invalid credentials fail authentication.
     */
    public function test_invalid_credentials_fail(): void
    {
        $response = $this->post('/login', [
            'identity' => '3201051208940001',
            'password' => 'WrongPassword999!',
        ]);

        $response->assertSessionHasErrors('identity');
        $this->assertFalse(Auth::check());
    }

    /**
     * Requirement 3: Unauthenticated user accessing CMS is redirected to login.
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        Auth::logout();
        $response = $this->get('/cms/dashboard');
        $response->assertRedirect(route('login'));
    }

    /**
     * Requirement 3: Role protection for Bendahara.
     * Ensure Bendahara can only access finance/asset endpoints, not citizens, letters, or master RT.
     */
    public function test_bendahara_role_protection(): void
    {
        $bendahara = Admin::where('role', 'bendahara')->first();
        $this->assertNotNull($bendahara);
        $this->actingAs($bendahara);

        // Allowed: Dashboard, Finance, Inventory redirect to Filament
        $this->get('/cms/dashboard')->assertRedirect('/admin');
        $this->get('/cms/finance')->assertRedirect('/admin/pemasukans');
        $this->get('/cms/inventory')->assertRedirect('/admin/inventaris');

        // Forbidden: Citizens, Letters, Master RT
        $this->get('/cms/citizens')->assertStatus(403);
        $this->get('/cms/letters')->assertStatus(403);
        $this->get('/cms/master-rt')->assertStatus(403);
    }

    /**
     * Requirement 3: Role protection for Sekretaris.
     * Ensure Sekretaris accesses citizens/letters, not finance, inventory, or master RT.
     */
    public function test_sekretaris_role_protection(): void
    {
        $sekretaris = Admin::where('role', 'sekretaris')->first();
        $this->assertNotNull($sekretaris);
        $this->actingAs($sekretaris);

        // Allowed: Dashboard, Citizens, Letters redirect to Filament
        $this->get('/cms/dashboard')->assertRedirect('/admin');
        $this->get('/cms/citizens')->assertRedirect('/admin/wargas');
        $this->get('/cms/letters')->assertRedirect('/admin/surats');

        // Forbidden: Finance, Inventory, Master RT
        $this->get('/cms/finance')->assertStatus(403);
        $this->get('/cms/inventory')->assertStatus(403);
        $this->get('/cms/master-rt')->assertStatus(403);
    }

    /**
     * Requirement 3: Role protection for Super Admin.
     * Ensure Super Admin accesses master RT and all other CMS modules.
     */
    public function test_super_admin_role_protection(): void
    {
        $superAdmin = Admin::where('role', 'super_admin')->first();
        $this->assertNotNull($superAdmin);
        $this->actingAs($superAdmin);

        // Allowed: Master RT and all CMS modules redirect to Filament
        $this->get('/cms/master-rt')->assertRedirect('/admin/data-rts');
        $this->get('/cms/dashboard')->assertRedirect('/admin');
        $this->get('/cms/finance')->assertRedirect('/admin/pemasukans');
        $this->get('/cms/inventory')->assertRedirect('/admin/inventaris');
        $this->get('/cms/citizens')->assertRedirect('/admin/wargas');
        $this->get('/cms/letters')->assertRedirect('/admin/surats');
    }

    /**
     * Requirement 3: Admin RT is forbidden from Master RT (Super Admin only).
     */
    public function test_admin_rt_cannot_access_master_rt(): void
    {
        $adminRt = Admin::where('role', 'admin_rt')->first();
        $this->assertNotNull($adminRt);
        $this->actingAs($adminRt);

        $this->get('/cms/master-rt')->assertStatus(403);
        // But can access operational modules redirected to Filament
        $this->get('/cms/dashboard')->assertRedirect('/admin');
        $this->get('/cms/finance')->assertRedirect('/admin/pemasukans');
        $this->get('/cms/citizens')->assertRedirect('/admin/wargas');
    }

    /**
     * Requirement 3: Warga role is blocked from CMS and redirected to Citizen Portal.
     */
    public function test_warga_cannot_access_cms(): void
    {
        $warga = Warga::first();
        $this->assertNotNull($warga);
        $this->actingAs($warga);

        $response = $this->get('/cms/dashboard');
        $response->assertRedirect('/portal');
    }

    /**
     * Test Logout clears authentication and redirects to login.
     */
    public function test_logout_clears_session(): void
    {
        $warga = Warga::first();
        $this->actingAs($warga);
        $this->assertTrue(Auth::check());

        $response = $this->post('/logout');
        $response->assertRedirect(route('login'));
        $this->assertFalse(Auth::check());
    }
}
