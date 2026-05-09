<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Fix All Laravel Paths</h2>";

// Find where vendor actually is
$possibleVendorPaths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
];

$vendorPath = null;
foreach ($possibleVendorPaths as $path) {
    if (file_exists($path)) {
        $vendorPath = $path;
        echo "✓ Found vendor at: $path<br>";
        break;
    }
}

if (!$vendorPath) {
    die("❌ vendor/autoload.php not found! Please run 'composer install'");
}

// Determine the Laravel root directory
$laravelRoot = dirname($vendorPath);
echo "✓ Laravel root: $laravelRoot<br><br>";

// Now fix index.php
$indexPath = __DIR__ . '/index.php';
if (!file_exists($indexPath)) {
    // Copy from public/index.php
    if (file_exists(__DIR__ . '/public/index.php')) {
        copy(__DIR__ . '/public/index.php', $indexPath);
        echo "✓ Copied index.php from public/<br>";
    }
}

if (file_exists($indexPath)) {
    $indexContent = file_get_contents($indexPath);
    
    // Calculate relative path from public_html to vendor
    $relativePath = str_replace(__DIR__, '', $laravelRoot);
    if (empty($relativePath)) {
        $relativePath = '.';
    } else {
        $relativePath = '.' . $relativePath;
    }
    
    echo "Relative path: $relativePath<br>";
    
    // Replace paths in index.php
    $newContent = "<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists(\$maintenance = __DIR__.'$relativePath/storage/framework/maintenance.php')) {
    require \$maintenance;
}

// Register the Composer autoloader...
require __DIR__.'$relativePath/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application \$app */
\$app = require_once __DIR__.'$relativePath/bootstrap/app.php';

\$app->handleRequest(Request::capture());
";
    
    file_put_contents($indexPath, $newContent);
    echo "✓ index.php updated with correct paths<br><br>";
}

// Copy .htaccess from public/ if needed
if (!file_exists(__DIR__ . '/.htaccess') || filesize(__DIR__ . '/.htaccess') < 100) {
    if (file_exists(__DIR__ . '/public/.htaccess')) {
        copy(__DIR__ . '/public/.htaccess', __DIR__ . '/.htaccess');
        echo "✓ Copied .htaccess from public/<br>";
    }
}

echo "<br><h3 style='color: green;'>✓ Paths Fixed!</h3>";
echo "<a href='/'>Test Homepage</a> | ";
echo "<a href='/install'>Go to Installer</a>";
