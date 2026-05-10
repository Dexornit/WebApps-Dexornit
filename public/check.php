<?php
/**
 * Quick Installation Status Checker
 * Helps diagnose common issues
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wanseven - Installation Check</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-6">🔍 Installation Status</h1>
        
        <?php
        $checks = [];
        
        // Check PHP version
        $phpVersion = phpversion();
        $checks[] = [
            'name' => 'PHP Version',
            'status' => version_compare($phpVersion, '8.1.0', '>='),
            'message' => "PHP $phpVersion " . (version_compare($phpVersion, '8.1.0', '>=') ? '✅' : '❌ (Need 8.1+)')
        ];
        
        // Check .env file
        $envExists = file_exists(__DIR__ . '/../.env');
        $checks[] = [
            'name' => '.env File',
            'status' => $envExists,
            'message' => $envExists ? '✅ Found' : '❌ Missing (run installer first)'
        ];
        
        // Check vendor folder
        $vendorExists = file_exists(__DIR__ . '/../vendor/autoload.php');
        $checks[] = [
            'name' => 'Vendor Folder',
            'status' => $vendorExists,
            'message' => $vendorExists ? '✅ Found' : '❌ Missing (upload vendor/ or run composer install)'
        ];
        
        // Check storage permissions
        $storageWritable = is_writable(__DIR__ . '/../storage');
        $checks[] = [
            'name' => 'Storage Writable',
            'status' => $storageWritable,
            'message' => $storageWritable ? '✅ Writable' : '❌ Not writable (chmod 755 or 775)'
        ];
        
        // Check bootstrap/cache permissions
        $cacheWritable = is_writable(__DIR__ . '/../bootstrap/cache');
        $checks[] = [
            'name' => 'Cache Writable',
            'status' => $cacheWritable,
            'message' => $cacheWritable ? '✅ Writable' : '❌ Not writable (chmod 755 or 775)'
        ];
        
        // Check installation marker
        $installed = file_exists(__DIR__ . '/../storage/app/.installed');
        $checks[] = [
            'name' => 'Installation Marker',
            'status' => $installed,
            'message' => $installed ? '✅ Installed' : '❌ Not installed yet'
        ];
        
        // Check migration marker
        $migrated = file_exists(__DIR__ . '/../storage/app/.migrated');
        $checks[] = [
            'name' => 'Database Migrated',
            'status' => $migrated,
            'message' => $migrated ? '✅ Migrated' : '❌ Not migrated yet'
        ];
        
        // Check database file (if SQLite)
        if ($envExists) {
            $env = file_get_contents(__DIR__ . '/../.env');
            if (preg_match('/^DB_CONNECTION=sqlite/m', $env)) {
                $dbExists = file_exists(__DIR__ . '/../database/database.sqlite');
                $checks[] = [
                    'name' => 'SQLite Database',
                    'status' => $dbExists,
                    'message' => $dbExists ? '✅ Found' : '❌ Missing'
                ];
            }
        }
        
        // Display results
        foreach ($checks as $check) {
            $bgColor = $check['status'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
            echo "<div class='mb-3 p-4 border rounded $bgColor'>";
            echo "<strong>{$check['name']}:</strong> {$check['message']}";
            echo "</div>";
        }
        
        // Overall status
        $allGood = array_reduce($checks, function($carry, $item) {
            return $carry && $item['status'];
        }, true);
        
        echo "<div class='mt-6 p-6 rounded-lg " . ($allGood ? 'bg-green-100 border-2 border-green-500' : 'bg-yellow-100 border-2 border-yellow-500') . "'>";
        
        if ($allGood) {
            echo "<h2 class='text-2xl font-bold text-green-700 mb-2'>✅ Semua OK!</h2>";
            echo "<p class='text-green-600'>Aplikasi siap digunakan.</p>";
            echo "<a href='/' class='inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700'>Buka Website</a>";
        } else {
            echo "<h2 class='text-2xl font-bold text-yellow-700 mb-2'>⚠️ Ada Masalah</h2>";
            echo "<p class='text-yellow-600 mb-4'>Perbaiki item yang ditandai ❌ di atas.</p>";
            
            if (!$installed) {
                echo "<a href='/install.php' class='inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 mr-2'>Jalankan Installer</a>";
            }
            
            if ($installed && !$migrated && $vendorExists) {
                echo "<a href='/migrate.php' class='inline-block bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 mr-2'>Jalankan Migrasi</a>";
            }
            
            if (!$vendorExists) {
                echo "<div class='mt-4 p-4 bg-white rounded border'>";
                echo "<strong>Cara Upload Vendor:</strong>";
                echo "<ol class='list-decimal list-inside mt-2 space-y-1 text-sm'>";
                echo "<li>Di komputer lokal, jalankan: <code class='bg-gray-200 px-2 py-1 rounded'>composer install</code></li>";
                echo "<li>Compress folder <code>vendor/</code> menjadi ZIP</li>";
                echo "<li>Upload ZIP ke server via File Manager</li>";
                echo "<li>Extract di folder root aplikasi</li>";
                echo "</ol>";
                echo "<div class='mt-3 pt-3 border-t'>";
                echo "<p class='text-sm text-gray-600 mb-2'>Atau coba install otomatis (bisa timeout di server lambat):</p>";
                echo "<a href='/composer-setup.php' class='inline-block bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm'>Auto-Install Dependencies</a>";
                echo "</div>";
                echo "</div>";
            }
        }
        
        echo "</div>";
        ?>
        
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Current Path: <?= __DIR__ ?></p>
            <p>Server: <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></p>
        </div>
    </div>
</body>
</html>
