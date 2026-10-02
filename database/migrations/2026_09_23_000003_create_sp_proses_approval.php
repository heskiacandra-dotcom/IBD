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
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::unprepared("DROP PROCEDURE IF EXISTS sp_proses_approval;");

            DB::unprepared("
                CREATE PROCEDURE sp_proses_approval(
                    IN p_id_approval INT,
                    IN p_admin_id BIGINT UNSIGNED,
                    IN p_status VARCHAR(20),
                    IN p_catatan TEXT,
                    OUT p_res_code INT,
                    OUT p_res_msg VARCHAR(255)
                )
                BEGIN
                    DECLARE v_count INT DEFAULT 0;
                    DECLARE v_tabel VARCHAR(50);
                    DECLARE v_ref VARCHAR(50);

                    DECLARE EXIT HANDLER FOR SQLEXCEPTION
                    BEGIN
                        ROLLBACK;
                        SET p_res_code = 500;
                        SET p_res_msg = 'Database error: Gagal memproses approval dalam transaksi.';
                    END;

                    START TRANSACTION;

                    SELECT COUNT(*), tabel_sumber, id_referensi 
                    INTO v_count, v_tabel, v_ref
                    FROM APPROVAL 
                    WHERE id_approval = p_id_approval
                    GROUP BY tabel_sumber, id_referensi
                    LIMIT 1;

                    IF v_count = 0 OR v_tabel IS NULL THEN
                        ROLLBACK;
                        SET p_res_code = 404;
                        SET p_res_msg = CONCAT('Tiket approval #', p_id_approval, ' tidak ditemukan.');
                    ELSE
                        -- 1. Update tabel APPROVAL
                        UPDATE APPROVAL 
                        SET status = p_status,
                            catatan = p_catatan,
                            Admin_ID = p_admin_id,
                            tgl_approval = NOW(),
                            updated_at = NOW()
                        WHERE id_approval = p_id_approval;

                        -- 2. Update tabel sumber terkait jika ada
                        IF UPPER(v_tabel) = 'SURAT' THEN
                            UPDATE SURAT 
                            SET status = p_status,
                                updated_at = NOW()
                            WHERE surat_id = v_ref;
                        ELSEIF UPPER(v_tabel) = 'PEMASUKAN' THEN
                            IF p_status = 'Disetujui' THEN
                                UPDATE PEMASUKAN 
                                SET status_tagihan = 'Lunas',
                                    updated_at = NOW()
                                WHERE Tagihan_Id = v_ref;
                            ELSE
                                UPDATE PEMASUKAN 
                                SET status_tagihan = 'Belum Lunas',
                                    updated_at = NOW()
                                WHERE Tagihan_Id = v_ref;
                            END IF;
                        END IF;

                        COMMIT;
                        SET p_res_code = 200;
                        SET p_res_msg = CONCAT('Tiket #', p_id_approval, ' (', v_tabel, ') berhasil diperbarui menjadi: ', p_status);
                    END IF;
                END
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::unprepared("DROP PROCEDURE IF EXISTS sp_proses_approval;");
        }
    }
};
