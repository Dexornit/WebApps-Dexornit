<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Vendor Folder Check</h2>";

$vendorDir = __DIR__ . '/vendor';

if (!is_dir($vendorDir)) {
    die("❌ vendor/ directory does NOT exist!<br><br>
    <strong>Solution:</strong><br>
    1. Zip vendor/ folder on your local machine<br>
    2. Upload vendor.zip to shared hosting<br>
    3. Extract it in cPanel File Manager<br>
    4. Or run: composer install via SSH");
}

echo "✓ vendor/ directory exists<br><br>";

// Check if autoload.php exists
$autoloadPath = $vendorDir . '/autoload.php';
if (file_exists($autoloadPath)) {
    echo "✓ vendor/autoload.php exists<br>";
} else {
    echo "❌ vendor/autoload.php NOT found!<br>";
    echo "→ vendor/ folder is empty or incomplete<br><br>";
}

// Count files in vendor
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($vendorDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$fileCount = 0;
$dirCount = 0;
foreach ($iterator as $item) {
    if ($item->isFile()) {
        $fileCount++;
    } else {
        $dirCount++;
    }
    
    if ($fileCount > 100) break; // Stop counting after 100 files
}

echo "<br><strong>Vendor Statistics:</strong><br>";
echo "Files found: " . ($fileCount > 100 ? '100+' : $fileCount) . "<br>";
echo "Directories: " . $dirCount . "<br><br>";

if ($fileCount < 10) {
    echo "<div style='background: #ff0000; color: white; padding: 20px;'>";
    echo "<h3>❌ vendor/ folder is EMPTY or INCOMPLETE!</h3>";
    echo "<strong>You need to:</strong><br>";
    echo "1. On your local machine, zip the vendor/ folder<br>";
    echo "2. Upload vendor.zip to your shared hosting<br>";
    echo "3. Extract it using cPanel File Manager<br>";
    echo "4. OR run 'composer install' via SSH if available<br>";
    echo "</div>";
} else {
    echo "✓ vendor/ looks OK (has files)<br>";
    echo "<a href='/install'>Try Installer Again</a>";
}
