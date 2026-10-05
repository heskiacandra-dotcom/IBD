<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pemasukan;
use App\Models\Warga;
use App\Models\Admin;
use App\Models\Approval;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;

class MidtransPaymentGatewayTest extends TestCase
{
    use DatabaseMigrations;

    protected Warga $warga;
    protected Pemasukan $tagihan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);

        $this->warga = Warga::first();
        $this->tagihan = Pemasukan::withoutGlobalScope('tenant')->create([
            'ID_KK' => $this->warga->ID_KK,
            'ID_RT' => $this->warga->ID_RT ?: 1,
            'Kategori' => 'Iuran Warga',
            'nama_tagihan' => 'Iuran Kas RT 05 - Oktober 2026',
            'nominal' => 75000,
            'tgl_jatuh_tmp' => now()->addDays(10)->toDateString(),
            'status_tagihan' => 'Belum Lunas',
        ]);
    }

    /**
     * Test 1: Warga dapat me-request Snap Token untuk pembayaran iuran keluarga
     */
    public function test_warga_can_request_snap_token_for_their_dues(): void
    {
        $res = $this->actingAs($this->warga, 'warga')->postJson(route('payment.snap-token'), [
            'tagihan_id' => $this->tagihan->Tagihan_Id,
        ]);

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);
        $this->assertNotEmpty($res->json('snap_token'));
        $this->assertNotEmpty($res->json('order_id'));

        // Cek database ter-update
        $fresh = $this->tagihan->fresh();
        $this->assertNotNull($fresh->snap_token);
        $this->assertNotNull($fresh->order_id);
        $this->assertEquals('qris_midtrans', $fresh->metode_pembayaran);
    }

    /**
     * Test 2: Warga tidak dapat me-request pembayaran untuk tagihan KK lain
     */
    public function test_warga_cannot_request_snap_token_for_another_household_dues(): void
    {
        // Cari atau buat warga lain dari KK berbeda
        $otherWarga = Warga::where('ID_KK', '!=', $this->warga->ID_KK)->first();
        if (!$otherWarga) {
            $otherWarga = Warga::create([
                'NIK' => '3271018888888888',
                'Nama_Lengkap' => 'Warga Lain',
                'ID_KK' => 9999999999999999,
                'ID_RT' => 1,
                'Username' => 'warga_lain',
                'Password_hash' => bcrypt('password'),
                'role' => 'warga',
            ]);
        }

        $res = $this->actingAs($otherWarga, 'warga')->postJson(route('payment.snap-token'), [
            'tagihan_id' => $this->tagihan->Tagihan_Id,
        ]);

        $res->assertStatus(403);
    }

    /**
     * Test 3: Webhook Midtrans otomatis menandai tagihan LUNAS ketika settlement berhasil
     */
    public function test_webhook_settlement_marks_pemasukan_as_lunas_automatically(): void
    {
        $orderId = 'IURAN-RT1-TAG' . $this->tagihan->Tagihan_Id . '-' . time();
        $this->tagihan->update(['order_id' => $orderId]);

        // Buat tiket approval terkait
        $approval = Approval::create([
            'Admin_ID' => 2,
            'tabel_sumber' => 'PEMASUKAN',
            'id_referensi' => (string) $this->tagihan->Tagihan_Id,
            'status' => 'pending',
            'catatan' => 'Menunggu verifikasi pembayaran',
        ]);

        $payload = [
            'order_id' => $orderId,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '75000.00',
            'payment_type' => 'qris',
            'fraud_status' => 'accept',
            'settlement_time' => now()->toDateTimeString(),
        ];

        $res = $this->postJson(route('payment.midtrans.webhook'), $payload);

        $res->assertStatus(200);
        $res->assertJson(['status' => 'OK']);

        // Verifikasi tagihan kas otomatis menjadi Lunas
        $fresh = $this->tagihan->fresh();
        $this->assertEquals('Lunas', $fresh->status_tagihan);
        $this->assertEquals('qris', $fresh->metode_pembayaran);
        $this->assertNotNull($fresh->paid_at);

        // Tiket approval otomatis disetujui
        $this->assertEquals('Disetujui', $approval->fresh()->status);
    }

    /**
     * Test 4: Simulasi Sandbox Sukses untuk Testing Demo Lokal
     */
    public function test_sandbox_simulate_success_instantly_marks_dues_as_lunas(): void
    {
        $res = $this->actingAs($this->warga, 'warga')->postJson(route('payment.simulate-success'), [
            'tagihan_id' => $this->tagihan->Tagihan_Id,
        ]);

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        $this->assertEquals('Lunas', $this->tagihan->fresh()->status_tagihan);
        $this->assertEquals('qris', $this->tagihan->fresh()->metode_pembayaran);
    }

    /**
     * Test 5: Check Status Endpoint (Polling)
     */
    public function test_check_status_polling_detects_paid_and_unpaid(): void
    {
        $orderId = 'TEST-ORDER-POLL-123';
        $this->tagihan->update(['order_id' => $orderId, 'status_tagihan' => 'Belum Lunas']);

        // 1. Pending check
        $resPending = $this->actingAs($this->warga, 'warga')->getJson(route('payment.check-status', ['order_id' => $orderId]));
        $resPending->assertStatus(200);
        $resPending->assertJson(['success' => true, 'is_paid' => false, 'status' => 'pending']);

        // 2. Mark as Lunas
        $this->tagihan->update(['status_tagihan' => 'Lunas']);

        // 3. Paid check (Instant auto-detection)
        $resPaid = $this->actingAs($this->warga, 'warga')->getJson(route('payment.check-status', ['order_id' => $orderId]));
        $resPaid->assertStatus(200);
        $resPaid->assertJson(['success' => true, 'is_paid' => true, 'status' => 'settlement']);
    }

    /**
     * Test 6: Cancel or Refund Transaction via Service
     */
    public function test_cancel_or_refund_transaction_reverts_bill_to_belum_lunas(): void
    {
        $this->tagihan->update([
            'order_id' => 'TEST-REFUND-123',
            'status_tagihan' => 'Lunas',
            'paid_at' => now(),
        ]);

        $service = app(\App\Services\MidtransService::class);
        $result = $service->cancelOrRefundTransaction($this->tagihan, 'Uji coba pembatalan CMS');

        $this->assertTrue($result['success']);
        $this->assertEquals('Belum Lunas', $this->tagihan->fresh()->status_tagihan);
        $this->assertNull($this->tagihan->fresh()->paid_at);
    }
}
