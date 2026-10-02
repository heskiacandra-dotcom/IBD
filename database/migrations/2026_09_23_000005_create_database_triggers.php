<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // Drop existing triggers if any
            DB::unprepared("DROP TRIGGER IF EXISTS trg_auto_approval_surat_warga;");
            DB::unprepared("DROP TRIGGER IF EXISTS trg_update_stok_inventaris_beli;");

            // 1. MySQL Trigger: trg_auto_approval_surat_warga
            DB::unprepared("
                CREATE TRIGGER trg_auto_approval_surat_warga
                AFTER INSERT ON SURAT
                FOR EACH ROW
                BEGIN
                    INSERT INTO APPROVAL (Admin_ID, tabel_sumber, id_referensi, status, catatan, tgl_approval, created_at, updated_at)
                    VALUES (NULL, 'SURAT', NEW.surat_id, 'pending', 'Pengajuan surat baru via portal warga', NULL, NOW(), NOW());
                END
            ");

            // 2. MySQL Trigger: trg_update_stok_inventaris_beli
            DB::unprepared("
                CREATE TRIGGER trg_update_stok_inventaris_beli
                AFTER INSERT ON PEMBELIAN_INVENTARIS
                FOR EACH ROW
                BEGIN
                    UPDATE INVENTARIS 
                    SET Jumlah = Jumlah + NEW.jumlah_pembelian 
                    WHERE Inventaris_ID = NEW.inventaris_id;
                END
            ");
        } else {
            // SQLite triggers for CI/CD test isolation
            DB::unprepared("
                CREATE TRIGGER IF NOT EXISTS trg_auto_approval_surat_warga
                AFTER INSERT ON SURAT
                BEGIN
                    INSERT INTO APPROVAL (Admin_ID, tabel_sumber, id_referensi, status, catatan, tgl_approval, created_at, updated_at)
                    VALUES (NULL, 'SURAT', NEW.surat_id, 'pending', 'Pengajuan surat baru via portal warga', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
                END;
            ");

            DB::unprepared("
                CREATE TRIGGER IF NOT EXISTS trg_update_stok_inventaris_beli
                AFTER INSERT ON PEMBELIAN_INVENTARIS
                BEGIN
                    UPDATE INVENTARIS 
                    SET Jumlah = Jumlah + NEW.jumlah_pembelian 
                    WHERE Inventaris_ID = NEW.inventaris_id;
                END;
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_auto_approval_surat_warga;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_update_stok_inventaris_beli;");
    }
};
