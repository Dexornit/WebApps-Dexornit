<?php
// DEBUG FILE - Hapus setelah selesai debug!
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<pre style='font-family:monospace;padding:20px;background:#111;color:#0f0;font-size:13px'>";
echo "=== DEXORNIT DEBUG ===\n\n";

$root = dirname(__DIR__);

// 1. PHP Info
echo "PHP Version: " . PHP_VERSION . "\n";
echo "SAPI: " . php_sapi_name() . "\n\n";

// 2. Critical files
echo "--- FILE CHECK ---\n";
$files = [
    '.env'                          => $root . '/.env',
    'vendor/autoload.php'           => $root . '/vendor/autoload.php',
    'bootstrap/app.php'             => $root . '/bootstrap/app.php',
    'storage/app/.installed'        => $root . '/storage/app/.installed',
];
foreach ($files as $label => $path) {
    echo $label . ': ' . (file_exists($path) ? 'EXISTS' : '*** MISSING ***') . "\n";
}

// 3. Storage dirs
echo "\n--- STORAGE DIRS ---\n";
$dirs = [
    'storage'                        => $root . '/storage',
    'storage/framework'              => $root . '/storage/framework',
    'storage/framework/cache/data'   => $root . '/storage/framework/cache/data',
    'storage/framework/sessions'     => $root . '/storage/framework/sessions',
    'storage/framework/views'        => $root . '/storage/framework/views',
    'storage/logs'                   => $root . '/storage/logs',
    'bootstrap/cache'                => $root . '/bootstrap/cache',
];
foreach ($dirs as $label => $path) {
    $exists   = is_dir($path);
    $writable = $exists && is_writable($path);
    echo $label . ': ' . ($exists ? 'EXISTS' : '*** MISSING ***') . ' | ' . ($writable ? 'WRITABLE' : '*** NOT WRITABLE ***') . "\n";
    if (!$exists) @mkdir($path, 0755, true);
}

// 4. .env content (DB only, safe)
echo "\n--- .ENV (DB & KEY) ---\n";
$env = file_exists($root . '/.env') ? file_get_contents($root . '/.env') : '';
preg_match('/^DB_CONNECTION=(.*)$/m', $env, $m); echo "DB_CONNECTION: " . ($m[1] ?? 'NOT SET') . "\n";
preg_match('/^DB_HOST=(.*)$/m', $env, $m);       echo "DB_HOST: "       . ($m[1] ?? 'NOT SET') . "\n";
preg_match('/^DB_DATABASE=(.*)$/m', $env, $m);   echo "DB_DATABASE: "   . ($m[1] ?? 'NOT SET') . "\n";
preg_match('/^DB_USERNAME=(.*)$/m', $env, $m);   echo "DB_USERNAME: "   . ($m[1] ?? 'NOT SET') . "\n";
preg_match('/^APP_KEY=(.*)$/m', $env, $m);       echo "APP_KEY: "       . (isset($m[1]) ? substr($m[1],0,20).'...' : 'NOT SET') . "\n";
preg_match('/^APP_DEBUG=(.*)$/m', $env, $m);     echo "APP_DEBUG: "     . ($m[1] ?? 'NOT SET') . "\n";

// 5. Try DB connection
echo "\n--- DB CONNECTION TEST ---\n";
preg_match('/^DB_HOST=(.*)$/m', $env, $h);
preg_match('/^DB_PORT=(.*)$/m', $env, $p);
preg_match('/^DB_DATABASE=(.*)$/m', $env, $d);
preg_match('/^DB_USERNAME=(.*)$/m', $env, $u);
preg_match('/^DB_PASSWORD=(.*)$/m', $env, $pw);
try {
    $dsn = "mysql:host=" . trim($h[1]??'localhost') . ";port=" . trim($p[1]??'3306') . ";dbname=" . trim($d[1]??'') . ";charset=utf8mb4";
    $pdo = new PDO($dsn, trim($u[1]??''), trim($pw[1]??''), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "DB Connection: OK\n";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(', ', $tables) . "\n";
} catch (Exception $e) {
    echo "DB Connection: *** FAILED ***\n";
    echo "Error: " . $e->getMessage() . "\n";
}

// 6. Try boot Laravel
echo "\n--- LARAVEL BOOT TEST ---\n";
try {
    require $root . '/vendor/autoload.php';
    $app = require_once $root . '/bootstrap/app.php';
    echo "Laravel Boot: OK\n";
    echo "APP_KEY (config): " . substr(config('app.key'), 0, 20) . "...\n";
} catch (Throwable $e) {
    echo "Laravel Boot: *** FAILED ***\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " line " . $e->getLine() . "\n";
}

// 7. Laravel error log (last 30 lines)
echo "\n--- LARAVEL LOG (last 30 lines) ---\n";
$logFile = $root . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $last  = array_slice($lines, -30);
    echo implode('', $last);
} else {
    echo "Log file not found\n";
}

echo "\n=== END DEBUG ===";
echo "</pre>";
