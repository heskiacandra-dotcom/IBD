<?php

/**
 * Smart RT - Diagnostic Checker for InfinityFree Hosting
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$results = [];

// 1. PHP Version
$phpVersion = PHP_VERSION;
$phpOk = version_compare($phpVersion, '8.2.0', '>=');
$results[] = [
    'title' => 'Versi PHP Server',
    'status' => $phpOk,
    'detail' => "Saat ini: <b>PHP {$phpVersion}</b> (Laravel 11 membutuhkan minimal PHP 8.2)",
    'fix' => $phpOk ? null : 'Buka cPanel InfinityFree > cari menu "Select PHP Version" > ganti versi PHP ke 8.2 atau 8.3.'
];

// 2. Folder vendor/
$vendorExists = file_exists(__DIR__ . '/vendor/autoload.php');
$results[] = [
    'title' => 'Folder vendor/ (Dependencies)',
    'status' => $vendorExists,
    'detail' => $vendorExists ? 'Folder <code>vendor/autoload.php</code> ditemukan.' : 'Folder <code>vendor/</code> BELUM ditemukan di htdocs!',
    'fix' => $vendorExists ? null : 'Upload folder <code>vendor/</code> dari komputer Anda ke dalam <code>htdocs/</code> di File Manager.'
];

// 3. File .env
$envExists = file_exists(__DIR__ . '/.env');
$results[] = [
    'title' => 'File Konfigurasi (.env)',
    'status' => $envExists,
    'detail' => $envExists ? 'File <code>.env</code> ditemukan di root htdocs.' : 'File <code>.env</code> belum ada di htdocs!',
    'fix' => $envExists ? null : 'Buat file <code>.env</code> di <code>htdocs/</code> lalu salin konfigurasi dari .env.infinityfree.'
];

// 4. APP_KEY di .env
$hasAppKey = false;
$dbHost = '';
$dbName = '';
$dbUser = '';
$dbPass = '';
if ($envExists) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    if (preg_match('/^APP_KEY=(.+)$/m', $envContent, $m) && !empty(trim($m[1]))) {
        $hasAppKey = true;
    }
    if (preg_match('/^DB_HOST=(.+)$/m', $envContent, $m)) $dbHost = trim($m[1]);
    if (preg_match('/^DB_DATABASE=(.+)$/m', $envContent, $m)) $dbName = trim($m[1]);
    if (preg_match('/^DB_USERNAME=(.+)$/m', $envContent, $m)) $dbUser = trim($m[1]);
    if (preg_match('/^DB_PASSWORD=(.*)$/m', $envContent, $m)) $dbPass = trim($m[1]);
}
$results[] = [
    'title' => 'APP_KEY di .env',
    'status' => $hasAppKey,
    'detail' => $hasAppKey ? 'APP_KEY terpasang dengan benar.' : 'APP_KEY belum terisi di .env!',
    'fix' => $hasAppKey ? null : 'Buka file .env di File Manager dan pastikan APP_KEY terisi (misal: base64:...).'
];

// 5. Database Connection
$dbConnected = false;
$dbError = '';
if ($dbHost && $dbName && $dbUser) {
    try {
        $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
        $dbConnected = true;
    } catch (\Throwable $e) {
        $dbError = $e->getMessage();
    }
}
$results[] = [
    'title' => 'Koneksi Database MySQL',
    'status' => $dbConnected,
    'detail' => $dbConnected 
        ? "Berhasil terhubung ke database <code>{$dbName}</code> di <code>{$dbHost}</code>." 
        : "Gagal terhubung ke MySQL. Error: " . htmlspecialchars($dbError ?: 'Konfigurasi DB kosong.'),
    'fix' => $dbConnected ? null : 'Periksa kembali DB_HOST, DB_DATABASE, DB_USERNAME, dan DB_PASSWORD di file .env Anda.'
];

// 6. Storage & Cache Write Permissions
$storageWritable = is_writable(__DIR__ . '/storage') && is_writable(__DIR__ . '/bootstrap/cache');
$results[] = [
    'title' => 'Izin Tulis (storage/ & bootstrap/cache)',
    'status' => $storageWritable,
    'detail' => $storageWritable ? 'Folder storage dan cache dapat ditulisi (writable).' : 'Folder storage/ atau bootstrap/cache tidak dapat ditulisi!',
    'fix' => $storageWritable ? null : 'Pastikan permission folder storage dan bootstrap/cache diatur ke 775 atau 777 di File Manager.'
];

// 7. Assets Build
$buildExists = file_exists(__DIR__ . '/public/build/manifest.json');
$results[] = [
    'title' => 'Aset Frontend (public/build/)',
    'status' => $buildExists,
    'detail' => $buildExists ? 'File aset frontend <code>public/build/manifest.json</code> ditemukan.' : 'Folder <code>public/build/</code> belum ada di hosting.',
    'fix' => $buildExists ? null : 'Upload folder <code>public/build</code> dari komputer lokal ke <code>htdocs/public/build</code> agar tampilan CSS & JS rapi.'
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Smart RT - InfinityFree Diagnostic Health Check</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; line-height: 1.6; }
        .container { max-width: 750px; margin: 0 auto; }
        h1 { color: #38bdf8; font-size: 24px; margin-bottom: 5px; }
        p.sub { color: #94a3b8; margin-top: 0; margin-bottom: 25px; }
        .card { background: #1e293b; border-radius: 10px; padding: 20px; margin-bottom: 15px; border: 1px solid #334155; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: bold; font-size: 13px; margin-bottom: 8px; }
        .badge-success { background: #065f46; color: #34d399; }
        .badge-danger { background: #7f1d1d; color: #f87171; }
        .fix { background: #334155; padding: 10px 14px; border-radius: 6px; margin-top: 10px; font-size: 14px; color: #fbbf24; border-left: 4px solid #f59e0b; }
        code { background: #0f172a; padding: 2px 6px; border-radius: 4px; color: #38bdf8; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🩺 Smart RT - Diagnostic System Check</h1>
        <p class="sub">Pemeriksaan otomatis konfigurasi server InfinityFree</p>

        <?php foreach ($results as $item): ?>
            <div class="card">
                <div>
                    <?php if ($item['status']): ?>
                        <span class="badge badge-success">✓ AMAN / OK</span>
                    <?php else: ?>
                        <span class="badge badge-danger">✗ PERLU TINDAKAN</span>
                    <?php endif; ?>
                </div>
                <h3 style="margin: 5px 0 10px 0;"><?= $item['title'] ?></h3>
                <div><?= $item['detail'] ?></div>
                <?php if (!$item['status'] && $item['fix']): ?>
                    <div class="fix">💡 <b>Solusi:</b> <?= $item['fix'] ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
