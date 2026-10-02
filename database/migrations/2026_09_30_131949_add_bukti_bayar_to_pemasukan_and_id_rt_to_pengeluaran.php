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
        if (Schema::hasTable('PEMASUKAN') && !Schema::hasColumn('PEMASUKAN', 'bukti_bayar')) {
            Schema::table('PEMASUKAN', function (Blueprint $table) {
                $table->string('bukti_bayar', 255)->nullable()->after('status_tagihan');
            });
        }

        if (Schema::hasTable('PENGELUARAN') && !Schema::hasColumn('PENGELUARAN', 'ID_RT')) {
            Schema::table('PENGELUARAN', function (Blueprint $table) {
                $table->unsignedBigInteger('ID_RT')->nullable()->default(1)->after('Pengeluaran_id');
                if (Schema::hasTable('DATA_RT')) {
                    $table->foreign('ID_RT')->references('ID_RT')->on('DATA_RT')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('PEMASUKAN') && Schema::hasColumn('PEMASUKAN', 'bukti_bayar')) {
            Schema::table('PEMASUKAN', function (Blueprint $table) {
                $table->dropColumn('bukti_bayar');
            });
        }

        if (Schema::hasTable('PENGELUARAN') && Schema::hasColumn('PENGELUARAN', 'ID_RT')) {
            Schema::table('PENGELUARAN', function (Blueprint $table) {
                $table->dropForeign(['ID_RT']);
                $table->dropColumn('ID_RT');
            });
        }
    }
};
