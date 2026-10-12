<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitizenPortalController;
use App\Http\Controllers\LetterPdfController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentGatewayController;

/*
|--------------------------------------------------------------------------
| Authentication Routes - Single Sign-On (SSO) Portal
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'loginView'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/admin/login', function () {
    return redirect()->route('login');
})->name('filament.admin.auth.login');
Route::get('/register', [AuthController::class, 'registerView'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Google OAuth Single Sign-On (SSO)
Route::get('/auth/google/redirect', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'callback'])->name('auth.google.callback');
Route::get('/auth/google/link', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'linkView'])->name('auth.google.link');
Route::post('/auth/google/link', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'handleLink'])->name('auth.google.link.post');
Route::post('/auth/google/unlink', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'unlink'])->name('auth.google.unlink');

/*
|--------------------------------------------------------------------------
| Progressive Web App (PWA) Assets & Handlers
|--------------------------------------------------------------------------
*/
Route::get('/manifest.json', function () {
    return response(file_get_contents(public_path('manifest.json')), 200, [
        'Content-Type' => 'application/manifest+json; charset=utf-8',
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->name('pwa.manifest');

Route::get('/sw.js', function () {
    return response(file_get_contents(public_path('sw.js')), 200, [
        'Content-Type' => 'application/javascript; charset=utf-8',
        'Cache-Control' => 'no-cache, must-revalidate',
    ]);
})->name('pwa.sw');

Route::get('/offline.html', function () {
    return response(file_get_contents(public_path('offline.html')), 200, [
        'Content-Type' => 'text/html; charset=utf-8',
    ]);
})->name('pwa.offline');

/*
|--------------------------------------------------------------------------
| Root Route Gateway (Citizen Gate First)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth('warga')->check()) {
        return redirect()->route('portal.home');
    }
    if (auth('web')->check()) {
        return redirect()->route('cms.dashboard');
    }
    return redirect()->route('login');
})->name('root');

/*
|--------------------------------------------------------------------------
| Web Routes - RuangWarga Citizen Self-Service Portal (Protected)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:warga,web'])->group(function () {
    Route::get('/portal', [CitizenPortalController::class, 'index'])->name('portal.home');
    Route::get('/portal/index', [CitizenPortalController::class, 'index'])->name('portal.index');

    // Interactive citizen endpoints (Strict Self-Data Scoping)
    Route::get('/api/cek-iuran', [CitizenPortalController::class, 'cekIuran'])->name('portal.cek-iuran');
    Route::get('/api/riwayat-warga', [CitizenPortalController::class, 'getRiwayatWarga'])->name('portal.riwayat-warga');
    Route::post('/api/pilih-akun-warga', [CitizenPortalController::class, 'pilihAkunWarga'])->name('portal.pilih-akun-warga');
    Route::post('/api/pengajuan-surat', [CitizenPortalController::class, 'submitSurat'])->name('portal.submit-surat');
    Route::post('/api/konfirmasi-iuran', [CitizenPortalController::class, 'konfirmasiIuran'])->name('portal.konfirmasi-iuran');
    Route::post('/api/pinjam-aset', [CitizenPortalController::class, 'pinjamAset'])->name('portal.pinjam-aset');
    Route::post('/api/aspirasi', [CitizenPortalController::class, 'submitAspirasi'])->name('portal.submit-aspirasi');
    Route::post('/api/usul-kegiatan', [CitizenPortalController::class, 'usulKegiatan'])->name('portal.usul-kegiatan');
    Route::post('/portal/usul-kegiatan', [CitizenPortalController::class, 'usulKegiatan']);
    Route::post('/api/edit-profil', [CitizenPortalController::class, 'editProfil'])->name('portal.edit-profil');

    // Payment Gateway Midtrans (Snap QRIS)
    Route::post('/api/payment/snap-token', [PaymentGatewayController::class, 'requestSnapToken'])->name('payment.snap-token');
    Route::post('/api/payment/simulate-success', [PaymentGatewayController::class, 'simulateSuccess'])->name('payment.simulate-success');
    Route::get('/api/payment/check-status', [PaymentGatewayController::class, 'checkStatus'])->name('payment.check-status');

    // Official letter PDF download
    Route::get('/surat/pdf/download/{id}', [LetterPdfController::class, 'downloadPdf'])->name('surat.pdf.download');

    // Halaman Khusus Portal Warga
    Route::get('/portal/layanan', [CitizenPortalController::class, 'layanan'])->name('portal.layanan');
    Route::get('/portal/agenda', [CitizenPortalController::class, 'agenda'])->name('portal.agenda');
    Route::get('/portal/transparansi', [CitizenPortalController::class, 'transparansi'])->name('portal.transparansi');
    Route::get('/portal/aspirasi', [CitizenPortalController::class, 'aspirasi'])->name('portal.aspirasi');
});

// Midtrans Webhook Notification Callback (No auth, exempt from CSRF)
Route::post('/api/payment/midtrans/webhook', [PaymentGatewayController::class, 'handleWebhook'])->name('payment.midtrans.webhook');

// Shortcut redirects
Route::get('/layanan', function () {
    return redirect()->route('portal.layanan');
})->name('layanan');

Route::get('/agenda', function () {
    return redirect()->route('portal.agenda');
})->name('agenda');

Route::get('/kegiatan', function () {
    return redirect()->route('portal.agenda');
})->name('kegiatan');

Route::get('/transparansi', function () {
    return redirect()->route('portal.transparansi');
})->name('transparansi');

Route::get('/aspirasi', function () {
    return redirect()->route('portal.aspirasi');
})->name('aspirasi');

// Public verification for QR Code scanning
Route::get('/surat/verify/{id}', [LetterPdfController::class, 'verifyLetter'])->name('surat.verify');


/*
|--------------------------------------------------------------------------
| CMS Routes - RuangWarga Filament Admin Panel
| All officer traffic now directs to Laravel Filament CMS (/admin)
|--------------------------------------------------------------------------
*/

Route::prefix('cms')->name('cms.')->middleware(['auth:web', 'role:super_admin,admin_rt,bendahara,sekretaris'])->group(function () {
    Route::get('/', fn () => redirect('/admin'))->name('index');
    Route::get('/dashboard', fn () => redirect('/admin'))->name('dashboard');
    Route::get('/approvals', fn () => redirect('/admin/approvals'))->name('approvals');
    Route::get('/community', fn () => redirect('/admin/kegiatans'))->name('community');

    Route::middleware('role:super_admin')->get('/master-rt', fn () => redirect('/admin/data-rts'))->name('master-rt');
    Route::middleware('role:super_admin,admin_rt,bendahara')->get('/finance', fn () => redirect('/admin/pemasukans'))->name('finance');
    Route::middleware('role:super_admin,admin_rt,bendahara')->get('/inventory', fn () => redirect('/admin/inventaris'))->name('inventory');
    Route::middleware('role:super_admin,admin_rt,sekretaris')->get('/citizens', fn () => redirect('/admin/wargas'))->name('citizens');
    Route::middleware('role:super_admin,admin_rt,sekretaris')->get('/letters', fn () => redirect('/admin/surats'))->name('letters');
    Route::middleware('role:super_admin')->get('/officers', fn () => redirect('/admin/admins'))->name('officers');
});

// Also support direct /dashboard alias
Route::get('/dashboard', fn () => redirect('/admin'));

// Helper untuk inisialisasi hosting tanpa SSH (seperti InfinityFree)
Route::get('/hosting-setup', function () {
    $results = [];
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $results[] = 'Storage link: Berhasil';
    } catch (\Throwable $e) {
        $results[] = 'Storage link: ' . $e->getMessage();
    }

    try {
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        $results[] = 'Cache clear: Berhasil';
    } catch (\Throwable $e) {
        $results[] = 'Cache clear: ' . $e->getMessage();
    }

    return response('<h2>Status Setup Hosting Smart RT</h2><ul><li>' . implode('</li><li>', $results) . '</li></ul><p>Setup selesai! Anda sekarang bisa mengakses <a href="/">Beranda</a> atau <a href="/login">Login</a>.</p>');
});

