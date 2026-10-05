<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class CmsNavigationTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);
    }

    /**
     * Test admin navigating between multiple CMS pages after standard login.
     */
    public function test_super_admin_can_navigate_between_cms_pages_without_being_kicked_to_login(): void
    {
        $loginRes = $this->post('/login', [
            'identity' => 'superadmin',
            'password' => 'Rahasia123!',
        ]);

        $loginRes->assertRedirect('/cms');

        // CMS redirect to /admin
        $cmsRes = $this->get('/cms');
        $cmsRes->assertRedirect('/admin');

        // /admin dashboard
        $adminRes = $this->get('/admin');
        $adminRes->assertStatus(200);

        // Navigate to Data RT
        $dataRtRes = $this->get('/admin/data-rts');
        $dataRtRes->assertStatus(200);

        // Navigate to Wargas
        $wargasRes = $this->get('/admin/wargas');
        $wargasRes->assertStatus(200);

        // Navigate to Pemasukans
        $pemasukanRes = $this->get('/admin/pemasukans');
        $pemasukanRes->assertStatus(200);

        // Navigate to Surats
        $suratsRes = $this->get('/admin/surats');
        $suratsRes->assertStatus(200);

        // Navigate to Approvals
        $approvalsRes = $this->get('/admin/approvals');
        $approvalsRes->assertStatus(200);
    }

    /**
     * Test Ketua RT navigating between permitted CMS pages after standard login.
     */
    public function test_admin_rt_can_navigate_between_cms_pages_without_being_kicked_to_login(): void
    {
        $loginRes = $this->post('/login', [
            'identity' => 'ketua_rt05',
            'password' => 'Rahasia123!',
        ]);

        $loginRes->assertRedirect('/cms');

        $adminRes = $this->get('/admin');
        $adminRes->assertStatus(200);

        $wargasRes = $this->get('/admin/wargas');
        $wargasRes->assertStatus(200);

        $pemasukanRes = $this->get('/admin/pemasukans');
        $pemasukanRes->assertStatus(200);

        $suratsRes = $this->get('/admin/surats');
        $suratsRes->assertStatus(200);
    }

    /**
     * Test Bendahara navigating between permitted CMS pages after standard login.
     */
    public function test_bendahara_can_navigate_between_cms_pages_without_being_kicked_to_login(): void
    {
        $loginRes = $this->post('/login', [
            'identity' => 'bendahara_rt05',
            'password' => 'Rahasia123!',
        ]);

        $loginRes->assertRedirect('/cms');

        $adminRes = $this->get('/admin');
        $adminRes->assertStatus(200);

        $pemasukanRes = $this->get('/admin/pemasukans');
        $pemasukanRes->assertStatus(200);

        $inventarisRes = $this->get('/admin/inventaris');
        $inventarisRes->assertStatus(200);
    }

    /**
     * Test Sekretaris navigating between permitted CMS pages after standard login.
     */
    public function test_sekretaris_can_navigate_between_cms_pages_without_being_kicked_to_login(): void
    {
        $loginRes = $this->post('/login', [
            'identity' => 'sekretaris_rt05',
            'password' => 'Rahasia123!',
        ]);

        $loginRes->assertRedirect('/cms');

        $adminRes = $this->get('/admin');
        $adminRes->assertStatus(200);

        $wargasRes = $this->get('/admin/wargas');
        $wargasRes->assertStatus(200);

        $suratsRes = $this->get('/admin/surats');
        $suratsRes->assertStatus(200);
    }

    /**
     * Test Livewire login and table pagination without redirecting to login.
     */
    public function test_livewire_sso_login_and_table_pagination(): void
    {
        \Livewire\Livewire::test('auth.login-sso')
            ->set('identity', 'ketua_rt05')
            ->set('password', 'Rahasia123!')
            ->call('login')
            ->assertRedirect('/cms');

        $this->assertTrue(Auth::guard('web')->check());
        $this->assertEquals('admin_rt', Auth::guard('web')->user()->role);

        $admin = Auth::guard('web')->user();

        // Test Livewire table component pagination
        \Livewire\Livewire::actingAs($admin, 'web')
            ->test(\App\Filament\Resources\Surats\Pages\ListSurats::class)
            ->assertStatus(200)
            ->call('nextPage')
            ->assertStatus(200);
    }

    /**
     * Test what happens when Bendahara accesses Wargas.
     */
    public function test_bendahara_accessing_wargas(): void
    {
        $bendahara = Admin::where('role', 'bendahara')->first();
        $res = $this->actingAs($bendahara, 'web')->get('/admin/wargas');
        dump('Bendahara /admin/wargas status: ' . $res->getStatusCode());
        if ($res->isRedirect()) {
            dump('Redirected to: ' . $res->headers->get('Location'));
        }
    }

    /**
     * Test what happens when Admin RT accesses Data RT.
     */
    public function test_admin_rt_accessing_data_rts(): void
    {
        $adminRt = Admin::where('role', 'admin_rt')->first();
        $res = $this->actingAs($adminRt, 'web')->get('/admin/data-rts');
        dump('Admin RT /admin/data-rts status: ' . $res->getStatusCode());
        if ($res->isRedirect()) {
            dump('Redirected to: ' . $res->headers->get('Location'));
        }
    }

    /**
     * Test what happens when Warga accesses /admin or /admin/wargas.
     */
    public function test_warga_accessing_admin_pages(): void
    {
        $warga = \App\Models\Warga::first();
        $resAdmin = $this->actingAs($warga, 'warga')->get('/admin');
        dump('Warga /admin status: ' . $resAdmin->getStatusCode());
        if ($resAdmin->isRedirect()) {
            dump('Warga /admin redirected to: ' . $resAdmin->headers->get('Location'));
        }

        $resWargas = $this->actingAs($warga, 'warga')->get('/admin/wargas');
        dump('Warga /admin/wargas status: ' . $resWargas->getStatusCode());
        if ($resWargas->isRedirect()) {
            dump('Warga /admin/wargas redirected to: ' . $resWargas->headers->get('Location'));
        }

        $resCms = $this->actingAs($warga, 'warga')->get('/cms');
        dump('Warga /cms status: ' . $resCms->getStatusCode());
        if ($resCms->isRedirect()) {
            dump('Warga /cms redirected to: ' . $resCms->headers->get('Location'));
        }
    }
}
