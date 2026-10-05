<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('KONTAK_RT')) {
            Schema::create('KONTAK_RT', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ID_RT')->nullable();
                $table->foreign('ID_RT')->references('ID_RT')->on('DATA_RT')->onDelete('cascade');
                $table->string('role', 100);
                $table->string('nama_kontak', 150);
                $table->string('nomor_telepon', 50);
                $table->boolean('is_whatsapp')->default(true);
                $table->string('pesan_wa', 255)->nullable();
                $table->string('lokasi_tampilan', 50)->default('footer_darurat');
                $table->integer('urutan')->default(0);
                $table->boolean('is_aktif')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('DATA_RT')) {
            Schema::table('DATA_RT', function (Blueprint $table) {
                if (!Schema::hasColumn('DATA_RT', 'no_telp_ketua_rt')) {
                    $table->string('no_telp_ketua_rt', 50)->nullable()->after('nama_ketua_rt');
                }
                if (!Schema::hasColumn('DATA_RT', 'no_telp_sekretariat')) {
                    $table->string('no_telp_sekretariat', 50)->nullable()->after('no_telp_ketua_rt');
                }
            });
        }

        $rtId = Schema::hasTable('DATA_RT') ? DB::table('DATA_RT')->value('ID_RT') : null;

        $defaultContacts = [
            [
                'ID_RT' => $rtId,
                'role' => 'Ketua RT 05',
                'nama_kontak' => 'Bpk. Sukardi Hartono',
                'nomor_telepon' => '0812-3456-7890',
                'is_whatsapp' => true,
                'pesan_wa' => 'Halo Ketua RT 05, saya warga Harmoni Residence',
                'lokasi_tampilan' => 'footer_darurat',
                'urutan' => 1,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ID_RT' => $rtId,
                'role' => 'Pos Ronda & Keamanan',
                'nama_kontak' => 'Piket Regu Sekuriti',
                'nomor_telepon' => '0821-9876-5432',
                'is_whatsapp' => true,
                'pesan_wa' => 'Halo Pos Ronda RT 05, saya warga ingin melapor keadaan keamanan',
                'lokasi_tampilan' => 'footer_darurat',
                'urutan' => 2,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ID_RT' => $rtId,
                'role' => 'Bhabinkamtibmas Polsek',
                'nama_kontak' => 'Aiptu Hendro Wibowo',
                'nomor_telepon' => '0813-8822-1100',
                'is_whatsapp' => true,
                'pesan_wa' => 'Halo Bhabinkamtibmas Polsek, saya warga RT 05 Harmoni Residence',
                'lokasi_tampilan' => 'footer_darurat',
                'urutan' => 3,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ID_RT' => $rtId,
                'role' => 'Ambulans & Damkar',
                'nama_kontak' => 'Posko Siaga Darurat',
                'nomor_telepon' => '112',
                'is_whatsapp' => false,
                'pesan_wa' => null,
                'lokasi_tampilan' => 'footer_darurat',
                'urutan' => 4,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ID_RT' => $rtId,
                'role' => 'Sekretariat Lingkungan',
                'nama_kontak' => 'Balai Warga RT 05 / RW 01',
                'nomor_telepon' => '0812-3456-7890',
                'is_whatsapp' => true,
                'pesan_wa' => 'Halo Pengurus RT 05, saya warga Harmoni Residence',
                'lokasi_tampilan' => 'sekretariat',
                'urutan' => 5,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($defaultContacts as $contact) {
            DB::table('KONTAK_RT')->insertOrIgnore($contact);
        }

        if (Schema::hasTable('DATA_RT') && $rtId) {
            DB::table('DATA_RT')->where('ID_RT', $rtId)->update([
                'no_telp_ketua_rt' => '0812-3456-7890',
                'no_telp_sekretariat' => '0812-3456-7890',
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('KONTAK_RT');
        if (Schema::hasTable('DATA_RT')) {
            Schema::table('DATA_RT', function (Blueprint $table) {
                if (Schema::hasColumn('DATA_RT', 'no_telp_ketua_rt')) {
                    $table->dropColumn('no_telp_ketua_rt');
                }
                if (Schema::hasColumn('DATA_RT', 'no_telp_sekretariat')) {
                    $table->dropColumn('no_telp_sekretariat');
                }
            });
        }
    }
};
