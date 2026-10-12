<?php

/**
 * Smart RT - InfinityFree Entry Point & Error Diagnostic
 */

// Tampilkan error jika terjadi crash di server hosting
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

define('LARAVEL_START', microtime(true));

// 1. Cek folder vendor
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Smart RT - Setup Diperlukan</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; line-height: 1.6; }
            .card { max-width: 650px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border: 1px solid #334155; }
            h2 { color: #f59e0b; margin-top: 0; }
            code { background: #0f172a; padding: 2px 8px; border-radius: 4px; color: #38bdf8; font-size: 0.9em; }
            .box { background: #334155; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #f59e0b; }
        </style>
    </head>
    <body>
        <div class="card">
            <h2>⚠️ Folder <code>vendor/</code> Belum Ter-upload</h2>
            <div class="box">
                InfinityFree belum memiliki folder <code>vendor</code> (karena GitHub mengabaikan folder ini demi alasan keamanan/ukuran).
            </div>
            <p><strong>Langkah Perbaikan:</strong></p>
            <ol>
                <li>Buka File Manager di InfinityFree, masuk ke folder <code>htdocs/</code>.</li>
                <li>Upload folder <code>vendor</code> dari komputer lokal Anda (atau kompres <code>c:\laragon\www\ibd\vendor</code> menjadi <code>vendor.zip</code>, upload lalu ekstrak di dalam <code>htdocs/</code>).</li>
            </ol>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 2. Cek file .env
if (!file_exists(__DIR__ . '/.env')) {
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Smart RT - Setup Diperlukan</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; line-height: 1.6; }
            .card { max-width: 650px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border: 1px solid #334155; }
            h2 { color: #ef4444; margin-top: 0; }
            code { background: #0f172a; padding: 2px 8px; border-radius: 4px; color: #38bdf8; font-size: 0.9em; }
            .box { background: #334155; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #ef4444; }
        </style>
    </head>
    <body>
        <div class="card">
            <h2>⚠️ File <code>.env</code> Belum Ada</h2>
            <div class="box">
                File konfigurasi <code>.env</code> belum ditemukan di folder <code>htdocs/</code>.
            </div>
            <p><strong>Langkah Perbaikan:</strong></p>
            <ol>
                <li>Buka File Manager di InfinityFree, masuk ke folder <code>htdocs/</code>.</li>
                <li>Buat file baru bernama <code>.env</code>.</li>
                <li>Salin isi konfigurasi dari file <code>.env.infinityfree</code> di proyek Anda.</li>
            </ol>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 3. Pastikan folder storage & bootstrap/cache ada
$requiredDirs = [
    __DIR__ . '/storage/framework/views',
    __DIR__ . '/storage/framework/cache',
    __DIR__ . '/storage/framework/sessions',
    __DIR__ . '/storage/logs',
    __DIR__ . '/bootstrap/cache',
];
foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

try {
    // Maintenance mode
    if (file_exists($maintenance = __DIR__ . '/storage/framework/maintenance.php')) {
        require $maintenance;
    }

    // Composer autoloader
    require __DIR__ . '/vendor/autoload.php';

    // Bootstrap Laravel application
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/bootstrap/app.php';

    $app->handleRequest(\Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Smart RT - Error Diagnostik</title>
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; line-height: 1.6; }
            .card { max-width: 800px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border: 1px solid #334155; }
            h2 { color: #ef4444; margin-top: 0; }
            pre { background: #0f172a; padding: 15px; border-radius: 8px; overflow-x: auto; font-size: 13px; color: #cbd5e1; border: 1px solid #334155; }
            .box { background: #334155; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #ef4444; }
        </style>
    </head>
    <body>
        <div class="card">
            <h2>❌ Detail Kendala (Laravel Error)</h2>
            <div class="box">
                <strong>Pesan:</strong> <?= htmlspecialchars($e->getMessage()) ?><br>
                <strong>File:</strong> <?= htmlspecialchars($e->getFile()) ?> (baris <?= $e->getLine() ?>)
            </div>
            <h3>Stack Trace:</h3>
            <pre><?= htmlspecialchars($e->getTraceAsString()) ?></pre>
        </div>
    </body>
    </html>
    <?php
}
