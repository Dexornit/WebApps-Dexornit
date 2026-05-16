<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Jika .env belum ada (belum diinstall), redirect ke setup wizard
// agar tidak crash dengan 500 error saat boot Laravel tanpa konfigurasi
if (!file_exists(__DIR__ . '/../.env')) {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if (!str_contains($uri, 'setup.php') && !str_contains($uri, 'requirements.php')) {
        header('Location: /setup.php');
        exit;
    }
}

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
