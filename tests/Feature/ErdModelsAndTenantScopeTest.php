<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\DataRt;
use App\Models\Admin;
use App\Models\Approval;
use App\Models\KartuKeluarga;
use App\Models\Warga;
use App\Models\Surat;
use App\Models\Aspirasi;
use App\Models\ResponAspirasi;
use App\Models\Kegiatan;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Inventaris;
use App\Models\PembelianInventaris;
use Illuminate\Support\Facades\Schema;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;

class ErdModelsAndTenantScopeTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);
    }
    /**
     * Requirement 1 & 2: Test that all 13 exact table names exist and ADMIN.ID_RT is nullable.
     */
    public function test_all_13_tables_exist_with_exact_naming_and_admin_id_rt_is_nullable(): void
    {
        $tables = [
            'DATA_RT',
            'ADMIN',
            'APPROVAL',
            'KARTU_KELUARGA',
            'WARGA',
            'SURAT',
            'ASPIRASI',
            'RESPON_ASPIRASI',
            'KEGIATAN',
            'PEMASUKAN',
            'PENGELUARAN',
            'INVENTARIS',
            'PEMBELIAN_INVENTARIS',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table {$table} must exist in the database.");
        }

        // Test ADMIN table columns and ID_RT nullable
        $this->assertTrue(Schema::hasColumn('ADMIN', 'ID_RT'), "ADMIN table must have ID_RT column.");
        $superAdmin = Admin::where('role', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->ID_RT = null;
            $superAdmin->save();
            $this->assertNull($superAdmin->fresh()->ID_RT, "Super Admin ID_RT must be nullable.");
        }
    }

    /**
     * Requirement 3: Test primary key definitions.
     */
    public function test_primary_keys_types(): void
    {
        $warga = new Warga();
        $this->assertEquals('NIK', $warga->getKeyName());
        $this->assertFalse($warga->getIncrementing());
        $this->assertEquals('string', $warga->getKeyType());

        $kk = new KartuKeluarga();
        $this->assertEquals('ID_KK', $kk->getKeyName());
        $this->assertFalse($kk->getIncrementing());
        $this->assertEquals('string', $kk->getKeyType());

        $rt = new DataRt();
        $this->assertEquals('ID_RT', $rt->getKeyName());
        $this->assertTrue($rt->getIncrementing());
        $this->assertEquals('int', $rt->getKeyType());

        $admin = new Admin();
        $this->assertEquals('Admin_ID', $admin->getKeyName());
        $this->assertTrue($admin->getIncrementing());
        $this->assertEquals('int', $admin->getKeyType());
    }

    /**
     * Requirement 4: Test all Eloquent relationships on models.
     */
    public function test_eloquent_relationships(): void
    {
        $rt = DataRt::first();
        $this->assertNotNull($rt);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $rt->warga);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $rt->admins);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $rt->pemasukan);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $rt->kegiatan);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $rt->inventaris);

        $admin = Admin::whereNotNull('ID_RT')->first();
        if ($admin) {
            $this->assertNotNull($admin->dataRt);
            $this->assertNotNull($admin->rt);
        }

        $kk = KartuKeluarga::first();
        $this->assertNotNull($kk);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $kk->warga);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $kk->pemasukan);

        $warga = Warga::first();
        $this->assertNotNull($warga);
        if ($warga->ID_KK) {
            $this->assertNotNull($warga->kartuKeluarga);
        }
        if ($warga->ID_RT) {
            $this->assertNotNull($warga->dataRt);
        }
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $warga->surat);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $warga->aspirasi);

        $surat = Surat::first();
        if ($surat) {
            $this->assertNotNull($surat->warga);
        }

        $aspirasi = Aspirasi::first();
        if ($aspirasi) {
            $this->assertNotNull($aspirasi->warga);
        }

        $respon = ResponAspirasi::first();
        if ($respon) {
            $this->assertNotNull($respon->aspirasi);
        }

        $inv = Inventaris::first();
        if ($inv) {
            $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $inv->pembelian);
        }

        $pengeluaran = Pengeluaran::first();
        if ($pengeluaran) {
            $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $pengeluaran->pembelianInventaris);
        }
    }

    /**
     * Requirement 5: Test TenantScope Trait on WARGA, PEMASUKAN, KEGIATAN, INVENTARIS.
     */
    public function test_tenant_scope_filters_by_id_rt_unless_super_admin(): void
    {
        // 1. Unauthenticated -> returns all records
        auth()->logout();
        $unauthWargaCount = Warga::count();
        $this->assertGreaterThan(0, $unauthWargaCount);

        // 2. Authenticated as Admin RT (ID_RT = 1, role = 'admin_rt')
        $adminRt = Admin::where('role', 'admin_rt')->where('ID_RT', 1)->first();
        $this->assertNotNull($adminRt);
        auth()->login($adminRt);

        $scopedWargas = Warga::all();
        foreach ($scopedWargas as $w) {
            $this->assertEquals(1, $w->ID_RT, "TenantScope must filter Warga by ID_RT = 1");
        }

        $scopedPemasukans = Pemasukan::all();
        foreach ($scopedPemasukans as $p) {
            $this->assertEquals(1, $p->ID_RT, "TenantScope must filter Pemasukan by ID_RT = 1");
        }

        $scopedKegiatans = Kegiatan::all();
        foreach ($scopedKegiatans as $k) {
            $this->assertEquals(1, $k->ID_RT, "TenantScope must filter Kegiatan by ID_RT = 1");
        }

        $scopedInventaris = Inventaris::all();
        foreach ($scopedInventaris as $i) {
            $this->assertEquals(1, $i->ID_RT, "TenantScope must filter Inventaris by ID_RT = 1");
        }

        // 3. Authenticated as Super Admin (role = 'super_admin') -> should NOT filter
        $superAdmin = Admin::where('role', 'super_admin')->first();
        $this->assertNotNull($superAdmin);
        auth()->login($superAdmin);

        $superAdminWargaCount = Warga::count();
        $this->assertEquals($unauthWargaCount, $superAdminWargaCount, "Super Admin must not be filtered by TenantScope.");

        // 4. Test withoutTenant scope
        auth()->login($adminRt);
        $withoutTenantCount = Warga::withoutTenant()->count();
        $this->assertEquals($unauthWargaCount, $withoutTenantCount, "withoutTenant scope must bypass tenant filtering.");

        auth()->logout();
    }
}
