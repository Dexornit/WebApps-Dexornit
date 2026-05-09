<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Folder Structure Check</h2>";
echo "Current directory: " . __DIR__ . "<br><br>";

$folders = [
    'app',
    'bootstrap',
    'config',
    'database',
    'public',
    'resources',
    'routes',
    'storage',
    'vendor',
];

echo "<h3>Laravel Folders:</h3>";
foreach ($folders as $folder) {
    $path = __DIR__ . '/' . $folder;
    $exists = is_dir($path);
    $color = $exists ? 'green' : 'red';
    echo "<span style='color: $color'>[$exists ? '✓' : '✗'] $folder/</span><br>";
}

echo "<br><h3>Important Files:</h3>";
$files = [
    'artisan',
    'composer.json',
    '.env',
    'public/index.php',
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path);
    $color = $exists ? 'green' : 'red';
    echo "<span style='color: $color'>[" . ($exists ? '✓' : '✗') . "] $file</span><br>";
}

echo "<br><h3>Permissions:</h3>";
$checkPerms = ['storage', 'bootstrap/cache'];
foreach ($checkPerms as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        $writable = is_writable($path);
        $color = $writable ? 'green' : 'red';
        echo "<span style='color: $color'>[" . ($writable ? '✓' : '✗') . "] $dir writable</span><br>";
    } else {
        echo "<span style='color: red'>[✗] $dir NOT FOUND</span><br>";
    }
}

echo "<br><h3>Recommendation:</h3>";
if (!is_dir(__DIR__ . '/app')) {
    echo "<strong style='color: red;'>❌ Laravel files NOT found in public_html!</strong><br>";
    echo "You need to:<br>";
    echo "1. Upload ALL Laravel files to public_html<br>";
    echo "2. OR move Laravel files outside public_html and only put public/ contents in public_html<br>";
} else {
    echo "<strong style='color: green;'>✓ Laravel structure looks OK</strong><br>";
    echo "Try accessing: <a href='/install'>Go to Installer</a><br>";
}
