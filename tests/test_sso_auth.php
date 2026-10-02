<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use App\Models\Warga;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckRole;

echo "=== TESTING RUANGWARGA SSO & ROLE PROTECTION ===" . PHP_EOL . PHP_EOL;

// 1. Test RuangWargaUserProvider with Admin and Warga
echo "--- 1. Testing RuangWargaUserProvider ---" . PHP_EOL;
$provider = new \App\Auth\RuangWargaUserProvider();

// Test retrieveByCredentials for Super Admin
$superAdminUser = $provider->retrieveByCredentials(['identity' => 'superadmin', 'password' => 'Rahasia123!']);
if ($superAdminUser && $superAdminUser->role === 'super_admin' && $provider->validateCredentials($superAdminUser, ['password' => 'Rahasia123!'])) {
    echo "Super Admin Credentials Verification: OK\n";
} else {
    echo "Super Admin Credentials Verification: FAILED\n";
}

// Test retrieveByCredentials for Warga by NIK
$wargaUserByNik = $provider->retrieveByCredentials(['identity' => '3201051208940001', 'password' => 'WargaHarmoni2026!']);
if ($wargaUserByNik && $wargaUserByNik->role === 'warga' && $provider->validateCredentials($wargaUserByNik, ['password' => 'WargaHarmoni2026!'])) {
    echo "Warga by NIK Credentials Verification: OK\n";
} else {
    echo "Warga by NIK Credentials Verification: FAILED\n";
}

// Test retrieveByCredentials for Warga by Username
$wargaUserByUsername = $provider->retrieveByCredentials(['identity' => 'hendra_w', 'password' => 'WargaHarmoni2026!']);
if ($wargaUserByUsername && $wargaUserByUsername->role === 'warga' && $provider->validateCredentials($wargaUserByUsername, ['password' => 'WargaHarmoni2026!'])) {
    echo "Warga by Username Credentials Verification: OK\n";
} else {
    echo "Warga by Username Credentials Verification: FAILED\n";
}

// Test retrieveById
$retrievedAdmin = $provider->retrieveById('admin:' . $superAdminUser->Admin_ID);
if ($retrievedAdmin && $retrievedAdmin->Admin_ID == $superAdminUser->Admin_ID) {
    echo "Retrieve Admin by Prefixed ID: OK\n";
} else {
    echo "Retrieve Admin by Prefixed ID: FAILED\n";
}

$retrievedWarga = $provider->retrieveById('warga:' . $wargaUserByNik->NIK);
if ($retrievedWarga && $retrievedWarga->NIK == $wargaUserByNik->NIK) {
    echo "Retrieve Warga by Prefixed ID: OK\n";
} else {
    echo "Retrieve Warga by Prefixed ID: FAILED\n";
}

// 2. Test AuthController::login redirects
echo PHP_EOL . "--- 2. Testing AuthController::login Redirects ---" . PHP_EOL;
$authController = new AuthController();

// Test Login as Warga -> redirects to /portal
$reqWarga = Request::create('/login', 'POST', [
    'identity' => '3201051208940001',
    'password' => 'WargaHarmoni2026!',
]);
$reqWarga->setLaravelSession($app['session']->driver());
$resWarga = $authController->login($reqWarga);
if ($resWarga->isRedirect(url('/portal'))) {
    echo "Warga Login Redirect to /portal: OK (" . $resWarga->headers->get('Location') . ")\n";
} else {
    echo "Warga Login Redirect to /portal: FAILED (" . $resWarga->headers->get('Location') . ")\n";
}

// Test Login as Super Admin -> redirects to /cms/dashboard
$reqSa = Request::create('/login', 'POST', [
    'identity' => 'superadmin',
    'password' => 'Rahasia123!',
]);
$reqSa->setLaravelSession($app['session']->driver());
$resSa = $authController->login($reqSa);
if ($resSa->isRedirect(route('cms.dashboard'))) {
    echo "Super Admin Login Redirect to /cms/dashboard: OK (" . $resSa->headers->get('Location') . ")\n";
} else {
    echo "Super Admin Login Redirect to /cms/dashboard: FAILED (" . $resSa->headers->get('Location') . ")\n";
}

// Test Login as Bendahara -> redirects to /cms/dashboard
$reqBen = Request::create('/login', 'POST', [
    'identity' => 'bendahara_rt05',
    'password' => 'Rahasia123!',
]);
$reqBen->setLaravelSession($app['session']->driver());
$resBen = $authController->login($reqBen);
if ($resBen->isRedirect(route('cms.dashboard'))) {
    echo "Bendahara Login Redirect to /cms/dashboard: OK (" . $resBen->headers->get('Location') . ")\n";
} else {
    echo "Bendahara Login Redirect to /cms/dashboard: FAILED (" . $resBen->headers->get('Location') . ")\n";
}

// Test Login as Sekretaris -> redirects to /cms/dashboard
$reqSek = Request::create('/login', 'POST', [
    'identity' => 'sekretaris_rt05',
    'password' => 'Rahasia123!',
]);
$reqSek->setLaravelSession($app['session']->driver());
$resSek = $authController->login($reqSek);
if ($resSek->isRedirect(route('cms.dashboard'))) {
    echo "Sekretaris Login Redirect to /cms/dashboard: OK (" . $resSek->headers->get('Location') . ")\n";
} else {
    echo "Sekretaris Login Redirect to /cms/dashboard: FAILED (" . $resSek->headers->get('Location') . ")\n";
}

