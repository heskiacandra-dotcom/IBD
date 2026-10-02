<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stored Procedure: sp_dashboard_summary_rt
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_dashboard_summary_rt`;");
            DB::unprepared("
                CREATE PROCEDURE `sp_dashboard_summary_rt`(IN p_id_rt INT)
                BEGIN
                    DECLARE v_total_pengajuan INT DEFAULT 0;
                    DECLARE v_menunggu_approval INT DEFAULT 0;
                    DECLARE v_pemasukan_bulan_ini DECIMAL(15,2) DEFAULT 0.00;
                    DECLARE v_total_pemasukan DECIMAL(15,2) DEFAULT 0.00;
                    DECLARE v_total_pengeluaran DECIMAL(15,2) DEFAULT 0.00;
                    DECLARE v_saldo_kas_riil DECIMAL(15,2) DEFAULT 0.00;

                    -- 1. Total Pengajuan Masuk (dari tabel APPROVAL)
                    SELECT COUNT(*) INTO v_total_pengajuan FROM `APPROVAL`;

                    -- 2. Menunggu Approval
                    SELECT COUNT(*) INTO v_menunggu_approval 
                    FROM `APPROVAL` 
                    WHERE LOWER(`status`) IN ('menunggu', 'pending');

                    -- 3. Pemasukan Kas Bulan Ini (Status Lunas)
                    SELECT COALESCE(SUM(`nominal`), 0.00) INTO v_pemasukan_bulan_ini
                    FROM `PEMASUKAN`
                    WHERE LOWER(`status_tagihan`) = 'lunas'
                      AND (p_id_rt IS NULL OR `ID_RT` = p_id_rt)
                      AND MONTH(`created_at`) = MONTH(CURRENT_DATE())
                      AND YEAR(`created_at`) = YEAR(CURRENT_DATE());

                    -- 4. Total Pemasukan Kas (Status Lunas)
                    SELECT COALESCE(SUM(`nominal`), 0.00) INTO v_total_pemasukan
                    FROM `PEMASUKAN`
                    WHERE LOWER(`status_tagihan`) = 'lunas'
                      AND (p_id_rt IS NULL OR `ID_RT` = p_id_rt);

                    -- 5. Total Pengeluaran Kas
                    SELECT COALESCE(SUM(`Jumlah_Bayar`), 0.00) INTO v_total_pengeluaran
                    FROM `PENGELUARAN`;

                    -- 6. Saldo Kas Riil (Total Pemasukan Lunas - Total Pengeluaran)
                    SET v_saldo_kas_riil = v_total_pemasukan - v_total_pengeluaran;

                    -- Return single summary row
                    SELECT 
                        v_total_pengajuan AS total_pengajuan,
                        v_menunggu_approval AS menunggu_approval,
                        v_pemasukan_bulan_ini AS pemasukan_bulan_ini,
                        v_saldo_kas_riil AS saldo_kas_riil,
                        v_total_pemasukan AS total_pemasukan,
                        v_total_pengeluaran AS total_pengeluaran;
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
            DB::unprepared("DROP PROCEDURE IF EXISTS `sp_dashboard_summary_rt`;");
        }
    }
};
