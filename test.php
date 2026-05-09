<?php
// Most basic PHP test
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current directory: " . __DIR__ . "<br>";

// Test file access
if (file_exists(__DIR__ . '/storage/logs/laravel.log')) {
    echo "Laravel log exists<br>";
    $content = file_get_contents(__DIR__ . '/storage/logs/laravel.log');
    echo "<h3>Last 2000 characters of log:</h3>";
    echo "<pre>" . htmlspecialchars(substr($content, -2000)) . "</pre>";
} else {
    echo "Laravel log NOT found<br>";
}
