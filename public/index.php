<?php
/**
 * Wanseven Entry Point
 * Ultra-simple: Check installed, redirect to installer if needed
 */

// Check if installed
$installedMarker = __DIR__ . '/../storage/app/.installed';

if (!file_exists($installedMarker)) {
    // NOT INSTALLED - Redirect to installer (only if not already there)
    if (basename($_SERVER['PHP_SELF']) !== 'install.php') {
        header('Location: /install.php');
        exit('Redirecting to installer...');
    }
    // If already on install.php, let it load
    exit;
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
            <p><a href="/check.php" style="display:inline-block;margin-top:10px;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;border-radius:5px;">Check Status</a></p>
        </div>
    </body>
    </html>
    ');
}

// Check if migrations have been run
$migrationCheck = __DIR__ . '/../storage/app/.migrated';
if (!file_exists($migrationCheck)) {
    // Migrations not run yet - show setup page
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <title>Setup Required</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100">
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full">
                <div class="text-center mb-6">
                    <svg class="w-16 h-16 text-yellow-500 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Setup Database</h1>
                    <p class="text-gray-600">Installation complete! Now run migrations.</p>
                </div>
                
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <h3 class="font-bold text-blue-800 mb-2">📋 Next Steps:</h3>
                    <ol class="text-sm text-blue-700 space-y-2 list-decimal list-inside">
                        <li>Run migrations via SSH or terminal</li>
                        <li>Seed the database with initial data</li>
                        <li>Refresh this page</li>
                    </ol>
                </div>
                
                <div class="bg-gray-900 text-green-400 p-4 rounded font-mono text-sm mb-6">
                    <div class="mb-2"># Run these commands:</div>
                    <div>$ php artisan migrate</div>
                    <div>$ php artisan db:seed</div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 p-4 rounded mb-6 text-sm">
                    <p class="text-yellow-800"><strong>⚠️ No SSH access?</strong></p>
                    <p class="text-yellow-700 mt-1">You can run migrations manually through your hosting control panel or use a web-based terminal if available.</p>
                </div>
                
                <div class="flex gap-2">
                    <a href="/check.php" class="flex-1 text-center bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Check Status</a>
                    <a href="/" class="flex-1 text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Refresh</a>
                </div>
            </div>
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
