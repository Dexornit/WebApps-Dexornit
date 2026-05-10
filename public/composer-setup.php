<?php
/**
 * Composer Auto-Installer for Shared Hosting
 * Downloads and runs composer install automatically
 * 
 * WARNING: This may timeout on slow servers!
 * Better to upload vendor/ manually if possible.
 */

set_time_limit(300); // 5 minutes max
ini_set('memory_limit', '512M');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Composer Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-6">📦 Composer Auto-Installer</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
            echo "<div class='bg-blue-50 border border-blue-200 p-4 rounded mb-4'>";
            echo "<p class='font-semibold'>⏳ Installing dependencies...</p>";
            echo "<p class='text-sm text-gray-600'>This may take 2-5 minutes. Please wait...</p>";
            echo "</div>";
            
            echo "<div class='bg-gray-900 text-green-400 p-4 rounded font-mono text-sm overflow-auto max-h-96 mb-4'>";
            
            $rootDir = realpath(__DIR__ . '/..');
            
            // Check if composer.phar exists
            if (!file_exists($rootDir . '/composer.phar')) {
                echo "→ Downloading Composer...\n";
                flush();
                
                $composerSetup = file_get_contents('https://getcomposer.org/installer');
                if ($composerSetup === false) {
                    echo "❌ Failed to download Composer installer!\n";
                    echo "</div>";
                    exit;
                }
                
                file_put_contents($rootDir . '/composer-setup.php', $composerSetup);
                
                // Run composer setup
                chdir($rootDir);
                exec('php composer-setup.php 2>&1', $output, $return);
                echo implode("\n", $output) . "\n";
                
                unlink($rootDir . '/composer-setup.php');
                
                if ($return !== 0) {
                    echo "❌ Failed to install Composer!\n";
                    echo "</div>";
                    exit;
                }
                
                echo "✅ Composer downloaded!\n\n";
            } else {
                echo "✅ Composer already exists!\n\n";
            }
            
            // Run composer install
            echo "→ Installing dependencies (this will take a while)...\n";
            flush();
            
            chdir($rootDir);
            exec('php composer.phar install --no-dev --optimize-autoloader 2>&1', $output, $return);
            echo implode("\n", $output) . "\n";
            
            if ($return === 0) {
                echo "\n✅ Installation complete!\n";
                echo "</div>";
                echo "<div class='bg-green-50 border border-green-200 p-4 rounded'>";
                echo "<p class='font-semibold text-green-700'>✅ Dependencies installed successfully!</p>";
                echo "<a href='/check.php' class='inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700'>Check Status</a>";
                echo "</div>";
            } else {
                echo "\n❌ Installation failed!\n";
                echo "</div>";
                echo "<div class='bg-red-50 border border-red-200 p-4 rounded'>";
                echo "<p class='font-semibold text-red-700'>❌ Installation failed!</p>";
                echo "<p class='text-sm text-red-600 mt-2'>Please upload vendor/ folder manually instead.</p>";
                echo "</div>";
            }
            
        } else {
            // Show form
            ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <h3 class="font-bold text-yellow-800 mb-2">⚠️ Peringatan</h3>
                <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                    <li>Proses ini membutuhkan waktu 2-5 menit</li>
                    <li>Server harus punya akses internet</li>
                    <li>Bisa timeout di shared hosting yang lambat</li>
                    <li><strong>Lebih baik upload vendor/ manual jika memungkinkan</strong></li>
                </ul>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 p-4 rounded mb-6">
                <h3 class="font-bold text-blue-800 mb-2">ℹ️ Alternatif (Recommended)</h3>
                <ol class="text-sm text-blue-700 space-y-2 list-decimal list-inside">
                    <li>Di komputer lokal: <code class="bg-blue-100 px-2 py-1 rounded">composer install</code></li>
                    <li>Compress folder vendor/: <code class="bg-blue-100 px-2 py-1 rounded">zip -r vendor.zip vendor/</code></li>
                    <li>Upload vendor.zip ke server</li>
                    <li>Extract via File Manager</li>
                </ol>
            </div>
            
            <form method="POST" onsubmit="return confirm('Proses ini bisa memakan waktu lama. Lanjutkan?');">
                <button type="submit" name="install" value="1" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
                    🚀 Install Dependencies Sekarang
                </button>
            </form>
            
            <div class="mt-4 text-center">
                <a href="/check.php" class="text-blue-600 hover:underline">← Kembali ke Status Check</a>
            </div>
            <?php
        }
        ?>
    </div>
</body>
</html>