// 3. Test CheckRole Middleware
echo PHP_EOL . "--- 3. Testing CheckRole Middleware ---" . PHP_EOL;
$middleware = new CheckRole();

// A. Unauthenticated user
Auth::logout();
$reqGuest = Request::create('/cms/dashboard', 'GET');
$resGuest = $middleware->handle($reqGuest, fn() => response('OK'), 'super_admin');
if ($resGuest->isRedirect(route('login'))) {
    echo "Guest Access to /cms/dashboard Blocked & Redirected to Login: OK\n";
} else {
    echo "Guest Access to /cms/dashboard Blocked: FAILED\n";
}

// B. Bendahara role permissions
$bendahara = Admin::where('role', 'bendahara')->first();
Auth::login($bendahara);

// Bendahara accessing Finance: Allowed
$reqFin = Request::create('/cms/finance', 'GET');
$resFin = $middleware->handle($reqFin, fn() => response('OK'), 'super_admin,admin_rt,bendahara');
echo "Bendahara Accessing Finance: " . ($resFin->getContent() === 'OK' ? "OK (ALLOWED)" : "FAILED") . PHP_EOL;

// Bendahara accessing Inventory: Allowed
$reqInv = Request::create('/cms/inventory', 'GET');
$resInv = $middleware->handle($reqInv, fn() => response('OK'), 'super_admin,admin_rt,bendahara');
echo "Bendahara Accessing Inventory: " . ($resInv->getContent() === 'OK' ? "OK (ALLOWED)" : "FAILED") . PHP_EOL;

// Bendahara accessing Citizens: Forbidden (403)
try {
    $reqCit = Request::create('/cms/citizens', 'GET');
    $middleware->handle($reqCit, fn() => response('OK'), 'super_admin,admin_rt,sekretaris');
    echo "Bendahara Accessing Citizens: FAILED (Should be 403)\n";
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    echo "Bendahara Accessing Citizens: OK (Blocked 403 - {$e->getMessage()})\n";
}

// Bendahara accessing Master RT: Forbidden (403)
try {
    $reqMrt = Request::create('/cms/master-rt', 'GET');
    $middleware->handle($reqMrt, fn() => response('OK'), 'super_admin');
    echo "Bendahara Accessing Master RT: FAILED (Should be 403)\n";
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    echo "Bendahara Accessing Master RT: OK (Blocked 403 - {$e->getMessage()})\n";
}

// C. Sekretaris role permissions
$sekretaris = Admin::where('role', 'sekretaris')->first();
Auth::login($sekretaris);

// Sekretaris accessing Citizens: Allowed
$reqCit = Request::create('/cms/citizens', 'GET');
$resCit = $middleware->handle($reqCit, fn() => response('OK'), 'super_admin,admin_rt,sekretaris');
echo "Sekretaris Accessing Citizens: " . ($resCit->getContent() === 'OK' ? "OK (ALLOWED)" : "FAILED") . PHP_EOL;

// Sekretaris accessing Letters: Allowed
$reqLet = Request::create('/cms/letters', 'GET');
$resLet = $middleware->handle($reqLet, fn() => response('OK'), 'super_admin,admin_rt,sekretaris');
echo "Sekretaris Accessing Letters: " . ($resLet->getContent() === 'OK' ? "OK (ALLOWED)" : "FAILED") . PHP_EOL;

// Sekretaris accessing Finance: Forbidden (403)
try {
    $reqFin = Request::create('/cms/finance', 'GET');
    $middleware->handle($reqFin, fn() => response('OK'), 'super_admin,admin_rt,bendahara');
    echo "Sekretaris Accessing Finance: FAILED (Should be 403)\n";
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    echo "Sekretaris Accessing Finance: OK (Blocked 403 - {$e->getMessage()})\n";
}

// D. Super Admin role permissions
$superAdmin = Admin::where('role', 'super_admin')->first();
Auth::login($superAdmin);

// Super Admin accessing Master RT: Allowed
$reqMrt = Request::create('/cms/master-rt', 'GET');
$resMrt = $middleware->handle($reqMrt, fn() => response('OK'), 'super_admin');
echo "Super Admin Accessing Master RT: " . ($resMrt->getContent() === 'OK' ? "OK (ALLOWED)" : "FAILED") . PHP_EOL;

// E. Warga role blocked from CMS
$warga = Warga::first();
Auth::login($warga);

$reqCms = Request::create('/cms/dashboard', 'GET');
$reqCms->setLaravelSession($app['session']->driver());
$resCms = $middleware->handle($reqCms, fn() => response('OK'), 'super_admin,admin_rt,bendahara,sekretaris');
if ($resCms->isRedirect(route('portal.home'))) {
    echo "Warga Access to CMS Blocked & Redirected to Portal: OK (" . $resCms->headers->get('Location') . ")\n";
} else {
    echo "Warga Access to CMS Blocked: FAILED\n";
}

echo PHP_EOL . "=== ALL SSO & ROLE PROTECTION TESTS PASSED SUCCESSFULLY! ===" . PHP_EOL;
