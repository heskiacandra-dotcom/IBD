<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use App\Models\Admin;
use App\Models\KontakRt;
use App\Models\Warga;
use App\Filament\Resources\KontakRts\Pages\ListKontakRts;
use Livewire\Livewire;

class KontakRtWhatsAppIntegrationTest extends TestCase
{
    use DatabaseMigrations;

    protected Admin $adminRt;
    protected Admin $sekretaris;
    protected Warga $warga;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);

        $this->adminRt = Admin::where('role', 'admin_rt')->first() ?? Admin::create([
            'Username' => 'ketua_rt_test',
            'Password_hash' => bcrypt('Rahasia123!'),
            'role' => 'admin_rt',
            'ID_RT' => 1,
        ]);

        $this->sekretaris = Admin::where('role', 'sekretaris')->first() ?? Admin::create([
            'Username' => 'sekretaris_test',
            'Password_hash' => bcrypt('Rahasia123!'),
            'role' => 'sekretaris',
            'ID_RT' => 1,
        ]);

        $this->warga = Warga::first();
    }

    /**
     * Test KontakRt model WhatsApp URL generation.
     */
    public function test_kontak_rt_generates_correct_whatsapp_urls(): void
    {
        $contact = KontakRt::create([
            'ID_RT' => 1,
            'role' => 'Ketua RT 05',
            'nama_kontak' => 'Bpk. Sukardi Hartono',
            'nomor_telepon' => '0812-3456-7890',
            'is_whatsapp' => true,
            'pesan_wa' => 'Halo Ketua RT, saya butuh bantuan',
            'lokasi_tampilan' => 'footer_darurat',
            'urutan' => 1,
            'is_aktif' => true,
        ]);

        $this->assertEquals('6281234567890', $contact->clean_whatsapp_number);
        $this->assertStringStartsWith('https://wa.me/6281234567890', $contact->whatsapp_url);
        $this->assertStringContainsString('Halo%20Ketua%20RT', $contact->whatsapp_url);
    }

    /**
     * Test CMS allows editing contact phone numbers and connects to WhatsApp.
     */
    public function test_cms_allows_editing_phone_number_and_reflects_on_portal(): void
    {
        $this->actingAs($this->adminRt, 'web');

        $contact = KontakRt::where('role', 'Ketua RT 05')->first() ?? KontakRt::create([
            'ID_RT' => 1,
            'role' => 'Ketua RT 05',
            'nama_kontak' => 'Bpk. Sukardi Hartono',
            'nomor_telepon' => '0812-3456-7890',
            'is_whatsapp' => true,
            'lokasi_tampilan' => 'footer_darurat',
            'urutan' => 1,
            'is_aktif' => true,
        ]);

        // Test editing phone number in CMS Livewire table
        Livewire::test(ListKontakRts::class)
            ->assertTableActionExists('edit')
            ->mountTableAction('edit', $contact)
            ->setTableActionData([
                'nomor_telepon' => '0878-1122-3344',
                'is_whatsapp' => true,
                'nama_kontak' => 'Bpk. Sukardi Hartono Baru',
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        $freshContact = $contact->fresh();
        $this->assertEquals('0878-1122-3344', $freshContact->nomor_telepon);
        $this->assertEquals('6287811223344', $freshContact->clean_whatsapp_number);
        $this->assertStringStartsWith('https://wa.me/6287811223344', $freshContact->whatsapp_url);

        // Verify the edited phone number and WhatsApp link appear on the citizen portal
        $portalResponse = $this->actingAs($this->warga, 'warga')->get(route('portal.home'));
        $portalResponse->assertStatus(200);
        $portalResponse->assertSee('0878-1122-3344');
        $portalResponse->assertSee('https://wa.me/6287811223344', false);
    }

    /**
     * Test secretariat button connects to dynamic WhatsApp URL.
     */
    public function test_secretariat_button_connects_to_dynamic_whatsapp_url(): void
    {
        $sekretariat = KontakRt::where('lokasi_tampilan', 'sekretariat')->first();
        if ($sekretariat) {
            $sekretariat->update([
                'nomor_telepon' => '0899-7777-8888',
                'pesan_wa' => 'Halo Pengurus Sekretariat',
            ]);
        }

        $response = $this->actingAs($this->warga, 'warga')->get(route('portal.home'));
        $response->assertStatus(200);
        $response->assertSee('https://wa.me/6289977778888', false);
        $response->assertSee('0899-7777-8888');
    }
}
