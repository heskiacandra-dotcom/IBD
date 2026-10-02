<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppGatewayService;
use App\Models\Warga;

class TestWhatsAppNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:test {phone? : Nomor WhatsApp tujuan} {--message= : Isi pesan custom}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pesan uji coba notifikasi WhatsApp melalui Node.js WA Gateway';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppGatewayService $waService)
    {
        $this->info("Menghubungi RuangWarga Node.js WA Gateway...");

        $isOnline = $waService->isOnline();
        if (!$isOnline) {
            $this->warn("⚠️  Node.js WhatsApp Gateway terdeteksi OFFLINE di http://localhost:3001.");
            $this->line("Jalankan gateway dengan perintah: npm run gateway");
        } else {
            $this->info("✅ Node.js WhatsApp Gateway ONLINE!");
        }

        $phone = $this->argument('phone');
        if (!$phone) {
            $warga = Warga::whereNotNull('no_telp')->first();
            $phone = $warga ? $warga->no_telp : '081234567890';
            $this->line("Menggunakan nomor contoh: {$phone}" . ($warga ? " ({$warga->Nama_Lengkap})" : ""));
        }

        $message = $this->option('message') ?: "Halo! Ini adalah pesan uji coba integrasi Node.js WhatsApp Gateway dengan Laravel 11 RuangWarga.";

        $this->line("Mengirim pesan ke: {$phone}...");
        $result = $waService->sendMessage($phone, $message, 'info');

        if (($result['success'] ?? false)) {
            $this->info("🎉 Pesan berhasil diproses oleh Node.js Gateway!");
            $this->table(['Key', 'Value'], [
                ['Status', $result['data']['status'] ?? 'DELIVERED'],
                ['Recipient', $result['data']['recipient'] ?? $phone],
                ['Message ID', $result['data']['messageId'] ?? '-'],
                ['Sent At', $result['data']['sentAt'] ?? date('Y-m-d H:i:s')],
            ]);
        } else {
            $this->error("❌ Gagal mengirim pesan: " . ($result['message'] ?? 'Unknown error'));
        }

        return 0;
    }
}
