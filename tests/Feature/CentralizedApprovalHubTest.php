<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use App\Models\Admin;
use App\Models\Approval;
use App\Models\Surat;
use App\Models\Pemasukan;
use App\Filament\Resources\Approvals\Pages\ListApprovals;
use Livewire\Livewire;
use Illuminate\Support\Facades\DB;

class CentralizedApprovalHubTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);
    }

    /**
     * Requirement 1: Route '/cms/approvals' redirects to Filament '/admin/approvals', accessible by authenticated admin.
     */
    public function test_approvals_route_accessible_by_authenticated_admin(): void
    {
        $admin = Admin::where('role', 'admin_rt')->first();
        $this->actingAs($admin, 'web');

        // /cms/approvals redirects to /admin/approvals in the new Filament architecture
        $redirectResponse = $this->get('/cms/approvals');
        $redirectResponse->assertRedirect('/admin/approvals');

        $response = $this->get('/admin/approvals');
        $response->assertStatus(200);
        $response->assertSee('Pusat Approval');
        $response->assertSee('Filter Status');
        $response->assertSee('Filter Kategori');
    }

    /**
     * Requirement 1: Table category and status filtering.
     */
    public function test_dynamic_filter_tabs_and_categories(): void
    {
        $admin = Admin::where('role', 'admin_rt')->first();
        $this->actingAs($admin, 'web');

        Livewire::test(ListApprovals::class)
            ->assertStatus(200)
            ->filterTable('tabel_sumber', 'SURAT')
            ->assertSee('SURAT')
            ->filterTable('tabel_sumber', 'PEMASUKAN')
            ->assertSee('PEMASUKAN');
    }

    /**
     * Requirement 1: Search by applicant name, perihal, or source detail.
     */
    public function test_livewire_search_by_applicant_or_reference_id(): void
    {
        $admin = Admin::where('role', 'admin_rt')->first();
        $this->actingAs($admin, 'web');

        $approval = Approval::first();
        $this->assertNotNull($approval);

        Livewire::test(ListApprovals::class)
            ->searchTable($approval->source_detail)
            ->assertSee($approval->source_detail);
    }

    /**
     * Requirement 2: Approval Table displays required columns.
     */
    public function test_approval_table_displays_required_columns(): void
    {
        $admin = Admin::where('role', 'admin_rt')->first();
        $this->actingAs($admin, 'web');

        Livewire::test(ListApprovals::class)
            ->assertCanRenderTableColumn('id_approval')
            ->assertCanRenderTableColumn('tabel_sumber')
            ->assertCanRenderTableColumn('source_detail')
            ->assertCanRenderTableColumn('catatan')
            ->assertCanRenderTableColumn('status')
            ->assertCanRenderTableColumn('tgl_approval');
    }

    /**
     * Requirement 3: Approve and Reject table actions update status atomically.
     */
    public function test_approval_and_reject_actions(): void
    {
        $admin = Admin::where('role', 'admin_rt')->first();
        $this->actingAs($admin, 'web');

        // 1. Test Approve
        $pending1 = Approval::where('status', 'Menunggu')->first();
        $this->assertNotNull($pending1);

        Livewire::test(ListApprovals::class)
            ->callTableAction('approve', $pending1)
            ->assertHasNoTableActionErrors();

        $this->assertTrue(in_array($pending1->fresh()->status, ['Disetujui', 'approved']));
        $this->assertNotNull($pending1->fresh()->tgl_approval);

        // 2. Test Reject
        $pending2 = Approval::where('status', 'Menunggu')->first();
        if ($pending2) {
            Livewire::test(ListApprovals::class)
                ->callTableAction('reject', $pending2, [
                    'catatan' => 'Bukti transfer tidak terbaca / buram',
                ])
                ->assertHasNoTableActionErrors();

            $this->assertTrue(in_array($pending2->fresh()->status, ['Ditolak', 'rejected']));
            $this->assertNotNull($pending2->fresh()->tgl_approval);
        }
    }
}
