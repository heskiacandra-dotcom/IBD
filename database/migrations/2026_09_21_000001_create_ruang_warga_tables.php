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
        // 1. UNIT RT (Multi-Tenant Management)
        Schema::create('rt_units', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rt')->unique(); // e.g. "RT 05 / RW 01"
            $table->string('nama_kawasan');      // e.g. "Harmoni Residence"
            $table->string('kelurahan');         // e.g. "Sukamaju"
            $table->string('kecamatan');         // e.g. "Cilodong"
            $table->string('kota');              // e.g. "Kota Depok"
            $table->string('ketua_nama');        // e.g. "Bpk. Sukardi Hartono"
            $table->string('ketua_kontak');      // e.g. "0812-9876-5432"
            $table->string('status')->default('aktif'); // aktif / nonaktif
            $table->timestamps();
        });

        // 2. DATA WARGA & KEPENDUDUKAN (Sekretaris)
        Schema::create('citizens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rt_unit_id')->nullable()->constrained('rt_units')->nullOnDelete();
            $table->string('nik', 16)->unique();
            $table->string('no_kk', 16);
            $table->string('nama');
            $table->string('alamat');             // Blok B No. 12
            $table->string('whatsapp')->nullable();
            $table->string('status_domisili')->default('tetap'); // tetap, kontrak, kost
            $table->boolean('is_kepala_keluarga')->default(false);
            $table->string('pekerjaan')->nullable();
            $table->string('gender', 1)->default('L'); // L / P
            $table->timestamps();
        });

        // 3. PUSAT APPROVAL TIKET (Jembatan Warga <-> CMS Pengurus)
        Schema::create('approval_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique(); // e.g. #APP-2026-001
            $table->foreignId('rt_unit_id')->nullable()->constrained('rt_units')->nullOnDelete();
            $table->foreignId('citizen_id')->nullable()->constrained('citizens')->nullOnDelete();
            $table->string('applicant');             // Nama Pemohon
            $table->string('house');                 // Blok B No. 12
            $table->string('nik', 16)->nullable();
            $table->string('phone')->nullable();
            $table->string('tabel_sumber');          // SURAT, PEMASUKAN, PENGELUARAN, KEGIATAN, ASET
            $table->string('tab');                   // surat, keuangan, kegiatan, aset
            $table->string('perihal');
            $table->string('nominal')->default('-'); // e.g. "Rp 75.000" atau "-"
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('detail_lengkap')->nullable();
            $table->string('preview_type')->nullable(); // document, receipt, asset, event
            $table->string('preview_title')->nullable();
            $table->string('preview_meta')->nullable();
            $table->text('catatan_pengurus')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        // 4. PERSURATAN RESMI RT (Sekretaris)
        Schema::create('official_letters', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique(); // 470/05/IX/2026
            $table->string('ticket_code')->nullable();
            $table->foreignId('rt_unit_id')->nullable()->constrained('rt_units')->nullOnDelete();
            $table->foreignId('citizen_id')->nullable()->constrained('citizens')->nullOnDelete();
            $table->string('nama_warga');
            $table->string('nik', 16);
            $table->string('alamat');
            $table->string('jenis_surat');
            $table->text('keperluan');
            $table->string('status')->default('Pending'); // Pending, Draft Terbit, Siap Ambil, Selesai
            $table->string('qr_token')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->timestamps();
        });

        // 5. BUKU KAS & MUTASI KEUANGAN (Bendahara)
        Schema::create('cash_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rt_unit_id')->nullable()->constrained('rt_units')->nullOnDelete();
            $table->string('ticket_code')->nullable();
            $table->string('tipe');                  // pemasukan, pengeluaran
            $table->string('kategori');              // Iuran Warga, Kebersihan, Keamanan, Fasum, dll
            $table->string('perihal');
            $table->decimal('nominal', 15, 2);
            $table->string('nama_pihak');            // Pembayar atau Penerima
            $table->string('alamat_blok')->nullable();
            $table->string('periode')->nullable();   // September 2026
            $table->string('status')->default('approved'); // approved, pending, rejected
            $table->date('tanggal');
            $table->string('bukti_file')->nullable();
            $table->timestamps();
        });

        // 6. ASPIRASI & SUARA WARGA
        Schema::create('aspirations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rt_unit_id')->nullable()->constrained('rt_units')->nullOnDelete();
            $table->string('kategori');
            $table->string('judul');
            $table->text('isi');
            $table->string('nama')->default('Warga Anonim');
            $table->string('blok')->default('Harmoni');
            $table->boolean('is_anonim')->default(false);
            $table->string('status')->default('Menunggu Tanggapan'); // Menunggu Tanggapan, Diproses, Selesai
            $table->text('tanggapan_rt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspirations');
        Schema::dropIfExists('cash_mutations');
        Schema::dropIfExists('official_letters');
        Schema::dropIfExists('approval_tickets');
        Schema::dropIfExists('citizens');
        Schema::dropIfExists('rt_units');
    }
};
