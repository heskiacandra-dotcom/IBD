# RuangWarga WhatsApp Notification Gateway (Node.js)

Layanan microservice Node.js mandiri berbasis Express untuk menangani pengiriman dan simulasi notifikasi otomatis ke nomor WhatsApp warga RT 05 / RW 01 Harmoni Residence.

## Fitur Utama
1. **Normalisasi Nomor Telepon Otomatis**: Mengonversi format lokal (`0812...` atau `+628...`) menjadi standar WhatsApp internasional (`62812...`).
2. **Standardisasi Format Pesan**: Dilengkapi *header badge* dan *footer disclaimer* resmi RuangWarga.
3. **Template Otomatis**:
   - `surat_disetujui`: Notifikasi persetujuan surat pengantar RT ber-barcode.
   - `tagihan_iuran`: Notifikasi iuran kas bulanan dan tautan pembayaran/QRIS.
   - `pembayaran_lunas`: Konfirmasi pelunasan setoran kas oleh Bendahara RT.
   - `jadwal_ronda`: Pengingat piket ronda siskamling malam ini.
4. **Endpoint API**:
   - `GET /health` (Status & uptime)
   - `GET /api/status` (Driver status)
   - `POST /api/send-message` (Kirim pesan custom)
   - `POST /api/send-template` (Kirim pesan template)
   - `GET /api/logs` (Arsip log 100 pengiriman terakhir)

## Cara Menjalankan Layanan

### Dari Root Proyek:
```bash
npm run gateway
```
atau bersamaan dengan server Vite:
```bash
npm run dev:all
```

### Dari Folder `services/wa-gateway/`:
```bash
cd services/wa-gateway
npm install
npm run dev
```

## Uji Coba dari Laravel CLI
Jalankan perintah Artisan bawaan:
```bash
php artisan wa:test 081234567890 --message="Halo Warga!"
```
