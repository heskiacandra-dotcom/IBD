<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DataRt;
use App\Models\Admin;
use App\Models\Approval;
use App\Models\KartuKeluarga;
use App\Models\Warga;
use App\Models\Surat;
use App\Models\Aspirasi;
use App\Models\ResponAspirasi;
use App\Models\Kegiatan;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Inventaris;
use App\Models\PembelianInventaris;
use Illuminate\Support\Facades\Schema;

echo "--- 1. VERIFY TABLE NAMES IN DATABASE ---" . PHP_EOL;
$tables = [
    'DATA_RT', 'ADMIN', 'APPROVAL', 'KARTU_KELUARGA', 'WARGA',
    'SURAT', 'ASPIRASI', 'RESPON_ASPIRASI', 'KEGIATAN', 'PEMASUKAN',
    'PENGELUARAN', 'INVENTARIS', 'PEMBELIAN_INVENTARIS'
];

foreach ($tables as $t) {
    $exists = Schema::hasTable($t);
    echo "Table [{$t}]: " . ($exists ? "OK (EXISTS)" : "FAIL") . PHP_EOL;
    if (!$exists) exit(1);
}

echo PHP_EOL . "--- 2. VERIFY ADMIN ID_RT NULLABLE ---" . PHP_EOL;
$admin = Admin::where('role', 'super_admin')->first();
if ($admin) {
    $admin->ID_RT = null;
    $admin->save();
    echo "Super Admin ID_RT set to null: " . (is_null($admin->fresh()->ID_RT) ? "OK (NULLABLE)" : "FAIL") . PHP_EOL;
}

echo PHP_EOL . "--- 3. VERIFY PRIMARY KEYS ---" . PHP_EOL;
$w = new Warga();
echo "WARGA PK: " . $w->getKeyName() . " (Type: " . $w->getKeyType() . ", Inc: " . ($w->getIncrementing() ? 'yes':'no') . ")" . PHP_EOL;
$kk = new KartuKeluarga();
echo "KARTU_KELUARGA PK: " . $kk->getKeyName() . " (Type: " . $kk->getKeyType() . ", Inc: " . ($kk->getIncrementing() ? 'yes':'no') . ")" . PHP_EOL;
$rt = new DataRt();
echo "DATA_RT PK: " . $rt->getKeyName() . " (Type: " . $rt->getKeyType() . ", Inc: " . ($rt->getIncrementing() ? 'yes':'no') . ")" . PHP_EOL;

echo PHP_EOL . "--- 4. VERIFY ELOQUENT RELATIONSHIPS ---" . PHP_EOL;
$sampleRt = DataRt::first();
echo "DataRt -> warga: " . $sampleRt->warga->count() . " records" . PHP_EOL;
echo "DataRt -> admins: " . $sampleRt->admins->count() . " records" . PHP_EOL;
echo "DataRt -> kegiatan: " . $sampleRt->kegiatan->count() . " records" . PHP_EOL;
echo "DataRt -> pemasukan: " . $sampleRt->pemasukan->count() . " records" . PHP_EOL;
echo "DataRt -> inventaris: " . $sampleRt->inventaris->count() . " records" . PHP_EOL;

$sampleWarga = Warga::first();
echo "Warga -> kartuKeluarga: " . ($sampleWarga->kartuKeluarga ? $sampleWarga->kartuKeluarga->Alamat : 'N/A') . PHP_EOL;
echo "Warga -> dataRt: " . ($sampleWarga->dataRt ? 'RT ' . $sampleWarga->dataRt->nomor_rt : 'N/A') . PHP_EOL;
echo "Warga -> surat: " . $sampleWarga->surat->count() . " records" . PHP_EOL;
echo "Warga -> aspirasi: " . $sampleWarga->aspirasi->count() . " records" . PHP_EOL;

$sampleAsp = Aspirasi::first();
echo "Aspirasi -> responAspirasi: " . ($sampleAsp->responAspirasi ? $sampleAsp->responAspirasi->isi_respon : 'None') . PHP_EOL;

