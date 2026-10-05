<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Warga;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use Illuminate\Support\Facades\Auth;

class OfficerAccessPermissionsTest extends TestCase
{
    use DatabaseMigrations;

    public function test_check_all_officer_roles_access_and_dual_session(): void
    {
        $this->seed(ErdRuangWargaSeeder::class);

        $uris = [
            '/admin',
            '/admin/approvals',
            '/admin/wargas',
            '/admin/kartu-keluargas',
            '/admin/pemasukans',
            '/admin/pengeluarans',
            '/admin/inventaris',
            '/admin/kegiatans',
            '/admin/surats',
            '/admin/aspirasis',
            '/admin/admins',
            '/admin/data-rts',
        ];

        // 1. Test Ketua RT (admin_rt) - All operational routes
        $adminRt = Admin::where('role', 'admin_rt')->first();
        $adminRtUris = [
            '/admin',
            '/admin/approvals',
            '/admin/wargas',
            '/admin/kartu-keluargas',
            '/admin/pemasukans',
            '/admin/pengeluarans',
            '/admin/inventaris',
            '/admin/kegiatans',
            '/admin/surats',
            '/admin/aspirasis',
        ];
        foreach ($adminRtUris as $uri) {
            $res = $this->actingAs($adminRt, 'web')->get($uri);
            $this->assertEquals(200, $res->getStatusCode(), "Ketua RT should have access to {$uri}");
        }

        // 2. Test Bendahara (Finance, Assets, Approvals, Activities, Aspirations)
        $bendahara = Admin::where('role', 'bendahara')->first();
        foreach (['/admin', '/admin/approvals', '/admin/pemasukans', '/admin/pengeluarans', '/admin/inventaris', '/admin/kegiatans', '/admin/aspirasis'] as $uri) {
            $res = $this->actingAs($bendahara, 'web')->get($uri);
            $this->assertEquals(200, $res->getStatusCode(), "Bendahara should have access to {$uri}");
        }

        // 3. Test Sekretaris (Population, Letters, Assets, Approvals, Activities, Aspirations)
        $sekretaris = Admin::where('role', 'sekretaris')->first();
        foreach (['/admin', '/admin/approvals', '/admin/wargas', '/admin/kartu-keluargas', '/admin/surats', '/admin/kegiatans', '/admin/inventaris', '/admin/aspirasis'] as $uri) {
            $res = $this->actingAs($sekretaris, 'web')->get($uri);
            $this->assertEquals(200, $res->getStatusCode(), "Sekretaris should have access to {$uri}");
        }

        // 4. Test Dual Session (Warga in Tab 2 + Admin RT in Tab 1)
        $warga = Warga::first();
        Auth::guard('warga')->login($warga);
        Auth::guard('web')->login($adminRt);

        foreach ($adminRtUris as $uri) {
            $res = $this->get($uri);
            $this->assertEquals(200, $res->getStatusCode(), "Dual session: Admin should still access {$uri} without 403");
        }
    }
}
