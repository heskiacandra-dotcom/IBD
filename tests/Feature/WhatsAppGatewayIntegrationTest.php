<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Http;
use App\Services\WhatsAppGatewayService;
use App\Models\Warga;
use App\Models\Surat;
use App\Models\Pemasukan;
use Database\Seeders\ErdRuangWargaSeeder;

class WhatsAppGatewayIntegrationTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);
    }

    public function test_wa_gateway_health_check_detects_online_status()
    {
        Http::fake([
            'http://127.0.0.1:3001/health' => Http::response(['status' => 'online'], 200),
        ]);

        $service = app(WhatsAppGatewayService::class);
        $this->assertTrue($service->isOnline());
    }

    public function test_wa_gateway_send_message_dispatches_successfully()
    {
        Http::fake([
            'http://127.0.0.1:3001/api/send-message' => Http::response([
                'success' => true,
                'data' => [
                    'messageId' => 'WA-TEST-123',
                    'recipient' => '628123456789',
                    'status' => 'DELIVERED',
                    'sentAt' => '2026-09-27 13:00:00',
                ]
            ], 200),
        ]);

        $service = app(WhatsAppGatewayService::class);
        $response = $service->sendMessage('08123456789', 'Halo warga RT 05!', 'info');

        $this->assertTrue($response['success']);
        $this->assertEquals('WA-TEST-123', $response['data']['messageId']);
    }

    public function test_wa_gateway_send_surat_disetujui_template()
    {
        Http::fake([
            'http://127.0.0.1:3001/api/send-template' => Http::response([
                'success' => true,
                'data' => [
                    'messageId' => 'WA-TPL-456',
                    'template' => 'surat_disetujui',
                    'status' => 'DELIVERED',
                ]
            ], 200),
        ]);

        $warga = Warga::first();
        $surat = Surat::first();

        $service = app(WhatsAppGatewayService::class);
        $result = $service->notifySuratDisetujui($warga, $surat);

        $this->assertTrue($result['success']);
        $this->assertEquals('WA-TPL-456', $result['data']['messageId']);
    }

    public function test_artisan_wa_test_command_runs_successfully()
    {
        Http::fake([
            'http://127.0.0.1:3001/health' => Http::response(['status' => 'online'], 200),
            'http://127.0.0.1:3001/api/send-message' => Http::response([
                'success' => true,
                'data' => [
                    'messageId' => 'WA-CMD-789',
                    'recipient' => '628123456789',
                    'status' => 'DELIVERED',
                    'sentAt' => '2026-09-27 13:00:00',
                ]
            ], 200),
        ]);

        $this->artisan('wa:test 08123456789 --message="Test Command"')
            ->assertExitCode(0)
            ->expectsOutputToContain('Node.js WhatsApp Gateway ONLINE')
            ->expectsOutputToContain('Pesan berhasil diproses');
    }
}
