<?php
/**
 * Wanseven Entry Point
 * Ultra-simple: Check installed, redirect to installer if needed
 */

// Check if installed
$installedMarker = __DIR__ . '/../storage/app/.installed';

if (!file_exists($installedMarker)) {
    // NOT INSTALLED - Redirect to installer
    header('Location: /install.php');
    exit('Redirecting to installer...');
}

// INSTALLED - Check if vendor exists
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    // Vendor missing - show error
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <title>Vendor Missing</title>
        <style>
            body { font-family: Arial; max-width: 600px; margin: 100px auto; padding: 20px; }
            .error { background: #fee; border: 2px solid #f00; padding: 20px; border-radius: 8px; }
            h1 { color: #c00; }
            code { background: #eee; padding: 2px 6px; border-radius: 3px; }
        </style>
    </head>
    <body>
        <div class="error">
            <h1>⚠️ Vendor Folder Missing</h1>
            <p>The <code>vendor/</code> folder is required but not found.</p>
            <p><strong>Solution:</strong></p>
            <ol>
                <li>Upload <code>vendor/</code> folder from your local machine, OR</li>
                <li>Run <code>composer install</code> via SSH if available</li>
            </ol>
        </div>
    </body>
    </html>
    ');
}

// ALL GOOD - Boot Laravel
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->handleRequest(Request::capture());
