<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Pemasukan;
use App\Models\Warga;
use App\Models\KartuKeluarga;
use App\Services\MonthlyDuesBillingService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Database\Seeders\ErdRuangWargaSeeder;
use Illuminate\Support\Carbon;

class MonthlyDuesAndDoubleBillingTest extends TestCase
{
    use DatabaseMigrations;

    protected Warga $warga;
    protected MonthlyDuesBillingService $billingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ErdRuangWargaSeeder::class);

        $this->warga = Warga::first();
        $this->billingService = app(MonthlyDuesBillingService::class);
    }

    /**
     * Test 1: Tagihan iuran dibuat tiap bulan dengan jatuh tempo akhir bulan
     * (Warga bebas membayar di tanggal berapa saja selama bulan berjalan)
     */
    public function test_monthly_bill_has_end_of_month_due_date_allowing_payment_any_day(): void
    {
        $testDate = Carbon::parse('2026-10-15');
        $bill = $this->billingService->ensureMonthlyBillForHousehold($this->warga->ID_KK, $this->warga->ID_RT, $testDate);

        $this->assertNotNull($bill);
        $this->assertEquals('3201051909120008', $bill->ID_KK);
        $this->assertEquals(75000, (float) $bill->nominal);
        $this->assertStringContainsString('Oktober 2026', $bill->nama_tagihan);
        
        // Jatuh tempo harus di akhir bulan (2026-10-31) sehingga warga bebas bayar tanggal berapa saja
        $this->assertEquals('2026-10-31', Carbon::parse($bill->tgl_jatuh_tmp)->toDateString());
    }

    /**
     * Test 2: Bila berganti bulan dan bulan sebelumnya belum lunas, tagihan otomatis DOUBLE
     */
    public function test_bill_doubles_when_previous_month_is_unpaid_and_month_rolls_over(): void
    {
        // KK dengan tagihan bulan September 2026 yang belum lunas
        $targetKk = '3201051909120005';
        $wargaMenunggak = Warga::where('ID_KK', $targetKk)->first();
        $this->assertNotNull($wargaMenunggak);

        // Simulasi kita berada di bulan Oktober 2026 (bulan berganti)
        $october = Carbon::parse('2026-10-02');
        $summary = $this->billingService->getHouseholdBillingSummary($targetKk, $wargaMenunggak->ID_RT, $october);

        // Harus terdeteksi 2 bulan belum lunas (September + Oktober)
        $this->assertEquals(2, $summary['unpaid_count']);
        $this->assertTrue($summary['is_double']);
        $this->assertEquals(2, $summary['multiplier']);
        
        // Total tagihan menjadi Rp 150.000 (Double: 75.000 x 2)
        $this->assertEquals(150000.00, $summary['total_nominal']);
        $this->assertStringContainsString('Double', $summary['status_label']);
        $this->assertStringContainsString('150.000', $summary['notice_text']);

        // Rincian breakdown berisi 2 bulan
        $this->assertCount(2, $summary['bills']);
        $this->assertTrue($summary['bills'][0]['is_overdue_month']); // September = bulan lalu
        $this->assertFalse($summary['bills'][1]['is_overdue_month']); // Oktober = bulan berjalan
    }

    /**
     * Test 3: Warga dapat membayar seluruh tagihan double / akumulatif sekaligus via 1 QRIS Midtrans
     */
    public function test_warga_can_pay_all_accumulated_double_bills_simultaneously(): void
    {
        $targetKk = '3201051909120005';
        $wargaMenunggak = Warga::where('ID_KK', $targetKk)->first();
        $this->assertNotNull($wargaMenunggak);

        // Pastikan tagihan Oktober terbit
        $october = Carbon::parse('2026-10-02');
        $this->billingService->ensureMonthlyBillForHousehold($targetKk, $wargaMenunggak->ID_RT, $october);

        // 1. Request Snap Token dengan pay_all: true
        $resSnap = $this->actingAs($wargaMenunggak, 'warga')->postJson(route('payment.snap-token'), [
            'pay_all' => true,
        ]);

        $resSnap->assertStatus(200);
        $resSnap->assertJson([
            'success' => true,
            'total_amount' => 150000,
            'bills_count' => 2,
        ]);
        $this->assertNotEmpty($resSnap->json('snap_token'));
        $orderId = $resSnap->json('order_id');
        $this->assertStringContainsString('DOUBLE', $orderId);

        // 2. Simulasi webhook notifikasi settlement Midtrans
        $webhookPayload = [
            'order_id' => $orderId,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '150000.00',
            'payment_type' => 'qris',
            'fraud_status' => 'accept',
        ];

        $resWebhook = $this->postJson(route('payment.midtrans.webhook'), $webhookPayload);
        $resWebhook->assertStatus(200);
        $resWebhook->assertJson(['status' => 'OK']);

        // 3. Verifikasi KEDUA tagihan (September dan Oktober) otomatis LUNAS
        $unpaidRemaining = Pemasukan::withoutGlobalScope('tenant')
            ->where('ID_KK', $targetKk)
            ->whereRaw('LOWER(status_tagihan) != ?', ['lunas'])
            ->count();
        $this->assertEquals(0, $unpaidRemaining, 'All double bills should be marked as Lunas simultaneously');

        // Summary warga sekarang menjadi 0 tagihan / Lunas
        $freshSummary = $this->billingService->getHouseholdBillingSummary($targetKk, $wargaMenunggak->ID_RT, $october);
        $this->assertEquals(0, $freshSummary['unpaid_count']);
        $this->assertFalse($freshSummary['is_double']);
        $this->assertEquals('Lunas', $freshSummary['status_label']);
    }

    /**
     * Test 4: Simulasi Sandbox Bayar Sekaligus (pay_all) via endpoint simulateSuccess
     */
    public function test_sandbox_simulate_success_can_pay_all_double_bills(): void
    {
        $targetKk = '3201051909120006';
        $wargaMenunggak = Warga::where('ID_KK', $targetKk)->first();
        $this->assertNotNull($wargaMenunggak);

        $october = Carbon::parse('2026-10-02');
        $this->billingService->ensureMonthlyBillForHousehold($targetKk, $wargaMenunggak->ID_RT, $october);

        $res = $this->actingAs($wargaMenunggak, 'warga')->postJson(route('payment.simulate-success'), [
            'pay_all' => true,
        ]);

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        // Verifikasi semua tagihan KK menjadi Lunas
        $unpaidCount = Pemasukan::withoutGlobalScope('tenant')
            ->where('ID_KK', $targetKk)
            ->whereRaw('LOWER(status_tagihan) != ?', ['lunas'])
            ->count();
        $this->assertEquals(0, $unpaidCount);
    }

    /**
     * Test 5: Artisan command iuran:generate-monthly menerbitkan tagihan untuk seluruh KK
     */
    public function test_artisan_command_generates_monthly_dues_for_all_households(): void
    {
        $targetMonth = '2026-11';
        $this->artisan('iuran:generate-monthly', ['--month' => $targetMonth])
            ->assertExitCode(0);

        $householdsCount = KartuKeluarga::count();
        $generatedBills = Pemasukan::withoutGlobalScope('tenant')
            ->where('nama_tagihan', 'like', '%November 2026%')
            ->count();

        $this->assertEquals($householdsCount, $generatedBills);
    }

    /**
     * Test 6: API cek-iuran mengembalikan informasi billing lengkap dan status double
     */
    public function test_api_cek_iuran_returns_double_bill_details(): void
    {
        $targetKk = '3201051909120005';
        $wargaMenunggak = Warga::where('ID_KK', $targetKk)->first();

        $res = $this->actingAs($wargaMenunggak, 'warga')->getJson(route('portal.cek-iuran'));

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);
        $this->assertTrue($res->json('data.is_double'));
        $this->assertEquals(2, $res->json('data.multiplier'));
        $this->assertEquals('Rp 150.000', $res->json('data.amount'));
        $this->assertNotEmpty($res->json('data.due_date_text'));
        $this->assertNotEmpty($res->json('data.notice_text'));
    }
}
