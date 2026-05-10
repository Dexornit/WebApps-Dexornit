<?php
// DEBUG v2 - Hapus setelah selesai!
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<pre style='font-family:monospace;padding:20px;background:#111;color:#0f0;font-size:12px;line-height:1.6'>";
echo "=== DEXORNIT DEBUG v2 ===\n\n";

$root = dirname(__DIR__);

// ─── 1. Show Laravel log - FULL LAST ERROR (first 120 lines) ────────────────
echo "--- LARAVEL LOG (first 120 lines of file) ---\n";
$logFile = $root . '/storage/logs/laravel.log';
if (file_exists($logFile) && filesize($logFile) > 0) {
    $allLines = file($logFile);
    $total    = count($allLines);
    echo "Total log lines: {$total}\n\n";

    // Find the LAST error entry (starts with [20)
    $lastErrorLine = 0;
    for ($i = $total - 1; $i >= 0; $i--) {
        if (preg_match('/^\[20\d\d-/', $allLines[$i])) {
            $lastErrorLine = $i;
            break;
        }
    }
    echo "Last error starts at line: {$lastErrorLine}\n\n";
    // Show from last error start to end
    $show = array_slice($allLines, $lastErrorLine);
    foreach ($show as $line) {
        echo htmlspecialchars($line);
    }
} else {
    echo "Log file empty or not found.\n";
}

// ─── 2. Try to actually run the home route ───────────────────────────────────
echo "\n\n--- FULL LARAVEL REQUEST SIMULATION ---\n";
try {
    require $root . '/vendor/autoload.php';

    // Fake the request as GET /
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI']    = '/';
    $_SERVER['HTTP_HOST']      = $_SERVER['HTTP_HOST'] ?? 'localhost';

    $app    = require $root . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    echo "Kernel bootstrap: OK\n";
    echo "APP_KEY: " . substr(config('app.key'), 0, 20) . "...\n";
    echo "DB_CONNECTION: " . config('database.default') . "\n";

    // Try DB query
    $products = \App\Models\Product::count();
    echo "Products in DB: {$products}\n";

    $cats = \App\Models\Category::count();
    echo "Categories in DB: {$cats}\n";

    // Try compiling the home view
    echo "Trying to render home view...\n";
    $view = view('home', [
        'productsData' => collect([]),
        'categories'   => collect([]),
    ])->render();
    echo "View render: OK (" . strlen($view) . " bytes)\n";

} catch (\Throwable $e) {
    echo "\n*** ERROR CAUGHT ***\n";
    echo "Message : " . $e->getMessage() . "\n";
    echo "Class   : " . get_class($e) . "\n";
    echo "File    : " . $e->getFile() . "\n";
    echo "Line    : " . $e->getLine() . "\n";
    echo "\nTrace:\n";
    foreach (array_slice($e->getTrace(), 0, 15) as $i => $frame) {
        $file = isset($frame['file']) ? str_replace($root, '', $frame['file']) : '[internal]';
        $line = $frame['line'] ?? '?';
        $func = ($frame['class'] ?? '') . ($frame['type'] ?? '') . $frame['function'];
        echo "  #{$i} {$file}:{$line} → {$func}()\n";
    }
}

echo "\n=== END DEBUG v2 ===";
echo "</pre>";
