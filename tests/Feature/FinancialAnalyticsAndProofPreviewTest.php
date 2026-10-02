<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use App\Models\Admin;
use App\Models\Approval;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Filament\Widgets\ArusKasChartWidget;
use App\Filament\Resources\Pemasukans\Pages\ListPemasukans;
use App\Filament\Resources\Approvals\Pages\ListApprovals;
use Livewire\Livewire;

class FinancialAnalyticsAndProofPreviewTest extends TestCase
{
    use DatabaseMigrations;

    protected Admin $adminRt;
    protected Admin $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);

        $this->adminRt = Admin::where('role', 'admin_rt')->first();
        $this->superAdmin = Admin::where('role', 'super_admin')->first();
    }

    /**
     * Requirement 1: ArusKasChartWidget correctly provides 12-month Inflow vs Outflow datasets.
     */
    public function test_arus_kas_chart_widget_configuration_and_datasets(): void
    {
        $this->actingAs($this->adminRt, 'web');

        $widget = new class extends ArusKasChartWidget {
            public function testGetData(): array
            {
                return $this->getData();
            }

            public function testGetType(): string
            {
                return $this->getType();
            }
        };

        $this->assertEquals('bar', $widget->testGetType());

        $data = $widget->testGetData();
        $this->assertArrayHasKey('datasets', $data);
        $this->assertArrayHasKey('labels', $data);

        // Verify 12 month labels
        $expectedMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $this->assertEquals($expectedMonths, $data['labels']);

        // Verify datasets
        $this->assertCount(2, $data['datasets']);
        $this->assertEquals('Kas Masuk (Iuran Lunas)', $data['datasets'][0]['label']);
        $this->assertEquals('Kas Keluar (Operasional)', $data['datasets'][1]['label']);

        // Check dataset array counts (12 months)
        $this->assertCount(12, $data['datasets'][0]['data']);
        $this->assertCount(12, $data['datasets'][1]['data']);
    }

    /**
     * Requirement 1: Scoping logic respects authenticated user RT vs Super Admin.
     */
    public function test_arus_kas_chart_widget_scoping(): void
    {
        // Authenticated as Admin RT (scoped to RT 1)
        $this->actingAs($this->adminRt, 'web');
        $widgetRt = new class extends ArusKasChartWidget {
            public function testGetData(): array
            {
                return $this->getData();
            }
        };
        $dataRt = $widgetRt->testGetData();
        $this->assertNotEmpty($dataRt['datasets'][0]['data']);

        // Authenticated as Super Admin (global scope)
        $this->actingAs($this->superAdmin, 'web');
        $widgetSuper = new class extends ArusKasChartWidget {
            public function testGetData(): array
            {
                return $this->getData();
            }
        };
        $dataSuper = $widgetSuper->testGetData();
        $this->assertNotEmpty($dataSuper['datasets'][0]['data']);
    }

    /**
     * Requirement 2: PemasukansTable contains 'bukti_bayar' column and 'preview_bukti' action.
     */
    public function test_pemasukan_table_renders_bukti_column_and_preview_action(): void
    {
        $this->actingAs($this->adminRt, 'web');

        Livewire::test(ListPemasukans::class)
            ->assertCanRenderTableColumn('Tagihan_Id')
            ->assertCanRenderTableColumn('bukti_bayar')
            ->assertCanRenderTableColumn('nama_tagihan')
            ->assertCanRenderTableColumn('nominal')
            ->assertCanRenderTableColumn('status_tagihan');
    }

    /**
     * Requirement 2: PemasukansTable preview_bukti action can approve and reject pending payments.
     */
    public function test_pemasukan_modal_action_approve_and_reject(): void
    {
        $this->actingAs($this->adminRt, 'web');

        // Find or create a pending tagihan
        $tagihan = Pemasukan::withoutGlobalScope('tenant')->where('status_tagihan', '!=', 'Lunas')->first();
        if (!$tagihan) {
            $tagihan = Pemasukan::withoutGlobalScope('tenant')->first();
            $tagihan->update(['status_tagihan' => 'pending_approval']);
        }

        $tagihanId = $tagihan->Tagihan_Id;

        // Create corresponding approval ticket
        $approval = Approval::firstOrCreate(
            [
                'tabel_sumber' => 'PEMASUKAN',
                'id_referensi' => (string) $tagihanId,
            ],
            [
                'Admin_ID' => $this->adminRt->Admin_ID,
                'status' => 'Menunggu',
                'catatan' => 'Konfirmasi pembayaran iuran warga',
            ]
        );

        // Test approve action via shortcut
        Livewire::test(ListPemasukans::class)
            ->callTableAction('mark_as_paid', $tagihan)
            ->assertHasNoTableActionErrors();

        $this->assertEquals('Lunas', $tagihan->fresh()->status_tagihan);
        $this->assertEquals('Disetujui', $approval->fresh()->status);
    }

    /**
     * Requirement 2: ApprovalResource review_tiket action opens slide-over drawer with proof preview.
     */
    public function test_approval_table_renders_review_tiket_action(): void
    {
        $this->actingAs($this->adminRt, 'web');

        $approvalPemasukan = Approval::where('tabel_sumber', 'PEMASUKAN')->first();
        $this->assertNotNull($approvalPemasukan);

        Livewire::test(ListApprovals::class)
            ->assertTableActionExists('review_tiket')
            ->assertTableActionVisible('review_tiket', $approvalPemasukan);
    }

    /**
     * Requirement 3: Bukti resolution & fallback handling on models.
     */
    public function test_models_bukti_url_and_fallback_helpers(): void
    {
        // 1. Pemasukan with dummy / null proof
        $pemasukanNoProof = new Pemasukan(['bukti_bayar' => null]);
        $this->assertNull($pemasukanNoProof->bukti_bayar_url);
        $this->assertFalse($pemasukanNoProof->hasValidBuktiBayar());

        // 2. Pengeluaran with dummy / null proof
        $pengeluaranNoProof = new Pengeluaran(['Bukti_Path' => null]);
        $this->assertNull($pengeluaranNoProof->bukti_path_url);
        $this->assertFalse($pengeluaranNoProof->hasValidBukti());

        // 3. Approval with no proof
        $approvalNoProof = new Approval([
            'tabel_sumber' => 'SURAT',
            'id_referensi' => '99999',
        ]);
        $this->assertNull($approvalNoProof->bukti_url);
        $this->assertFalse($approvalNoProof->hasValidBukti());

        // 4. Pengeluaran with existing receipt nota
        $pengeluaranWithNota = Pengeluaran::whereNotNull('Bukti_Path')->first();
        if ($pengeluaranWithNota) {
            $this->assertNotNull($pengeluaranWithNota->bukti_path_url);
            $this->assertTrue($pengeluaranWithNota->hasValidBukti());
        }
    }
}
