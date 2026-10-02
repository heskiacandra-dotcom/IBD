<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Warga;
use Database\Seeders\ErdRuangWargaSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MobileAndPwaExperienceTest extends TestCase
{
    use DatabaseMigrations;

    protected Admin $admin;
    protected Warga $warga;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);

        $this->admin = Admin::where('role', 'super_admin')->first();
        $this->warga = Warga::first();
    }

    /**
     * Test 1: PWA Web App Manifest is accessible and has valid PWA spec properties.
     */
    public function test_pwa_manifest_is_valid_and_accessible(): void
    {
        $response = $this->get('/manifest.json');
        $response->assertStatus(200);
        $manifest = json_decode($response->getContent(), true);

        $this->assertIsArray($manifest);
        $this->assertEquals('RuangWarga - Smart RT Lingkungan', $manifest['name']);
        $this->assertEquals('RuangWarga', $manifest['short_name']);
        $this->assertEquals('standalone', $manifest['display']);
        $this->assertEquals('#2563EB', $manifest['theme_color']);
        $this->assertNotEmpty($manifest['icons']);
        $this->assertNotEmpty($manifest['shortcuts']);
    }

    /**
     * Test 2: PWA Service Worker script is available and contains cache configurations.
     */
    public function test_service_worker_is_accessible(): void
    {
        $response = $this->get('/sw.js');
        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertStringContainsString('CACHE_NAME', $content);
        $this->assertStringContainsString('/offline.html', $content);
        $this->assertStringContainsString('/manifest.json', $content);
    }

    /**
     * Test 3: Offline fallback page is present and contains hotline darurat.
     */
    public function test_offline_page_is_accessible(): void
    {
        $response = $this->get('/offline.html');
        $response->assertStatus(200);
        $response->assertSee('Koneksi Terputus (Offline)');
        $response->assertSee('Hotline Darurat Warga');
    }

    /**
     * Test 4: Portal Warga includes PWA tags and Mobile Bottom Navigation Bar.
     */
    public function test_portal_warga_includes_pwa_and_mobile_bottom_nav(): void
    {
        $response = $this->actingAs($this->warga, 'warga')->get('/portal');
        $response->assertStatus(200);
        
        // PWA Meta
        $response->assertSee('rel="manifest"', false);
        $response->assertSee('name="theme-color"', false);
        $response->assertSee('navigator.serviceWorker.register', false);

        // Mobile Bottom Navigation Bar
        $response->assertSee('mobile-bottom-nav', false);
        $response->assertSee('Beranda');
        $response->assertSee('Layanan');
        $response->assertSee('Agenda');
        $response->assertSee('Kas RT');
        $response->assertSee('Aspirasi');
    }

    /**
     * Test 5: CMS Filament Panel includes PWA head and service worker hooks.
     */
    public function test_cms_includes_pwa_head_and_mobile_styles(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get('/admin');
        $response->assertStatus(200);

        // PWA & Mobile Styles injected via PanelsRenderHook
        $response->assertSee('manifest.json');
        $response->assertSee('RuangWarga CMS');
        $response->assertSee('fi-ta-content');
        $response->assertSee('serviceWorker.register');
    }

    /**
     * Test 6: Unified Login page includes PWA tags for direct app installability.
     */
    public function test_login_page_includes_pwa_meta(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('rel="manifest"', false);
        $response->assertSee('name="theme-color"', false);
        $response->assertSee('RuangWarga');
    }
}
