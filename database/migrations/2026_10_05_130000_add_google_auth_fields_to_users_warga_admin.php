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
        // 1. Tambah kolom email, google_id, avatar pada tabel WARGA
        if (Schema::hasTable('WARGA')) {
            Schema::table('WARGA', function (Blueprint $table) {
                if (!Schema::hasColumn('WARGA', 'email')) {
                    $table->string('email', 150)->nullable()->unique()->after('no_telp');
                }
                if (!Schema::hasColumn('WARGA', 'google_id')) {
                    $table->string('google_id', 100)->nullable()->index()->after('email');
                }
                if (!Schema::hasColumn('WARGA', 'avatar')) {
                    $table->text('avatar')->nullable()->after('google_id');
                }
            });
        }

        // 2. Tambah kolom email, google_id, avatar pada tabel ADMIN
        if (Schema::hasTable('ADMIN')) {
            Schema::table('ADMIN', function (Blueprint $table) {
                if (!Schema::hasColumn('ADMIN', 'email')) {
                    $table->string('email', 150)->nullable()->unique()->after('Username');
                }
                if (!Schema::hasColumn('ADMIN', 'google_id')) {
                    $table->string('google_id', 100)->nullable()->index()->after('email');
                }
                if (!Schema::hasColumn('ADMIN', 'avatar')) {
                    $table->text('avatar')->nullable()->after('google_id');
                }
            });
        }

        // 3. Tambah kolom google_id, avatar pada tabel users (jika ada)
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'google_id')) {
                    $table->string('google_id', 100)->nullable()->index()->after('email');
                }
                if (!Schema::hasColumn('users', 'avatar')) {
                    $table->text('avatar')->nullable()->after('google_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            return;
        }

        if (Schema::hasTable('WARGA')) {
            Schema::table('WARGA', function (Blueprint $table) {
                if (Schema::hasColumn('WARGA', 'google_id')) {
                    $table->dropIndex(['google_id']);
                }
                $cols = [];
                if (Schema::hasColumn('WARGA', 'avatar')) $cols[] = 'avatar';
                if (Schema::hasColumn('WARGA', 'google_id')) $cols[] = 'google_id';
                if (Schema::hasColumn('WARGA', 'email')) $cols[] = 'email';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('ADMIN')) {
            Schema::table('ADMIN', function (Blueprint $table) {
                if (Schema::hasColumn('ADMIN', 'google_id')) {
                    $table->dropIndex(['google_id']);
                }
                $cols = [];
                if (Schema::hasColumn('ADMIN', 'avatar')) $cols[] = 'avatar';
                if (Schema::hasColumn('ADMIN', 'google_id')) $cols[] = 'google_id';
                if (Schema::hasColumn('ADMIN', 'email')) $cols[] = 'email';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'google_id')) {
                    $table->dropIndex(['google_id']);
                }
                $cols = [];
                if (Schema::hasColumn('users', 'avatar')) $cols[] = 'avatar';
                if (Schema::hasColumn('users', 'google_id')) $cols[] = 'google_id';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
