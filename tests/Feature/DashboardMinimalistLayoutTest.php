<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use App\Models\Admin;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\ArusKasChartWidget;
use App\Filament\Widgets\StatusIuranDonutWidget;
use App\Filament\Widgets\DemografiWargaChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\LatestApprovalsTableWidget;

class DashboardMinimalistLayoutTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);
    }

    public function test_dashboard_columns_and_widget_spans_are_minimalist_and_responsive(): void
    {
        $admin = Admin::where('role', 'super_admin')->first();
        if (!$admin) {
            $admin = Admin::factory()->create(['role' => 'super_admin']);
        }

        $this->actingAs($admin, 'web');

        $response = $this->get('/admin');
        $response->assertStatus(200);

        // Verify Dashboard columns configuration
        $dashboard = new Dashboard();
        $columns = $dashboard->getColumns();
        $this->assertIsArray($columns);
        $this->assertEquals(12, $columns['xl']);
        $this->assertEquals(4, $columns['md']);

        // Verify Arus Kas Chart configuration
        $arusKas = new ArusKasChartWidget();
        $arusKasSpan = $arusKas->getColumnSpan();
        $this->assertEquals(6, $arusKasSpan['xl']);
        $this->assertEquals(4, $arusKasSpan['md']);

        // Verify Status Iuran Donut configuration
        $statusIuran = new StatusIuranDonutWidget();
        $statusIuranSpan = $statusIuran->getColumnSpan();
        $this->assertEquals(3, $statusIuranSpan['xl']);
        $this->assertEquals(2, $statusIuranSpan['md']);

        // Verify Demografi Warga Chart configuration
        $demografi = new DemografiWargaChartWidget();
        $demografiSpan = $demografi->getColumnSpan();
        $this->assertEquals(3, $demografiSpan['xl']);
        $this->assertEquals(2, $demografiSpan['md']);

        // Total width on xl: 6 + 3 + 3 = 12 (perfect single row layout)
        $this->assertEquals(12, $arusKasSpan['xl'] + $statusIuranSpan['xl'] + $demografiSpan['xl']);

        // Total width on md: 2 + 2 = 4 (status iuran + demografi side-by-side)
        $this->assertEquals(4, $statusIuranSpan['md'] + $demografiSpan['md']);
    }
}
