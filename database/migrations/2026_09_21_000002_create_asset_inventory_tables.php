<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Master Data Inventaris Aset RT
        Schema::create('asset_items', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset')->unique(); // e.g. AST-TND-01
            $table->string('nama_aset');           // e.g. Tenda Lipat (3x6 Meter)
            $table->string('kategori');            // Tenda & Terpal, Kursi & Meja, Elektronik, Fasilitas Umum
            $table->integer('total_stok')->default(1);
            $table->integer('stok_tersedia')->default(1);
            $table->integer('stok_dipinjam')->default(0);
            $table->string('lokasi_penyimpanan')->default('Gudang Balai Warga RT 05');
            $table->string('kondisi')->default('Baik'); // Baik, Perlu Servis, Rusak
            $table->string('icon')->default('📦');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 2. Transaksi Peminjaman & Pengajuan Alat Aset RT
        Schema::create('asset_loans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pinjam')->unique(); // e.g. #PINJAM-2026-001
            $table->string('ticket_code')->nullable(); // Terhubung ke approval_tickets (#APP-2026-...)
            $table->foreignId('asset_item_id')->nullable()->constrained('asset_items')->nullOnDelete();
            $table->string('nama_alat');
            $table->string('kode_aset')->nullable();
            $table->integer('jumlah')->default(1);
            $table->string('nama_peminjam');
            $table->string('blok_rumah');
            $table->string('kontak')->nullable();
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->text('tujuan');
            $table->string('status')->default('pending'); // pending (Pengajuan), borrowed (Sedang Dipinjam), returned (Selesai), rejected (Ditolak)
            $table->string('kondisi_keluar')->default('Baik & Lengkap');
            $table->string('kondisi_kembali')->nullable();
            $table->date('tgl_kembali')->nullable();
            $table->string('approved_by')->nullable();
            $table->text('catatan_petugas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_loans');
        Schema::dropIfExists('asset_items');
    }
};
