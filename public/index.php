<?php
/**
 * Dexornit Store — Public Entry Point
 * Pola TMail: requirements.php di-include dulu, jika gagal → halt otomatis
 */

// ─── Requirements Check (auto-halt jika ada yang missing) ───────────────────
require 'requirements.php';

// ─── Boot Laravel ────────────────────────────────────────────────────────────
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());
