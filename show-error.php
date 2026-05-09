<?php
// Show Laravel error log via browser
// Access: http://wanseven.com/show-error.php

$logFile = __DIR__ . '/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    die("Log file not found at: $logFile");
}

// Get last 100 lines
$lines = file($logFile);
$lastLines = array_slice($lines, -100);

echo "<h2>Last 100 lines of Laravel Log</h2>";
echo "<pre style='background: #1e1e1e; color: #fff; padding: 20px; overflow: auto; max-height: 80vh;'>";
echo htmlspecialchars(implode('', $lastLines));
echo "</pre>";

echo "<hr>";
echo "<h3>PHP Info</h3>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Storage Path: " . __DIR__ . '/storage/logs/laravel.log' . "<br>";
echo "Storage Writable: " . (is_writable(__DIR__ . '/storage') ? 'YES' : 'NO') . "<br>";
echo "Bootstrap Cache Writable: " . (is_writable(__DIR__ . '/bootstrap/cache') ? 'YES' : 'NO') . "<br>";

echo "<hr>";
echo "<h3>Quick Actions</h3>";
echo "<a href='/clear-cache.php'>Clear Cache</a> | ";
echo "<a href='/install'>Go to Installer</a> | ";
echo "<a href='/'>Go to Home</a>";
