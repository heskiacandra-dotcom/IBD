<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Implementasi 13 Tabel ERD Resmi RuangWarga (Verbatim)
     */
    public function up(): void
    {
        // 1. DATA_RT (ID_RT [PK], nomor_rt, nomor_rw, kelurahan, kecamatan, kota, nama_ketua_rt)
        Schema::create('DATA_RT', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('ID_RT');
            $table->string('nomor_rt', 10);
            $table->string('nomor_rw', 10);
            $table->string('kelurahan', 100);
            $table->string('kecamatan', 100);
            $table->string('kota', 100);
            $table->string('nama_ketua_rt', 150);
            $table->timestamps();
        });

        // 2. ADMIN (Admin_ID [PK], Username, Password_hash, role, ID_RT [FK])
        Schema::create('ADMIN', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('Admin_ID');
            $table->string('Username', 100)->unique();
            $table->string('Password_hash', 255);
            $table->string('role', 50); // super_admin, admin_rt, bendahara, sekretaris
            $table->unsignedBigInteger('ID_RT')->nullable();
            $table->foreign('ID_RT')->references('ID_RT')->on('DATA_RT')->onDelete('cascade');
            $table->timestamps();
        });

        // 3. APPROVAL (id_approval [PK], Admin_ID [FK], tabel_sumber, id_referensi, status, catatan, tgl_approval)
        Schema::create('APPROVAL', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('id_approval');
            $table->unsignedBigInteger('Admin_ID')->nullable();
            $table->foreign('Admin_ID')->references('Admin_ID')->on('ADMIN')->onDelete('cascade');
            $table->string('tabel_sumber', 50); // SURAT, PEMASUKAN, PENGELUARAN, ASPIRASI, KEGIATAN, INVENTARIS
            $table->string('id_referensi', 50);
            $table->string('status', 50)->default('Menunggu'); // Menunggu, Disetujui, Ditolak
            $table->text('catatan')->nullable();
            $table->dateTime('tgl_approval')->nullable();
            $table->timestamps();
        });

        // 4. KARTU_KELUARGA (ID_KK [PK], Alamat)
        Schema::create('KARTU_KELUARGA', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('ID_KK', 20)->primary(); // Nomor KK 16 digit
            $table->text('Alamat');
            $table->timestamps();
        });

        // 5. WARGA (NIK [PK], ID_KK [FK], ID_RT [FK], Nama_Lengkap, no_telp, Username, Password_hash, tgl_lahir, role)
        Schema::create('WARGA', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('NIK', 16)->primary(); // NIK 16 digit
            $table->string('ID_KK', 20)->nullable();
            $table->foreign('ID_KK')->references('ID_KK')->on('KARTU_KELUARGA')->onDelete('cascade');
            $table->unsignedBigInteger('ID_RT')->nullable();
            $table->foreign('ID_RT')->references('ID_RT')->on('DATA_RT')->onDelete('cascade');
            $table->string('Nama_Lengkap', 150);
            $table->string('no_telp', 25)->nullable();
            $table->string('Username', 100)->nullable();
            $table->string('Password_hash', 255)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('role', 50)->default('warga');
            $table->timestamps();
        });

        // 6. SURAT (surat_id [PK], NIK [FK], Jenis_surat, perihal, tgl_pengajuan, status)
        Schema::create('SURAT', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('surat_id');
            $table->string('NIK', 16);
            $table->foreign('NIK')->references('NIK')->on('WARGA')->onDelete('cascade');
            $table->string('Jenis_surat', 100);
            $table->text('perihal');
            $table->date('tgl_pengajuan');
            $table->string('status', 50)->default('Menunggu'); // Menunggu, Disetujui, Ditolak
            $table->timestamps();
        });

        // 7. ASPIRASI (id_aspirasi [PK], NIK [FK], Isi_Aspirasi)
        Schema::create('ASPIRASI', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('id_aspirasi');
            $table->string('NIK', 16)->nullable();
            $table->foreign('NIK')->references('NIK')->on('WARGA')->onDelete('cascade');
            $table->text('Isi_Aspirasi');
            $table->timestamps();
        });

        // 8. RESPON_ASPIRASI (id_aspirasi [PK/FK], nama_responden, isi_respon, tanggal_respon)
        Schema::create('RESPON_ASPIRASI', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger('id_aspirasi')->primary();
            $table->foreign('id_aspirasi')->references('id_aspirasi')->on('ASPIRASI')->onDelete('cascade');
            $table->string('nama_responden', 150);
            $table->text('isi_respon');
            $table->dateTime('tanggal_respon');
            $table->timestamps();
        });

        // 9. KEGIATAN (kegiatan_id [PK], ID_RT [FK], nama_kegiatan, tgl_kegiatan, lokasi, deskripsi)
        Schema::create('KEGIATAN', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('kegiatan_id');
            $table->unsignedBigInteger('ID_RT')->nullable();
            $table->foreign('ID_RT')->references('ID_RT')->on('DATA_RT')->onDelete('cascade');
            $table->string('nama_kegiatan', 150);
            $table->date('tgl_kegiatan');
            $table->string('lokasi', 150);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 10. PEMASUKAN (Tagihan_Id [PK], ID_KK [FK], Kategori, ID_RT [FK], nama_tagihan, nominal, tgl_jatuh_tmp, status_tagihan)
        Schema::create('PEMASUKAN', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('Tagihan_Id');
            $table->string('ID_KK', 20)->nullable();
            $table->foreign('ID_KK')->references('ID_KK')->on('KARTU_KELUARGA')->onDelete('cascade');
            $table->string('Kategori', 50); // Iuran Bulanan, Donasi, Kas Sosial, dll
            $table->unsignedBigInteger('ID_RT')->nullable();
            $table->foreign('ID_RT')->references('ID_RT')->on('DATA_RT')->onDelete('cascade');
            $table->string('nama_tagihan', 150);
            $table->decimal('nominal', 15, 2);
            $table->date('tgl_jatuh_tmp');
            $table->string('status_tagihan', 50)->default('Belum Lunas'); // Belum Lunas, Lunas
            $table->timestamps();
        });

        // 11. PENGELUARAN (Pengeluaran_id [PK], Kategori [FK/String], Keterangan, Jumlah_Bayar, Tgl_Bayar, Metode_Bayar, Bukti_Path)
        Schema::create('PENGELUARAN', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('Pengeluaran_id');
            $table->string('Kategori', 50); // Keamanan, Kebersihan, Perbaikan Fasum, dll
            $table->string('Keterangan', 255);
            $table->decimal('Jumlah_Bayar', 15, 2);
            $table->date('Tgl_Bayar');
            $table->string('Metode_Bayar', 50)->default('Kas Tunai');
            $table->string('Bukti_Path', 255)->nullable();
            $table->timestamps();
        });

        // 12. INVENTARIS (Inventaris_ID [PK], ID_RT [FK], Nama_Barang, Kode_Barang, Jumlah, Lokasi)
        Schema::create('INVENTARIS', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('Inventaris_ID');
            $table->unsignedBigInteger('ID_RT')->nullable();
            $table->foreign('ID_RT')->references('ID_RT')->on('DATA_RT')->onDelete('cascade');
            $table->string('Nama_Barang', 150);
            $table->string('Kode_Barang', 50)->unique();
            $table->integer('Jumlah')->default(1);
            $table->string('Lokasi', 150)->default('Balai RT');
            $table->timestamps();
        });

        // 13. PEMBELIAN_INVENTARIS (Pembelian_id [PK], inventaris_id [FK], Pengeluaran_id [FK], tgl_pembelian, total_biaya, jumlah_pembelian)
        Schema::create('PEMBELIAN_INVENTARIS', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id('Pembelian_id');
            $table->unsignedBigInteger('inventaris_id')->nullable();
            $table->foreign('inventaris_id')->references('Inventaris_ID')->on('INVENTARIS')->onDelete('cascade');
            $table->unsignedBigInteger('Pengeluaran_id')->nullable();
            $table->foreign('Pengeluaran_id')->references('Pengeluaran_id')->on('PENGELUARAN')->onDelete('cascade');
            $table->date('tgl_pembelian');
            $table->decimal('total_biaya', 15, 2);
            $table->integer('jumlah_pembelian')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PEMBELIAN_INVENTARIS');
        Schema::dropIfExists('INVENTARIS');
        Schema::dropIfExists('PENGELUARAN');
        Schema::dropIfExists('PEMASUKAN');
        Schema::dropIfExists('KEGIATAN');
        Schema::dropIfExists('RESPON_ASPIRASI');
        Schema::dropIfExists('ASPIRASI');
        Schema::dropIfExists('SURAT');
        Schema::dropIfExists('WARGA');
        Schema::dropIfExists('KARTU_KELUARGA');
        Schema::dropIfExists('APPROVAL');
        Schema::dropIfExists('ADMIN');
        Schema::dropIfExists('DATA_RT');
    }
};
