<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ─────────────────────────────────────────────────────────────
// STEP 1: Check if installed - redirect to installer if not
// ─────────────────────────────────────────────────────────────
$installedMarker = __DIR__ . '/../storage/app/.installed';
if (!file_exists($installedMarker)) {
    // Not installed - redirect to installer
    if (!isset($_SERVER['REQUEST_URI']) || strpos($_SERVER['REQUEST_URI'], 'install.php') === false) {
        header('Location: /install.php');
        exit;
    }
}

// ─────────────────────────────────────────────────────────────
// STEP 2: Auto-copy .env.example → .env if .env doesn't exist
// ─────────────────────────────────────────────────────────────
if (!file_exists(__DIR__ . '/../.env')) {
    if (file_exists(__DIR__ . '/../.env.example')) {
        copy(__DIR__ . '/../.env.example', __DIR__ . '/../.env');
    }
}

// ─────────────────────────────────────────────────────────────
// STEP 3: Ensure critical storage directories exist
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
// STEP 4: Check server requirements (only shown if requirements fail)
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
