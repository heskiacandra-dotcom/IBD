<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use App\Models\Admin;
use App\Models\Approval;
use App\Models\Surat;
use App\Models\Warga;
use App\Filament\Resources\Surats\Pages\ListSurats;
use App\Filament\Resources\Surats\Pages\ViewSurat;
use App\Filament\Resources\Surats\Pages\EditSurat;
use Livewire\Livewire;

class SuratDirectApprovalTest extends TestCase
{
    use DatabaseMigrations;

    protected Admin $sekretaris;
    protected Warga $warga;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);

        $this->sekretaris = Admin::where('role', 'sekretaris')->first() ?? Admin::create([
            'Username' => 'sekretaris_surat_test',
            'Password_hash' => bcrypt('Rahasia123!'),
            'role' => 'sekretaris',
            'ID_RT' => 1,
        ]);

        $this->warga = Warga::first();
    }

    /**
     * Test direct approve button on ListSurats table row without opening edit menu.
     */
    public function test_direct_approve_button_on_surats_table_row(): void
    {
        $this->actingAs($this->sekretaris, 'web');

        $surat = Surat::create([
            'NIK' => $this->warga->NIK,
            'Jenis_surat' => 'Surat Pengantar SKCK Kepolisian',
            'perihal' => 'Keperluan pendaftaran CPNS',
            'tgl_pengajuan' => now(),
            'status' => 'Menunggu',
            'nomor_surat' => null,
        ]);

        $approval = Approval::create([
            'Admin_ID' => $this->sekretaris->Admin_ID,
            'tabel_sumber' => 'SURAT',
            'id_referensi' => (string) $surat->surat_id,
            'status' => 'pending',
            'catatan' => 'Permohonan surat pengantar baru',
        ]);

        Livewire::test(ListSurats::class)
            ->assertTableActionExists('approve_terbitkan')
            ->callTableAction('approve_terbitkan', $surat)
            ->assertHasNoTableActionErrors();

        $freshSurat = $surat->fresh();
        $this->assertEquals('approved', $freshSurat->status);
        $this->assertNotNull($freshSurat->nomor_surat);
        $this->assertStringContainsString('SP-RT', $freshSurat->nomor_surat);

        $freshApproval = $approval->fresh();
        $this->assertEquals('approved', $freshApproval->status);
        $this->assertNotNull($freshApproval->tgl_approval);
    }

    /**
     * Test direct reject button side-by-side on ListSurats table row.
     */
    public function test_direct_reject_button_on_surats_table_row_side_by_side(): void
    {
        $this->actingAs($this->sekretaris, 'web');

        $surat = Surat::create([
            'NIK' => $this->warga->NIK,
            'Jenis_surat' => 'Surat Pengantar Pembuatan KTP & KK Baru',
            'perihal' => 'KTP hilang dan butuh pengantar',
            'tgl_pengajuan' => now(),
            'status' => 'pending',
            'nomor_surat' => null,
        ]);

        $approval = Approval::create([
            'Admin_ID' => $this->sekretaris->Admin_ID,
            'tabel_sumber' => 'SURAT',
            'id_referensi' => (string) $surat->surat_id,
            'status' => 'pending',
            'catatan' => 'Permohonan surat pengantar baru',
        ]);

        $test = Livewire::test(ListSurats::class)
            ->assertTableActionExists('approve_terbitkan')
            ->assertTableActionExists('tolak_permohonan');

        $test->mountTableAction('tolak_permohonan', $surat)
            ->setTableActionData([
                'alasan_penolakan' => 'Dokumen pendukung kartu keluarga asli belum dilampirkan.',
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $freshSurat = $surat->fresh();
        $this->assertEquals('rejected', $freshSurat->status);

        $freshApproval = $approval->fresh();
        $this->assertEquals('rejected', $freshApproval->status);
        $this->assertStringContainsString('Dokumen pendukung kartu keluarga asli belum dilampirkan', $freshApproval->catatan);
        $this->assertNotNull($freshApproval->tgl_approval);
    }

    /**
     * Test approve action from ViewSurat header action without editing.
     */
    public function test_approve_action_from_view_surat_page(): void
    {
        $this->actingAs($this->sekretaris, 'web');

        $surat = Surat::create([
            'NIK' => $this->warga->NIK,
            'Jenis_surat' => 'Surat Keterangan Domisili Tempat Tinggal',
            'perihal' => 'Pengurusan rekening bank',
            'tgl_pengajuan' => now(),
            'status' => 'pending',
            'nomor_surat' => null,
        ]);

        Approval::create([
            'Admin_ID' => $this->sekretaris->Admin_ID,
            'tabel_sumber' => 'SURAT',
            'id_referensi' => (string) $surat->surat_id,
            'status' => 'pending',
        ]);

        Livewire::test(ViewSurat::class, ['record' => $surat->getKey()])
            ->assertActionExists('approve')
            ->callAction('approve')
            ->assertHasNoActionErrors();

        $this->assertEquals('approved', $surat->fresh()->status);
        $this->assertNotNull($surat->fresh()->nomor_surat);
    }

    /**
     * Test approve action from EditSurat header action.
     */
    public function test_approve_action_from_edit_surat_page(): void
    {
        $this->actingAs($this->sekretaris, 'web');

        $surat = Surat::create([
            'NIK' => $this->warga->NIK,
            'Jenis_surat' => 'Surat Keterangan Usaha (SKU) Mikro Lingkungan',
            'perihal' => 'Pengajuan KUR BRI',
            'tgl_pengajuan' => now(),
            'status' => 'pending',
            'nomor_surat' => null,
        ]);

        Approval::create([
            'Admin_ID' => $this->sekretaris->Admin_ID,
            'tabel_sumber' => 'SURAT',
            'id_referensi' => (string) $surat->surat_id,
            'status' => 'pending',
        ]);

        Livewire::test(EditSurat::class, ['record' => $surat->getKey()])
            ->assertActionExists('approve')
            ->callAction('approve')
            ->assertHasNoActionErrors();

        $this->assertEquals('approved', $surat->fresh()->status);
        $this->assertNotNull($surat->fresh()->nomor_surat);
    }

    /**
     * Test filtering pending includes both 'pending' and 'Menunggu'.
     */
    public function test_filter_status_matches_all_pending_variants(): void
    {
        $this->actingAs($this->sekretaris, 'web');

        $surat1 = Surat::create([
            'NIK' => $this->warga->NIK,
            'Jenis_surat' => 'Surat Pengantar Pembuatan KTP & KK Baru',
            'perihal' => 'KTP Baru',
            'tgl_pengajuan' => now(),
            'status' => 'Menunggu',
        ]);

        $surat2 = Surat::create([
            'NIK' => $this->warga->NIK,
            'Jenis_surat' => 'Surat Pengantar SKCK Kepolisian',
            'perihal' => 'SKCK Baru',
            'tgl_pengajuan' => now(),
            'status' => 'pending',
        ]);

        Livewire::test(ListSurats::class)
            ->filterTable('status', 'pending')
            ->assertCanSeeTableRecords([$surat1, $surat2]);
    }

    /**
     * Test page UI layout, tabs, widgets, and column rendering.
     */
    public function test_surat_list_page_and_overview_widget_rendering(): void
    {
        $this->actingAs($this->sekretaris, 'web');

        Livewire::test(ListSurats::class)
            ->assertStatus(200)
            ->assertCanRenderTableColumn('surat_id')
            ->assertCanRenderTableColumn('warga.Nama_Lengkap')
            ->assertCanRenderTableColumn('Jenis_surat')
            ->assertCanRenderTableColumn('nomor_surat')
            ->assertCanRenderTableColumn('tgl_pengajuan')
            ->assertCanRenderTableColumn('status');

        Livewire::test(\App\Filament\Resources\Surats\Widgets\SuratOverviewWidget::class)
            ->assertStatus(200)
            ->assertSee('Total Pengajuan')
            ->assertSee('Menunggu Tindakan')
            ->assertSee('Disetujui / Terbit')
            ->assertSee('Ditolak');
    }
}
