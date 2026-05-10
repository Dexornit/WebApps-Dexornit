<?php
/**
 * Wanseven Entry Point
 */

// Check if installed
if (!file_exists(__DIR__ . '/../storage/app/.installed')) {
    // NOT INSTALLED - Redirect to installer
    if (basename($_SERVER['PHP_SELF']) !== 'install.php') {
        header('Location: /install.php');
        exit;
    }
    exit;
}

// Check if vendor exists
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <title>Vendor Missing</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md">
            <h1 class="text-2xl font-bold text-red-600 mb-4">⚠️ Vendor Folder Missing</h1>
            <p class="text-gray-700 mb-4">The <code class="bg-gray-200 px-2 py-1 rounded">vendor/</code> folder is required but not found.</p>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <p class="font-semibold text-yellow-800">Solution:</p>
                <ol class="text-sm text-yellow-700 mt-2 list-decimal list-inside space-y-1">
                    <li>Upload <code>vendor/</code> folder from your local machine, OR</li>
                    <li>Run <code>composer install</code> via SSH</li>
                </ol>
            </div>
            <a href="/install.php" class="inline-block w-full text-center bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Back to Installer</a>
        </div>
    </body>
    </html>
    ');
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
