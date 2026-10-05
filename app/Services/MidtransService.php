<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\Warga;
use App\Models\Approval;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;
    protected string $snapApiUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key', 'SB-Mid-server-demo-rt-05-key');
        $this->clientKey = config('midtrans.client_key', 'SB-Mid-client-demo-rt-05-key');
        $this->isProduction = (bool) config('midtrans.is_production', false);
        $this->snapApiUrl = config('midtrans.snap_api_endpoint', 'https://app.sandbox.midtrans.com/snap/v1/transactions');
    }

    /**
     * Generate Snap Token for a citizen's dues bill(s) (Pemasukan).
     * Supports single bill or multi-bill accumulation (double / multi-month arrears).
     */
    public function createSnapTransaction(Pemasukan|\Illuminate\Support\Collection|array $pemasukan, Warga $warga): array
    {
        $bills = ($pemasukan instanceof Pemasukan) ? collect([$pemasukan]) : collect($pemasukan);
        if ($bills->isEmpty()) {
            throw new \InvalidArgumentException('Tidak ada tagihan yang dipilih untuk pembayaran.');
        }

        $tagihanIds = $bills->pluck('Tagihan_Id')->all();
        $firstBill = $bills->first();
        $isMulti = ($bills->count() > 1);
        $orderId = 'IURAN-RT' . ($firstBill->ID_RT ?: 1) . '-' . ($isMulti ? 'DOUBLE-' : '') . 'TAG' . implode('_', $tagihanIds) . '-' . time();
        $grossAmount = (int) round((float) $bills->sum('nominal'));

        $itemDetails = $bills->map(function ($bill) {
            return [
                'id' => 'TAG-' . $bill->Tagihan_Id,
                'price' => (int) round((float) $bill->nominal),
                'quantity' => 1,
                'name' => mb_substr($bill->nama_tagihan . ' (RT ' . str_pad($bill->ID_RT ?: 1, 2, '0', STR_PAD_LEFT) . ')', 0, 50),
            ];
        })->values()->all();

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $warga->Nama_Lengkap,
                'phone' => $warga->no_telp ?: '081234567890',
                'billing_address' => [
                    'address' => optional($warga->kartuKeluarga)->Alamat ?: ('RT ' . ($firstBill->ID_RT ?: 1)),
                ],
            ],
            'item_details' => $itemDetails,
            'enabled_payments' => [
                'qris',
                'gopay',
                'shopeepay',
                'bca_va',
                'bni_va',
                'bri_va',
                'mandiri_va',
                'permata_va',
                'other_va',
            ],
        ];

        $snapToken = null;
        $redirectUrl = null;
        $qrCodeUrl = null;
        $qrString = null;
        $isMock = false;

        $coreChargeUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2/charge'
            : 'https://api.sandbox.midtrans.com/v2/charge';

        // 1. Direct Core API QRIS Charge for true QR display & Simulator compatibility
        if (!empty($this->serverKey) && !str_starts_with($this->serverKey, 'SB-Mid-server-demo')) {
            try {
                $qrisChargePayload = [
                    'payment_type' => 'qris',
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => $grossAmount,
                    ],
                    'qris' => [
                        'acquirer' => 'gopay',
                    ],
                    'item_details' => $itemDetails,
                    'customer_details' => [
                        'first_name' => $warga->Nama_Lengkap,
                        'phone' => $warga->no_telp ?: '081234567890',
                    ],
                ];

                $qrisRes = Http::withBasicAuth($this->serverKey, '')
                    ->withHeaders(['Accept' => 'application/json', 'Content-Type' => 'application/json'])
                    ->timeout(10)
                    ->post($coreChargeUrl, $qrisChargePayload);

                if ($qrisRes->successful()) {
                    $qrisData = $qrisRes->json();
                    $qrString = $qrisData['qr_string'] ?? null;
                    if (!empty($qrisData['actions'])) {
                        foreach ($qrisData['actions'] as $act) {
                            if (($act['name'] ?? '') === 'generate-qr-code') {
                                $qrCodeUrl = $act['url'] ?? null;
                                break;
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::info('[MIDTRANS-QRIS-CHARGE] Core charge error: ' . $e->getMessage());
            }
        }

        // 2. Only invoke Snap if Core API was not used, to prevent duplicate order_id collisions
        if (!$qrCodeUrl && !empty($this->serverKey) && !str_starts_with($this->serverKey, 'SB-Mid-server-demo')) {
            try {
                $response = Http::withBasicAuth($this->serverKey, '')
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(10)
                    ->post($this->snapApiUrl, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $snapToken = $data['token'] ?? null;
                    $redirectUrl = $data['redirect_url'] ?? null;
                }
            } catch (\Throwable $e) {
                Log::warning('[MIDTRANS-SNAP] API connection failed: ' . $e->getMessage());
            }
        }

        // Fallback token if offline or demo
        if (!$snapToken) {
            $snapToken = 'snap-token-' . $orderId;
            $redirectUrl = 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $snapToken;
            if (!$qrCodeUrl) {
                $isMock = true;
            }
        }

        if (!$qrCodeUrl) {
            $demoId = 'demo-' . implode('-', $tagihanIds) . '-' . substr(md5($orderId), 0, 8);
            $qrCodeUrl = "https://api.sandbox.midtrans.com/v2/qris/{$demoId}/qr-code";
        }

        // Update all included bills
        foreach ($bills as $bill) {
            $bill->update([
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'metode_pembayaran' => 'qris_midtrans',
            ]);
        }

        return [
            'success' => true,
            'snap_token' => $snapToken,
            'redirect_url' => $redirectUrl,
            'order_id' => $orderId,
            'client_key' => $this->clientKey,
            'total_amount' => $grossAmount,
            'total_amount_formatted' => 'Rp ' . number_format($grossAmount, 0, ',', '.'),
            'bills_count' => $bills->count(),
            'is_mock' => $isMock,
            'qr_code_url' => $qrCodeUrl,
            'qr_string' => $qrString,
            'simulator_url' => 'https://simulator.sandbox.midtrans.com/v2/qris/index',
        ];
    }

    /**
     * Verify Midtrans Webhook SHA512 Signature.
     */
    public function verifySignature(array $payload): bool
    {
        if (!isset($payload['order_id'], $payload['status_code'], $payload['gross_amount'], $payload['signature_key'])) {
            return false;
        }

        $expectedSignature = hash(
            'sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $this->serverKey
        );

        return hash_equals($expectedSignature, $payload['signature_key']);
    }

    /**
     * Process Midtrans webhook notification payload and automatically update bill(s) & cash flow.
     * Supports single bill or bundled multi-month bills.
     */
    public function handleNotification(array $payload): array
    {
        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = strtolower($payload['transaction_status'] ?? '');
        $fraudStatus = strtolower($payload['fraud_status'] ?? 'accept');
        $paymentType = $payload['payment_type'] ?? 'qris';

        if (!$orderId) {
            return ['success' => false, 'message' => 'Missing order_id'];
        }

        // Find all Pemasukan records matching order_id
        $bills = Pemasukan::withoutGlobalScope('tenant')->where('order_id', $orderId)->get();

        // Fallback: parse TAG IDs from order_id format: IURAN-RT{rt}-DOUBLE-TAG{id1}_{id2}-{timestamp} or TAG{id}
        if ($bills->isEmpty() && preg_match('/TAG([0-9_]+)/i', $orderId, $matches)) {
            $ids = explode('_', $matches[1]);
            $bills = Pemasukan::withoutGlobalScope('tenant')->whereIn('Tagihan_Id', $ids)->get();
        }

        if ($bills->isEmpty()) {
            Log::warning("[MIDTRANS-WEBHOOK] Bills not found for order_id: {$orderId}");
            return ['success' => false, 'message' => 'Bill not found'];
        }

        $isSuccess = false;

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $isSuccess = true;
            }
        } elseif (in_array($transactionStatus, ['settlement', 'success'])) {
            $isSuccess = true;
        } elseif (in_array($transactionStatus, ['pending'])) {
            foreach ($bills as $bill) {
                $bill->update([
                    'status_tagihan' => 'pending_payment',
                    'payment_payload' => json_encode($payload),
                ]);
            }
            return ['success' => true, 'status' => 'pending'];
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            foreach ($bills as $bill) {
                $bill->update([
                    'status_tagihan' => 'Belum Lunas',
                    'payment_payload' => json_encode($payload),
                ]);
            }
            return ['success' => true, 'status' => $transactionStatus];
        }

        if ($isSuccess) {
            foreach ($bills as $bill) {
                $bill->update([
                    'status_tagihan' => 'Lunas',
                    'metode_pembayaran' => $paymentType,
                    'paid_at' => now(),
                    'payment_payload' => json_encode($payload),
                ]);

                // Sync with APPROVAL ticket if present
                Approval::where('tabel_sumber', 'PEMASUKAN')
                    ->where('id_referensi', (string) $bill->Tagihan_Id)
                    ->update([
                        'status' => 'Disetujui',
                        'tgl_approval' => now(),
                        'catatan' => 'Lunas otomatis via Payment Gateway Midtrans (' . strtoupper($paymentType) . ') - Order: ' . $orderId,
                    ]);
            }

            Log::info("[MIDTRANS-WEBHOOK] " . $bills->count() . " bills successfully paid via {$paymentType} for order {$orderId}");

            return [
                'success' => true,
                'status' => 'paid',
                'bills_count' => $bills->count(),
                'tagihan_ids' => $bills->pluck('Tagihan_Id')->all(),
                'total_nominal' => (float) $bills->sum('nominal'),
            ];
        }

        return ['success' => false, 'message' => 'Unhandled status: ' . $transactionStatus];
    }

    /**
     * Cancel or refund a transaction directly via Midtrans API and revert local bill to 'Belum Lunas'.
     */
    public function cancelOrRefundTransaction(Pemasukan $bill, string $reason = 'Pembatalan transaksi via CMS RT'): array
    {
        $orderId = $bill->order_id;
        $midtransMessage = 'Data transaksi lokal berhasil diubah menjadi Belum Lunas.';
        $midtransStatus = null;

        if ($orderId && !empty($this->serverKey) && !str_starts_with($this->serverKey, 'SB-Mid-server-demo')) {
            $statusUrl = $this->isProduction
                ? "https://api.midtrans.com/v2/{$orderId}/status"
                : "https://api.sandbox.midtrans.com/v2/{$orderId}/status";

            $cancelUrl = $this->isProduction
                ? "https://api.midtrans.com/v2/{$orderId}/cancel"
                : "https://api.sandbox.midtrans.com/v2/{$orderId}/cancel";

            $refundUrl = $this->isProduction
                ? "https://api.midtrans.com/v2/{$orderId}/refund"
                : "https://api.sandbox.midtrans.com/v2/{$orderId}/refund";

            try {
                // 1. Check current status in Midtrans
                $statusRes = Http::withBasicAuth($this->serverKey, '')
                    ->timeout(10)
                    ->get($statusUrl);

                if ($statusRes->successful()) {
                    $statusData = $statusRes->json();
                    $trxStatus = strtolower($statusData['transaction_status'] ?? '');
                    $midtransStatus = $trxStatus;

                    if ($trxStatus === 'pending') {
                        // Cancel pending transaction in Midtrans
                        $cancelRes = Http::withBasicAuth($this->serverKey, '')
                            ->timeout(10)
                            ->post($cancelUrl);
                        if ($cancelRes->successful()) {
                            $midtransMessage = 'Transaksi pending berhasil dibatalkan di server Midtrans.';
                        }
                    } elseif (in_array($trxStatus, ['settlement', 'capture'])) {
                        // Attempt refund in Midtrans
                        $grossAmount = (int) round((float) ($statusData['gross_amount'] ?? $bill->nominal));
                        $refundRes = Http::withBasicAuth($this->serverKey, '')
                            ->timeout(10)
                            ->post($refundUrl, [
                                'refund_key' => 'refund-' . $bill->Tagihan_Id . '-' . time(),
                                'amount' => $grossAmount,
                                'reason' => $reason,
                            ]);

                        if ($refundRes->successful()) {
                            $midtransMessage = 'Transaksi berhasil di-refund di server Midtrans.';
                        } else {
                            $errBody = $refundRes->json();
                            $errMsg = $errBody['status_message'] ?? 'Refund otomatis tidak didukung untuk metode ini.';
                            $midtransMessage = "Status lokal Belum Lunas. Catatan Midtrans: {$errMsg} (pengembalian uang riil dapat ditransfer manual ke warga).";
                        }
                    } elseif (in_array($trxStatus, ['cancel', 'expire', 'refund'])) {
                        $midtransMessage = "Status di server Midtrans sudah {$trxStatus}.";
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('[MIDTRANS-CANCEL-REFUND] Error: ' . $e->getMessage());
                $midtransMessage = 'Koneksi ke Midtrans gagal, status lokal tetap berhasil diperbarui: ' . $e->getMessage();
            }
        }

        // Revert local bill to Belum Lunas
        DB::transaction(function () use ($bill, $reason, $midtransMessage) {
            $bill->update([
                'status_tagihan' => 'Belum Lunas',
                'paid_at' => null,
            ]);

            Approval::where('tabel_sumber', 'PEMASUKAN')
                ->where('id_referensi', (string) $bill->Tagihan_Id)
                ->update([
                    'status' => 'rejected',
                    'catatan' => "Pembatalan: {$reason}. {$midtransMessage}",
                    'tgl_approval' => now(),
                    'Admin_ID' => auth()->id() ?? 3,
                ]);
        });

        return [
            'success' => true,
            'message' => "Tagihan #TAG-{$bill->Tagihan_Id} berhasil diubah menjadi Belum Lunas. {$midtransMessage}",
            'midtrans_status' => $midtransStatus,
        ];
    }
}
