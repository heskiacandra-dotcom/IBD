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
        Schema::table('PEMASUKAN', function (Blueprint $table) {
            if (!Schema::hasColumn('PEMASUKAN', 'snap_token')) {
                $table->string('snap_token', 255)->nullable()->after('bukti_bayar');
            }
            if (!Schema::hasColumn('PEMASUKAN', 'order_id')) {
                $table->string('order_id', 100)->nullable()->index()->after('snap_token');
            }
            if (!Schema::hasColumn('PEMASUKAN', 'metode_pembayaran')) {
                $table->string('metode_pembayaran', 50)->nullable()->after('order_id');
            }
            if (!Schema::hasColumn('PEMASUKAN', 'paid_at')) {
                $table->dateTime('paid_at')->nullable()->after('metode_pembayaran');
            }
            if (!Schema::hasColumn('PEMASUKAN', 'payment_payload')) {
                $table->json('payment_payload')->nullable()->after('paid_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('PEMASUKAN')) {
            try {
                \Illuminate\Support\Facades\DB::statement('DROP INDEX IF EXISTS pemasukan_order_id_index');
            } catch (\Throwable $e) {
                // Ignore if index doesn't exist
            }

            Schema::table('PEMASUKAN', function (Blueprint $table) {
                $cols = [];
                foreach (['snap_token', 'order_id', 'metode_pembayaran', 'paid_at', 'payment_payload'] as $col) {
                    if (Schema::hasColumn('PEMASUKAN', $col)) {
                        $cols[] = $col;
                    }
                }
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
