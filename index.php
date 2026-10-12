<?php

/**
 * Laravel - Shared Hosting Entry Point
 * Smart RT InfinityFree Gateway
 */

define('LARAVEL_START', microtime(true));

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
