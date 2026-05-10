<?php
/**
 * Wanseven Entry Point
 */

// Check if installed
if (!file_exists(__DIR__ . '/../storage/app/.installed')) {
    if (basename($_SERVER['PHP_SELF']) !== 'install.php') {
        header('Location: /install.php');
        exit;
    }
    exit;
}

// Check vendor
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('Vendor folder missing! Upload vendor/ folder or run: composer install');
}

// Boot Laravel
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());
