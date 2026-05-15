<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ─── Early installer check ────────────────────────────────────────────────────
// If .env doesn't exist yet, the app has never been installed.
// Boot the full framework would crash with a 500. Instead, redirect immediately
// to the standalone setup wizard so the user can complete installation.
if (!file_exists(__DIR__ . '/../.env')) {
    // Already on setup.php? Don't redirect (infinite loop prevention)
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if (!str_contains($uri, 'setup.php') && !str_contains($uri, 'requirements.php')) {
        header('Location: /setup.php');
        exit;
    }
}
// ─────────────────────────────────────────────────────────────────────────────

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
