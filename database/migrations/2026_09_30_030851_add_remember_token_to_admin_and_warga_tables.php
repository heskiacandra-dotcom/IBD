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
        if (Schema::hasTable('ADMIN') && !Schema::hasColumn('ADMIN', 'remember_token')) {
            Schema::table('ADMIN', function (Blueprint $table) {
                $table->rememberToken()->nullable()->after('ID_RT');
            });
        }

        if (Schema::hasTable('WARGA') && !Schema::hasColumn('WARGA', 'remember_token')) {
            Schema::table('WARGA', function (Blueprint $table) {
                $table->rememberToken()->nullable()->after('role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ADMIN') && Schema::hasColumn('ADMIN', 'remember_token')) {
            Schema::table('ADMIN', function (Blueprint $table) {
                $table->dropRememberToken();
            });
        }

        if (Schema::hasTable('WARGA') && Schema::hasColumn('WARGA', 'remember_token')) {
            Schema::table('WARGA', function (Blueprint $table) {
                $table->dropRememberToken();
            });
        }
    }
};
