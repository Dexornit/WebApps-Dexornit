<?php
/**
 * Dexornit Store - Public Entry Point
 * Laravel 12 Application
 */

define('LARAVEL_START', microtime(true));

// ─── Safety: Check vendor ────────────────────────────────────────────────────
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    http_response_code(503);
    die('<h2 style="font-family:monospace;padding:2rem;color:#c0392b">⚠ Vendor folder missing!<br><small>Upload the <code>vendor/</code> folder or run <code>composer install</code> on the server.</small></h2>');
}

// ─── Boot Laravel ────────────────────────────────────────────────────────────
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());
