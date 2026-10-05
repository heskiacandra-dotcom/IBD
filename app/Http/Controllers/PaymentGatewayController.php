<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Warga;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentGatewayController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * API: Request Snap Token for Citizen Dues Bill.
     * Supports single bill or bundled multi-month / double bills.
     */
    public function requestSnapToken(Request $request)
    {
        $warga = auth('warga')->user() ?? auth('web')->user();
        if ($warga instanceof \App\Models\Admin) {
            $warga = Warga::first();
        }

        if (!$warga) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'tagihan_id' => 'nullable|integer',
            'tagihan_ids' => 'nullable|array',
            'tagihan_ids.*' => 'integer',
            'pay_all' => 'nullable|boolean',
        ]);

        $query = Pemasukan::withoutGlobalScope('tenant')->where('ID_KK', $warga->ID_KK);

        if (!empty($validated['pay_all'])) {
            $bills = $query->whereRaw('LOWER(status_tagihan) != ?', ['lunas'])->orderBy('tgl_jatuh_tmp', 'asc')->get();
        } elseif (!empty($validated['tagihan_ids'])) {
            $bills = $query->whereIn('Tagihan_Id', $validated['tagihan_ids'])->get();
        } elseif (!empty($validated['tagihan_id'])) {
            $single = Pemasukan::withoutGlobalScope('tenant')->where('Tagihan_Id', $validated['tagihan_id'])->first();
            if (!$single) {
                return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan.'], 404);
            }
            if ($warga instanceof Warga && $single->ID_KK != $warga->ID_KK) {
                return response()->json(['success' => false, 'message' => 'Tagihan ini bukan milik KK Anda.'], 403);
            }
            $bills = collect([$single]);
        } else {
            // Default to all unpaid bills for this citizen
            $bills = $query->whereRaw('LOWER(status_tagihan) != ?', ['lunas'])->orderBy('tgl_jatuh_tmp', 'asc')->get();
        }

        if ($bills->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada tagihan tertunggak yang perlu dibayar.'], 400);
        }

        $allAlreadyPaid = $bills->every(fn($b) => strtolower($b->status_tagihan) === 'lunas');
        if ($allAlreadyPaid) {
            return response()->json(['success' => false, 'message' => 'Semua tagihan yang dipilih sudah lunas.'], 400);
        }

        $result = $this->midtransService->createSnapTransaction($bills, $warga);

        return response()->json($result);
    }

    /**
     * Webhook Callback: Midtrans Notification Listener.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        Log::info('[MIDTRANS-WEBHOOK] Notification received:', $payload);

        // Verify signature if signature_key is provided
        if (isset($payload['signature_key']) && config('midtrans.is_production')) {
            if (!$this->midtransService->verifySignature($payload)) {
                Log::warning('[MIDTRANS-WEBHOOK] Invalid signature key!');
                return response()->json(['message' => 'Invalid signature'], 403);
            }
        }

        $result = $this->midtransService->handleNotification($payload);

        return response()->json(['status' => 'OK', 'result' => $result]);
    }

    /**
     * Sandbox Testing Helper: Simulate successful QRIS scan payment.
     * Supports single bill or bundled multi-month / double bills.
     */
    public function simulateSuccess(Request $request)
    {
        $warga = auth('warga')->user() ?? auth('web')->user();
        if ($warga instanceof \App\Models\Admin) {
            $warga = Warga::first();
        }

        $validated = $request->validate([
            'tagihan_id' => 'nullable|integer',
            'tagihan_ids' => 'nullable|array',
            'tagihan_ids.*' => 'integer',
            'pay_all' => 'nullable|boolean',
        ]);

        if (!empty($validated['pay_all']) && $warga) {
            $bills = Pemasukan::withoutGlobalScope('tenant')
                ->where('ID_KK', $warga->ID_KK)
                ->whereRaw('LOWER(status_tagihan) != ?', ['lunas'])
                ->get();
        } elseif (!empty($validated['tagihan_ids'])) {
            $bills = Pemasukan::withoutGlobalScope('tenant')
                ->whereIn('Tagihan_Id', $validated['tagihan_ids'])
                ->get();
        } elseif (!empty($validated['tagihan_id'])) {
            $tagihan = Pemasukan::withoutGlobalScope('tenant')->find($validated['tagihan_id']);
            $bills = $tagihan ? collect([$tagihan]) : collect();
        } else {
            $bills = $warga ? Pemasukan::withoutGlobalScope('tenant')
                ->where('ID_KK', $warga->ID_KK)
                ->whereRaw('LOWER(status_tagihan) != ?', ['lunas'])
                ->get() : collect();
        }

        if ($bills->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan.'], 404);
        }

        $tagIds = $bills->pluck('Tagihan_Id')->all();
        $isMulti = ($bills->count() > 1);
        $first = $bills->first();
        $orderId = $first->order_id ?: ('SIMULATE-IURAN-' . ($isMulti ? 'DOUBLE-' : '') . 'TAG' . implode('_', $tagIds) . '-' . time());

        foreach ($bills as $bill) {
            if (!$bill->order_id) {
                $bill->update(['order_id' => $orderId]);
            }
        }

        $grossAmount = (int) round((float) $bills->sum('nominal'));

        $mockPayload = [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => (string) $grossAmount,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'payment_type' => 'qris',
        ];

        $result = $this->midtransService->handleNotification($mockPayload);

        $countText = $bills->count() > 1 ? "({$bills->count()} bulan sekaligus)" : "";

        return response()->json([
            'success' => true,
            'message' => "Pembayaran iuran {$countText} senilai Rp " . number_format($grossAmount, 0, ',', '.') . " sukses diverifikasi otomatis via QRIS (Sandbox).",
            'result' => $result,
        ]);
    }

    /**
     * Real-time Payment Status Polling Endpoint.
     * Checks local database and queries Midtrans API directly.
     * When paid in simulator or scanned via banking app, automatically verifies to LUNAS.
     */
    public function checkStatus(Request $request)
    {
        $orderId = $request->query('order_id');
        if (!$orderId) {
            return response()->json(['success' => false, 'message' => 'Missing order_id'], 400);
        }

        // 1. Check local database first
        $bills = Pemasukan::withoutGlobalScope('tenant')->where('order_id', $orderId)->get();
        if ($bills->isEmpty() && preg_match('/TAG([0-9_]+)/i', $orderId, $matches)) {
            $ids = explode('_', $matches[1]);
            $bills = Pemasukan::withoutGlobalScope('tenant')->whereIn('Tagihan_Id', $ids)->get();
        }

        if ($bills->isNotEmpty() && $bills->every(fn($b) => strtolower($b->status_tagihan) === 'lunas')) {
            return response()->json([
                'success' => true,
                'is_paid' => true,
                'status' => 'settlement',
                'order_id' => $orderId,
                'message' => 'Pembayaran telah terverifikasi LUNAS.',
            ]);
        }

        // 2. Query Midtrans API directly
        $serverKey = config('midtrans.server_key');
        if (!empty($serverKey) && !str_starts_with($serverKey, 'SB-Mid-server-demo')) {
            try {
                $statusUrl = config('midtrans.is_production')
                    ? "https://api.midtrans.com/v2/{$orderId}/status"
                    : "https://api.sandbox.midtrans.com/v2/{$orderId}/status";

                $midtransRes = \Illuminate\Support\Facades\Http::withBasicAuth($serverKey, '')
                    ->timeout(5)
                    ->get($statusUrl);

                if ($midtransRes->successful()) {
                    $midtransData = $midtransRes->json();
                    $trxStatus = !empty($midtransData['transaction_status']) ? strtolower($midtransData['transaction_status']) : 'pending';

                    if (in_array($trxStatus, ['settlement', 'capture', 'success'])) {
                        // Automatically update local bills to LUNAS
                        $this->midtransService->handleNotification($midtransData);

                        return response()->json([
                            'success' => true,
                            'is_paid' => true,
                            'status' => $trxStatus,
                            'order_id' => $orderId,
                            'message' => 'Pembayaran via QRIS berhasil diverifikasi otomatis!',
                        ]);
                    }

                    return response()->json([
                        'success' => true,
                        'is_paid' => false,
                        'status' => $trxStatus ?: 'pending',
                        'order_id' => $orderId,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::info('[MIDTRANS-POLL-STATUS] ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'is_paid' => false,
            'status' => 'pending',
            'order_id' => $orderId,
        ]);
    }
}
