<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Admin;
use App\Models\Aspirasi;
use App\Models\DataRt;
use App\Models\Inventaris;
use App\Models\KartuKeluarga;
use App\Models\Kegiatan;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Surat;
use App\Models\Warga;
use Database\Seeders\ErdRuangWargaSeeder;

class CmsComprehensiveAuditTest extends TestCase
{
    use DatabaseMigrations;

    protected Admin $superAdmin;
    protected Admin $ketuaRt;
    protected Admin $bendahara;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);

        $this->superAdmin = Admin::where('role', 'super_admin')->first() ?? Admin::first();
        $this->ketuaRt = Admin::where('role', 'admin_rt')->first() ?? Admin::first();
        $this->bendahara = Admin::where('role', 'bendahara')->first() ?? Admin::first();
    }

    public function test_all_resource_index_routes_return_200()
    {
        $routes = [
            '/admin',
            '/admin/approvals',
            '/admin/pemasukans',
            '/admin/pengeluarans',
            '/admin/wargas',
            '/admin/kartu-keluargas',
            '/admin/surats',
            '/admin/kegiatans',
            '/admin/aspirasis',
            '/admin/inventaris',
            '/admin/data-rts',
            '/admin/admins',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->superAdmin, 'web')->get($route);
            $this->assertEquals(200, $response->status(), "Failed asserting route {$route} returns 200. Got: " . $response->status());
        }
    }

    public function test_pemasukan_create_and_edit_forms_render_successfully()
    {
        $createResponse = $this->actingAs($this->superAdmin, 'web')->get('/admin/pemasukans/create');
        $this->assertEquals(200, $createResponse->status(), "Failed opening /admin/pemasukans/create");

        $pemasukan = Pemasukan::first();
        if ($pemasukan) {
            $editResponse = $this->actingAs($this->superAdmin, 'web')->get("/admin/pemasukans/{$pemasukan->Tagihan_Id}/edit");
            $this->assertEquals(200, $editResponse->status(), "Failed opening /admin/pemasukans/{$pemasukan->Tagihan_Id}/edit");
        }
    }

    public function test_pengeluaran_create_and_edit_forms_render_successfully()
    {
        $createResponse = $this->actingAs($this->superAdmin, 'web')->get('/admin/pengeluarans/create');
        $this->assertEquals(200, $createResponse->status(), "Failed opening /admin/pengeluarans/create");

        $pengeluaran = Pengeluaran::first();
        if ($pengeluaran) {
            $editResponse = $this->actingAs($this->superAdmin, 'web')->get("/admin/pengeluarans/{$pengeluaran->Pengeluaran_id}/edit");
            $this->assertEquals(200, $editResponse->status(), "Failed opening /admin/pengeluarans/{$pengeluaran->Pengeluaran_id}/edit");
        }
    }

    public function test_warga_create_and_edit_forms_render_successfully()
    {
        $createResponse = $this->actingAs($this->superAdmin, 'web')->get('/admin/wargas/create');
        $this->assertEquals(200, $createResponse->status(), "Failed opening /admin/wargas/create");

        $warga = Warga::first();
        if ($warga) {
            $editResponse = $this->actingAs($this->superAdmin, 'web')->get("/admin/wargas/{$warga->NIK}/edit");
            $this->assertEquals(200, $editResponse->status(), "Failed opening /admin/wargas/{$warga->NIK}/edit");
        }
    }

    public function test_surat_create_and_edit_forms_render_successfully()
    {
        $createResponse = $this->actingAs($this->superAdmin, 'web')->get('/admin/surats/create');
        $this->assertEquals(200, $createResponse->status(), "Failed opening /admin/surats/create");

        $surat = Surat::first();
        if ($surat) {
            $editResponse = $this->actingAs($this->superAdmin, 'web')->get("/admin/surats/{$surat->surat_id}/edit");
            $this->assertEquals(200, $editResponse->status(), "Failed opening /admin/surats/{$surat->surat_id}/edit");
        }
    }

    public function test_kegiatan_create_and_edit_forms_render_successfully()
    {
        $createResponse = $this->actingAs($this->superAdmin, 'web')->get('/admin/kegiatans/create');
        $this->assertEquals(200, $createResponse->status(), "Failed opening /admin/kegiatans/create");

        $kegiatan = Kegiatan::first();
        if ($kegiatan) {
            $editResponse = $this->actingAs($this->superAdmin, 'web')->get("/admin/kegiatans/{$kegiatan->kegiatan_id}/edit");
            $this->assertEquals(200, $editResponse->status(), "Failed opening /admin/kegiatans/{$kegiatan->kegiatan_id}/edit");
        }
    }

    public function test_aspirasi_create_and_edit_forms_render_successfully()
    {
        $createResponse = $this->actingAs($this->superAdmin, 'web')->get('/admin/aspirasis/create');
        $this->assertEquals(200, $createResponse->status(), "Failed opening /admin/aspirasis/create");

        $aspirasi = Aspirasi::first();
        if ($aspirasi) {
            $editResponse = $this->actingAs($this->superAdmin, 'web')->get("/admin/aspirasis/{$aspirasi->id_aspirasi}/edit");
            $this->assertEquals(200, $editResponse->status(), "Failed opening /admin/aspirasis/{$aspirasi->id_aspirasi}/edit");
        }
    }

    public function test_form_and_infolist_schemas_configure_correctly()
    {
        $schema = new \Filament\Schemas\Schema();

        $this->assertNotEmpty(\App\Filament\Resources\KartuKeluargas\Schemas\KartuKeluargaForm::configure(clone $schema)->getComponents());
        $this->assertNotEmpty(\App\Filament\Resources\KartuKeluargas\Schemas\KartuKeluargaInfolist::configure(clone $schema)->getComponents());

        $this->assertNotEmpty(\App\Filament\Resources\Inventaris\Schemas\InventarisForm::configure(clone $schema)->getComponents());
        $this->assertNotEmpty(\App\Filament\Resources\Inventaris\Schemas\InventarisInfolist::configure(clone $schema)->getComponents());

        $this->assertNotEmpty(\App\Filament\Resources\DataRts\Schemas\DataRtForm::configure(clone $schema)->getComponents());
        $this->assertNotEmpty(\App\Filament\Resources\DataRts\Schemas\DataRtInfolist::configure(clone $schema)->getComponents());

        $this->assertNotEmpty(\App\Filament\Resources\Pengeluarans\Schemas\PengeluaranForm::configure(clone $schema)->getComponents());
        $this->assertNotEmpty(\App\Filament\Resources\Surats\Schemas\SuratForm::configure(clone $schema)->getComponents());
        $this->assertNotEmpty(\App\Filament\Resources\Aspirasis\Schemas\AspirasiForm::configure(clone $schema)->getComponents());
        $this->assertNotEmpty(\App\Filament\Resources\Pemasukans\Schemas\PemasukanForm::configure(clone $schema)->getComponents());
        $this->assertNotEmpty(\App\Filament\Resources\Admins\Schemas\AdminForm::configure(clone $schema)->getComponents());
    }

    public function test_officer_roles_access_resources()
    {
        // Ketua RT can access dashboard, approvals, wargas, surats, pemasukans, pengeluarans, kegiatans, aspirasis, inventaris, admins
        $this->actingAs($this->ketuaRt, 'web')->get('/admin')->assertStatus(200);
        $this->actingAs($this->ketuaRt, 'web')->get('/admin/approvals')->assertStatus(200);
        $this->actingAs($this->ketuaRt, 'web')->get('/admin/wargas')->assertStatus(200);
        $this->actingAs($this->ketuaRt, 'web')->get('/admin/surats')->assertStatus(200);
        $this->actingAs($this->ketuaRt, 'web')->get('/admin/pemasukans')->assertStatus(200);
        $this->actingAs($this->ketuaRt, 'web')->get('/admin/pengeluarans')->assertStatus(200);
        $this->actingAs($this->ketuaRt, 'web')->get('/admin/kegiatans')->assertStatus(200);
        $this->actingAs($this->ketuaRt, 'web')->get('/admin/aspirasis')->assertStatus(200);
        $this->actingAs($this->ketuaRt, 'web')->get('/admin/inventaris')->assertStatus(200);
        $this->actingAs($this->ketuaRt, 'web')->get('/admin/admins')->assertStatus(200);

        // Bendahara can access dashboard, pemasukans, pengeluarans, approvals, inventaris
        $this->actingAs($this->bendahara, 'web')->get('/admin')->assertStatus(200);
        $this->actingAs($this->bendahara, 'web')->get('/admin/pemasukans')->assertStatus(200);
        $this->actingAs($this->bendahara, 'web')->get('/admin/pengeluarans')->assertStatus(200);
        $this->actingAs($this->bendahara, 'web')->get('/admin/approvals')->assertStatus(200);
        $this->actingAs($this->bendahara, 'web')->get('/admin/inventaris')->assertStatus(200);
    }
}
