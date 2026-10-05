<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use App\Models\Warga;

class PortalWargaCleanFooterTest extends TestCase
{
    use DatabaseMigrations;

    protected Warga $warga;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);
        $this->warga = Warga::first();
    }

    /**
     * Test that 'masuk ke cms' / 'Akses CMS' and 'Bot WhatsApp' are removed from portal warga.
     */
    public function test_portal_footer_removes_cms_and_whatsapp_bot(): void
    {
        $response = $this->actingAs($this->warga, 'warga')->get(route('portal.home'));

        $response->assertStatus(200);

        // Assert CMS entry points are removed from citizen portal footer
        $response->assertDontSee('Akses CMS Pengurus RT');
        $response->assertDontSee('modalCmsNotice');
        $response->assertDontSee('Portal CMS Digunakan untuk memverifikasi', false);

        // Assert WhatsApp bot indicator is removed
        $response->assertDontSee('Bot WhatsApp RT');
        $response->assertDontSee('Bot WhatsApp');

        // Assert clean citizen-facing elements are present
        $response->assertSee('Layanan Mandiri Warga RT 05');
        $response->assertSee('Status Surat & Mutasi Iuran', false);
    }

    /**
     * Test navbar for guest displays 'Masuk' instead of 'Login CTA'.
     */
    public function test_navbar_displays_masuk_instead_of_login_cta(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertDontSee('Login CTA');
    }

    /**
     * Test portal pages (layanan, agenda, transparansi, aspirasi) are clean and functional.
     */
    public function test_portal_subpages_are_clean(): void
    {
        $pages = [
            route('portal.layanan'),
            route('portal.agenda'),
            route('portal.transparansi'),
            route('portal.aspirasi'),
        ];

        foreach ($pages as $url) {
            $response = $this->actingAs($this->warga, 'warga')->get($url);
            $response->assertStatus(200);
            $response->assertDontSee('Akses CMS Pengurus RT');
            $response->assertDontSee('modalCmsNotice');
            $response->assertDontSee('Bot WhatsApp RT');
        }
    }
}
