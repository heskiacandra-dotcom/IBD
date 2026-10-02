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
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_ajukan_surat_warga`;");

            DB::unprepared("
                CREATE PROCEDURE `sp_ajukan_surat_warga`(
                    IN p_nik VARCHAR(16),
                    IN p_jenis_surat VARCHAR(100),
                    IN p_perihal TEXT,
                    OUT p_surat_id INT,
                    OUT p_res_code INT,
                    OUT p_res_msg VARCHAR(255)
                )
                BEGIN
                    DECLARE v_warga_count INT DEFAULT 0;
                    DECLARE v_new_surat_id INT DEFAULT 0;

                    DECLARE EXIT HANDLER FOR SQLEXCEPTION
                    BEGIN
                        ROLLBACK;
                        SET p_surat_id = 0;
                        SET p_res_code = 500;
                        SET p_res_msg = 'Terjadi kesalahan sistem saat memproses pengajuan surat.';
                    END;

                    START TRANSACTION;

                    -- 1. Validasi Warga
                    SELECT COUNT(*) INTO v_warga_count 
                    FROM `WARGA` 
                    WHERE `NIK` = p_nik;

                    IF v_warga_count = 0 THEN
                        ROLLBACK;
                        SET p_surat_id = 0;
                        SET p_res_code = 404;
                        SET p_res_msg = CONCAT('NIK ', p_nik, ' tidak ditemukan dalam basis data kependudukan RT.');
                    ELSE
                        -- 2. Insert ke SURAT
                        INSERT INTO `SURAT` (
                            `NIK`, 
                            `Jenis_surat`, 
                            `perihal`, 
                            `tgl_pengajuan`, 
                            `status`, 
                            `created_at`, 
                            `updated_at`
                        ) VALUES (
                            p_nik, 
                            p_jenis_surat, 
                            p_perihal, 
                            NOW(), 
                            'Menunggu', 
                            NOW(), 
                            NOW()
                        );

                        SET v_new_surat_id = LAST_INSERT_ID();
                        SET p_surat_id = v_new_surat_id;

                        -- 3. Insert ke antrean APPROVAL
                        INSERT INTO `APPROVAL` (
                            `Admin_ID`, 
                            `tabel_sumber`, 
                            `id_referensi`, 
                            `status`, 
                            `catatan`, 
                            `tgl_approval`, 
                            `created_at`, 
                            `updated_at`
                        ) VALUES (
                            NULL, 
                            'SURAT', 
                            v_new_surat_id, 
                            'Menunggu', 
                            CONCAT('Permohonan ', p_jenis_surat, ': ', SUBSTRING(p_perihal, 1, 150)), 
                            NULL, 
                            NOW(), 
                            NOW()
                        );

                        COMMIT;
                        SET p_res_code = 200;
                        SET p_res_msg = CONCAT('Surat #', v_new_surat_id, ' berhasil diajukan dan diteruskan ke antrean persetujuan RT.');
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
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_ajukan_surat_warga`;");
        }
    }
};
