<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ─────────────────────────────────────────────────────────────
// STEP 1: Auto-copy .env.example → .env if .env doesn't exist
// ─────────────────────────────────────────────────────────────
if (!file_exists(__DIR__ . '/../.env')) {
    if (file_exists(__DIR__ . '/../.env.example')) {
        copy(__DIR__ . '/../.env.example', __DIR__ . '/../.env');
    } else {
        // Create a minimal .env file so Laravel can boot
        file_put_contents(__DIR__ . '/../.env',
            "APP_NAME=\"Dexornit Store\"\n" .
            "APP_ENV=production\n" .
            "APP_KEY=\n" .
            "APP_DEBUG=false\n" .
            "APP_URL=http://localhost\n\n" .
            "DB_CONNECTION=sqlite\n\n" .
            "SESSION_DRIVER=file\n" .
            "CACHE_STORE=file\n" .
            "QUEUE_CONNECTION=sync\n"
        );
    }
}

// ─────────────────────────────────────────────────────────────
// STEP 2: Ensure critical storage directories exist
// ─────────────────────────────────────────────────────────────
$storageDirs = [
    __DIR__ . '/../storage/app',
    __DIR__ . '/../storage/app/public',
    __DIR__ . '/../storage/framework',
    __DIR__ . '/../storage/framework/cache',
    __DIR__ . '/../storage/framework/cache/data',
    __DIR__ . '/../storage/framework/sessions',
    __DIR__ . '/../storage/framework/views',
    __DIR__ . '/../storage/logs',
    __DIR__ . '/../bootstrap/cache',
];
foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
}

// ─────────────────────────────────────────────────────────────
// STEP 3: Fix double /public/ in URL (common on shared hosting)
// ─────────────────────────────────────────────────────────────
$url = $_SERVER['REQUEST_URI'] ?? '/';
if (strpos($url, '/public/') !== false) {
    $url = str_replace('public/', '', $url);
    header('Location: ' . $url, true, 301);
    exit();
}

// ─────────────────────────────────────────────────────────────
// STEP 4: Check server requirements before booting Laravel
// (only shown if requirements fail)
// ─────────────────────────────────────────────────────────────
require __DIR__ . '/requirements.php';

// ─────────────────────────────────────────────────────────────
// STEP 5: Boot Laravel normally
// ─────────────────────────────────────────────────────────────

// Check maintenance mode
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel and handle the request
/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());
