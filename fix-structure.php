<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Fix Laravel Structure for Shared Hosting</h2>";

// Check if already fixed
if (file_exists(__DIR__ . '/index.php')) {
    $content = file_get_contents(__DIR__ . '/index.php');
    if (strpos($content, 'LARAVEL_START') !== false && strpos($content, "'/../vendor/autoload.php'") === false) {
        die("✓ Structure already fixed!<br><a href='/install'>Go to Installer</a>");
    }
}

echo "Step 1: Moving files from public/ to root...<br>";

// Files to move from public/ to root
$publicFiles = glob(__DIR__ . '/public/*');
$moved = 0;

foreach ($publicFiles as $file) {
    $filename = basename($file);
    $dest = __DIR__ . '/' . $filename;
    
    if ($filename === '.' || $filename === '..') continue;
    
    if (is_file($file)) {
        if (copy($file, $dest)) {
            echo "✓ Moved: $filename<br>";
            $moved++;
        }
    } elseif (is_dir($file)) {
        // For directories, we'll just note them
        echo "→ Directory: $filename (keeping in place)<br>";
    }
}

echo "<br>Step 2: Updating index.php paths...<br>";

// Update index.php to point to correct paths
$indexContent = file_get_contents(__DIR__ . '/index.php');
$indexContent = str_replace("__DIR__.'/../vendor/autoload.php'", "__DIR__.'/vendor/autoload.php'", $indexContent);
$indexContent = str_replace("__DIR__.'/../storage/framework/maintenance.php'", "__DIR__.'/storage/framework/maintenance.php'", $indexContent);
$indexContent = str_replace("__DIR__.'/../bootstrap/app.php'", "__DIR__.'/bootstrap/app.php'", $indexContent);

file_put_contents(__DIR__ . '/index.php', $indexContent);
echo "✓ index.php updated<br>";

echo "<br>Step 3: Updating .htaccess...<br>";

// Remove the root .htaccess that redirects to public/
if (file_exists(__DIR__ . '/.htaccess')) {
    $htaccess = file_get_contents(__DIR__ . '/.htaccess');
    if (strpos($htaccess, 'public/$1') !== false) {
        // This is the redirect .htaccess, replace it with Laravel's
        $laravelHtaccess = file_get_contents(__DIR__ . '/public/.htaccess');
        file_put_contents(__DIR__ . '/.htaccess', $laravelHtaccess);
        echo "✓ .htaccess updated with Laravel's version<br>";
    }
}

echo "<br><h3 style='color: green;'>✓ Structure Fixed!</h3>";
echo "<strong>Next steps:</strong><br>";
echo "1. <a href='/install'>Go to Installer</a><br>";
echo "2. If still error, <a href='/debug-laravel.php'>Run Debug</a><br>";
