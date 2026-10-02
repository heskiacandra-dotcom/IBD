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
        if (Schema::hasTable('SURAT') && !Schema::hasColumn('SURAT', 'nomor_surat')) {
            Schema::table('SURAT', function (Blueprint $table) {
                $table->string('nomor_surat', 100)->nullable()->after('perihal');
            });
        }

        if (Schema::hasTable('KEGIATAN') && !Schema::hasColumn('KEGIATAN', 'status')) {
            Schema::table('KEGIATAN', function (Blueprint $table) {
                $table->string('status', 30)->default('Mendatang')->after('deskripsi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('SURAT') && Schema::hasColumn('SURAT', 'nomor_surat')) {
            Schema::table('SURAT', function (Blueprint $table) {
                $table->dropColumn('nomor_surat');
            });
        }

        if (Schema::hasTable('KEGIATAN') && Schema::hasColumn('KEGIATAN', 'status')) {
            Schema::table('KEGIATAN', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