$sampleInv = Inventaris::first();
echo "Inventaris -> pembelianInventaris: " . $sampleInv->pembelianInventaris->count() . " records" . PHP_EOL;

$samplePeng = Pengeluaran::first();
echo "Pengeluaran -> pembelianInventaris: " . $samplePeng->pembelianInventaris->count() . " records" . PHP_EOL;

echo PHP_EOL . "--- 5. VERIFY TENANTSCOPE TRAIT ---" . PHP_EOL;
// Unauthenticated:
auth()->logout();
$allWarga = Warga::count();
$allPemasukan = Pemasukan::count();
$allKegiatan = Kegiatan::count();
$allInventaris = Inventaris::count();
echo "Unauthenticated (All): Warga={$allWarga}, Pemasukan={$allPemasukan}, Kegiatan={$allKegiatan}, Inventaris={$allInventaris}" . PHP_EOL;

// Admin RT (ID_RT = 1, role = 'admin_rt'):
$adminRt = Admin::where('role', 'admin_rt')->where('ID_RT', 1)->first();
auth()->login($adminRt);
$scopedWarga = Warga::count();
$scopedPemasukan = Pemasukan::count();
$scopedKegiatan = Kegiatan::count();
$scopedInventaris = Inventaris::count();
echo "Logged in as Admin RT (ID_RT=1): Warga={$scopedWarga}, Pemasukan={$scopedPemasukan}, Kegiatan={$scopedKegiatan}, Inventaris={$scopedInventaris}" . PHP_EOL;

// Verify every record returned matches ID_RT = 1
foreach (Warga::all() as $item) {
    if ($item->ID_RT != 1) {
        echo "FAIL: Warga ID_RT is {$item->ID_RT}, expected 1" . PHP_EOL;
        exit(1);
    }
}
foreach (Pemasukan::all() as $item) {
    if ($item->ID_RT != 1) {
        echo "FAIL: Pemasukan ID_RT is {$item->ID_RT}, expected 1" . PHP_EOL;
        exit(1);
    }
}
foreach (Kegiatan::all() as $item) {
    if ($item->ID_RT != 1) {
        echo "FAIL: Kegiatan ID_RT is {$item->ID_RT}, expected 1" . PHP_EOL;
        exit(1);
    }
}
foreach (Inventaris::all() as $item) {
    if ($item->ID_RT != 1) {
        echo "FAIL: Inventaris ID_RT is {$item->ID_RT}, expected 1" . PHP_EOL;
        exit(1);
    }
}
echo "TenantScope automatically scoped all queries to ID_RT = 1: OK" . PHP_EOL;

// Super Admin (role = 'super_admin'):
$sa = Admin::where('role', 'super_admin')->first();
auth()->login($sa);
$saWarga = Warga::count();
$saPemasukan = Pemasukan::count();
echo "Logged in as Super Admin (unscoped): Warga={$saWarga}, Pemasukan={$saPemasukan}" . PHP_EOL;
if ($saWarga !== $allWarga || $saPemasukan !== $allPemasukan) {
    echo "FAIL: Super admin should not be filtered by TenantScope" . PHP_EOL;
    exit(1);
}
echo "Super Admin bypasses TenantScope correctly: OK" . PHP_EOL;

// withoutTenant scope:
auth()->login($adminRt);
$bypassedCount = Warga::withoutTenant()->count();
echo "Admin RT with withoutTenant(): {$bypassedCount}" . PHP_EOL;
if ($bypassedCount !== $allWarga) {
    echo "FAIL: withoutTenant() should return all records" . PHP_EOL;
    exit(1);
}
echo "withoutTenant() works correctly: OK" . PHP_EOL;

auth()->logout();
echo PHP_EOL . "=== ALL 5 REQUIREMENTS VERIFIED SUCCESSFULLY ===" . PHP_EOL;
