<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Warga;
use App\Models\Surat;
use App\Models\Pemasukan;

class WhatsAppGatewayService
{
    protected string $baseUrl;
    protected bool $enabled;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.wa_gateway.url', 'http://127.0.0.1:3001'), '/');
        $this->enabled = (bool) config('services.wa_gateway.enabled', true);
        $this->timeout = (int) config('services.wa_gateway.timeout', 3);
    }

    /**
     * Check if Node.js WhatsApp Gateway service is running and responsive
     */
    public function isOnline(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        try {
            $response = Http::timeout($this->timeout)->get("{$this->baseUrl}/health");
            return $response->successful() && $response->json('status') === 'online';
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Send direct WhatsApp text message
     */
    public function sendMessage(string $phone, string $message, string $type = 'info', array $metadata = []): array
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'WhatsApp gateway is disabled in configuration.'];
        }

        try {
            $response = Http::timeout($this->timeout)->post("{$this->baseUrl}/api/send-message", [
                'phone' => $phone,
                'message' => $message,
                'type' => $type,
                'metadata' => $metadata,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("[WA-GATEWAY] Send message returned non-200", ['status' => $response->status(), 'body' => $response->body()]);
            return ['success' => false, 'message' => 'Gateway returned error response: ' . $response->body()];
        } catch (\Throwable $e) {
            Log::error("[WA-GATEWAY] Failed to connect to WhatsApp Gateway", ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Koneksi ke Node.js WA Gateway gagal: ' . $e->getMessage()];
        }
    }

    /**
     * Send pre-formatted template message
     */
    public function sendTemplate(string $template, string $phone, array $data = []): array
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'WhatsApp gateway is disabled.'];
        }

        try {
            $response = Http::timeout($this->timeout)->post("{$this->baseUrl}/api/send-template", [
                'template' => $template,
                'phone' => $phone,
                'data' => $data,
            ]);

            return $response->json() ?? ['success' => false, 'message' => 'Invalid JSON from gateway'];
        } catch (\Throwable $e) {
            Log::error("[WA-GATEWAY] Failed sending template {$template}", ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Koneksi ke Node.js WA Gateway gagal: ' . $e->getMessage()];
        }
    }

    /**
     * Notifikasi Otomatis: Surat Pengantar Disetujui
     */
    public function notifySuratDisetujui(Warga $warga, Surat $surat): array
    {
        $phone = $warga->no_telp ?? null;
        if (!$phone) {
            return ['success' => false, 'message' => 'Nomor WhatsApp warga belum terdaftar.'];
        }

        return $this->sendTemplate('surat_disetujui', $phone, [
            'nama' => $warga->Nama_Lengkap,
            'jenis_surat' => $surat->Jenis_surat,
            'ticket_code' => '#SRT-' . str_pad($surat->surat_id, 4, '0', STR_PAD_LEFT),
            'download_url' => route('surat.pdf.download', ['id' => $surat->surat_id], true),
        ]);
    }

    /**
     * Notifikasi Otomatis: Tagihan Iuran Baru
     */
    public function notifyTagihanIuran(Warga $warga, Pemasukan $tagihan): array
    {
        $phone = $warga->no_telp ?? null;
        if (!$phone) {
            return ['success' => false, 'message' => 'Nomor WhatsApp warga belum terdaftar.'];
        }

        return $this->sendTemplate('tagihan_iuran', $phone, [
            'nama' => $warga->Nama_Lengkap,
            'nama_tagihan' => $tagihan->nama_tagihan,
            'nominal' => (float) $tagihan->nominal,
            'jatuh_tempo' => $tagihan->tgl_jatuh_tmp ? \Carbon\Carbon::parse($tagihan->tgl_jatuh_tmp)->translatedFormat('d F Y') : '-',
            'portal_url' => route('portal.layanan', [], true),
        ]);
    }

    /**
     * Notifikasi Otomatis: Konfirmasi Pembayaran Kas Lunas
     */
    public function notifyPembayaranLunas(Warga $warga, Pemasukan $tagihan): array
    {
        $phone = $warga->no_telp ?? null;
        if (!$phone) {
            return ['success' => false, 'message' => 'Nomor WhatsApp warga belum terdaftar.'];
        }

        return $this->sendTemplate('pembayaran_lunas', $phone, [
            'nama' => $warga->Nama_Lengkap,
            'nama_tagihan' => $tagihan->nama_tagihan,
            'nominal' => (float) $tagihan->nominal,
        ]);
    }
}
