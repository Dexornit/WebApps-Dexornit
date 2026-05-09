<?php
// Debug Laravel bootstrap
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Laravel Bootstrap Debug</h2>";

try {
    echo "1. Loading autoloader...<br>";
    require __DIR__.'/vendor/autoload.php';
    echo "✓ Autoloader loaded<br><br>";
    
    echo "2. Loading Laravel app...<br>";
    $app = require_once __DIR__.'/bootstrap/app.php';
    echo "✓ App loaded<br><br>";
    
    echo "3. Creating kernel...<br>";
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✓ Kernel created<br><br>";
    
    echo "4. Creating request...<br>";
    $request = Illuminate\Http\Request::capture();
    echo "✓ Request: " . $request->getMethod() . " " . $request->getPathInfo() . "<br><br>";
    
    echo "5. Handling request...<br>";
    $response = $kernel->handle($request);
    echo "✓ Response status: " . $response->getStatusCode() . "<br><br>";
    
    echo "6. Sending response...<br>";
    $response->send();
    
    $kernel->terminate($request, $response);
    
} catch (\Throwable $e) {
    echo "<div style='background: #ff0000; color: white; padding: 20px; margin: 20px 0;'>";
    echo "<h3>❌ ERROR CAUGHT:</h3>";
    echo "<strong>Message:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>File:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Line:</strong> " . $e->getLine() . "<br><br>";
    echo "<strong>Stack Trace:</strong><br>";
    echo "<pre style='background: #000; color: #0f0; padding: 10px; overflow: auto;'>";
    echo $e->getTraceAsString();
    echo "</pre>";
    echo "</div>";
    
    // Check common issues
    echo "<h3>Common Issues Check:</h3>";
    echo ".env exists: " . (file_exists(__DIR__ . '/.env') ? '✓ YES' : '❌ NO') . "<br>";
    echo "storage writable: " . (is_writable(__DIR__ . '/storage') ? '✓ YES' : '❌ NO') . "<br>";
    echo "bootstrap/cache writable: " . (is_writable(__DIR__ . '/bootstrap/cache') ? '✓ YES' : '❌ NO') . "<br>";
    
    if (file_exists(__DIR__ . '/.env')) {
        $env = file_get_contents(__DIR__ . '/.env');
        echo "APP_KEY set: " . (strpos($env, 'APP_KEY=base64:') !== false ? '✓ YES' : '❌ NO') . "<br>";
    }
}
