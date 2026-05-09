<?php
/**
 * Deploy Helper Script
 * 
 * Script ini membantu proses deployment di shared hosting
 * Upload file ini ke public_html/ dan akses via browser
 * 
 * PENTING: HAPUS FILE INI SETELAH DEPLOYMENT SELESAI!
 */

// Konfigurasi path (sesuaikan dengan struktur hosting Anda)
$appPath = __DIR__ . '/../dexornit-app';
$publicPath = __DIR__;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dexornit Store - Deploy Helper</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 28px; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        .content { padding: 30px; }
        .action-card {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .action-card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 18px;
        }
        .action-card p {
            color: #6c757d;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .result {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin-top: 15px;
        }
        .warning {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info-box strong { color: #1976D2; }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            font-size: 13px;
            line-height: 1.5;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Dexornit Store Deploy Helper</h1>
            <p>Script bantuan untuk deployment di shared hosting</p>
        </div>

        <div class="content">
            <div class="warning">
                ⚠️ PENTING: Hapus file ini setelah deployment selesai untuk keamanan!
            </div>

            <?php
            // Check if action is requested
            $action = $_GET['action'] ?? null;

            if ($action === 'migrate') {
                echo '<div class="action-card">';
                echo '<h3>🗄️ Running Migrations...</h3>';
                
                try {
                    require $appPath . '/vendor/autoload.php';
                    $app = require_once $appPath . '/bootstrap/app.php';
                    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                    
                    ob_start();
                    $kernel->call('migrate', ['--force' => true]);
                    $output = ob_get_clean();
                    
                    echo '<div class="result">';
                    echo '<strong>✅ Migration berhasil!</strong><br>';
                    echo '<pre>' . htmlspecialchars($output) . '</pre>';
                    echo '</div>';
                } catch (Exception $e) {
                    echo '<div class="error">';
                    echo '<strong>❌ Error:</strong><br>';
                    echo htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
                
                echo '</div>';
            }

            if ($action === 'seed') {
                echo '<div class="action-card">';
                echo '<h3>🌱 Running Seeders...</h3>';
                
                try {
                    require $appPath . '/vendor/autoload.php';
                    $app = require_once $appPath . '/bootstrap/app.php';
                    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                    
                    ob_start();
                    $kernel->call('db:seed', ['--force' => true]);
                    $output = ob_get_clean();
                    
                    echo '<div class="result">';
                    echo '<strong>✅ Seeding berhasil!</strong><br>';
                    echo '<pre>' . htmlspecialchars($output) . '</pre>';
                    echo '</div>';
                } catch (Exception $e) {
                    echo '<div class="error">';
                    echo '<strong>❌ Error:</strong><br>';
                    echo htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
                
                echo '</div>';
            }

            if ($action === 'storage-link') {
                echo '<div class="action-card">';
                echo '<h3>🔗 Creating Storage Link...</h3>';
                
                try {
                    $target = $appPath . '/storage/app/public';
                    $link = $publicPath . '/storage';
                    
                    if (file_exists($link)) {
                        if (is_link($link)) {
                            unlink($link);
                        } else {
                            throw new Exception('File/folder "storage" sudah ada dan bukan symlink. Hapus manual terlebih dahulu.');
                        }
                    }
                    
                    if (symlink($target, $link)) {
                        echo '<div class="result">';
                        echo '<strong>✅ Storage link berhasil dibuat!</strong><br>';
                        echo 'Target: ' . htmlspecialchars($target) . '<br>';
                        echo 'Link: ' . htmlspecialchars($link);
                        echo '</div>';
                    } else {
                        throw new Exception('Gagal membuat symlink. Pastikan permissions sudah benar.');
                    }
                } catch (Exception $e) {
                    echo '<div class="error">';
                    echo '<strong>❌ Error:</strong><br>';
                    echo htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
                
                echo '</div>';
            }

            if ($action === 'optimize') {
                echo '<div class="action-card">';
                echo '<h3>⚡ Optimizing Application...</h3>';
                
                try {
                    require $appPath . '/vendor/autoload.php';
                    $app = require_once $appPath . '/bootstrap/app.php';
                    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                    
                    $commands = ['config:cache', 'route:cache', 'view:cache'];
                    $results = [];
                    
                    foreach ($commands as $cmd) {
                        ob_start();
                        $kernel->call($cmd);
                        $results[$cmd] = ob_get_clean();
                    }
                    
                    echo '<div class="result">';
                    echo '<strong>✅ Optimization berhasil!</strong><br>';
                    foreach ($results as $cmd => $output) {
                        echo '<strong>' . $cmd . ':</strong><br>';
                        echo '<pre>' . htmlspecialchars($output) . '</pre>';
                    }
                    echo '</div>';
                } catch (Exception $e) {
                    echo '<div class="error">';
                    echo '<strong>❌ Error:</strong><br>';
                    echo htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
                
                echo '</div>';
            }

            if ($action === 'clear-cache') {
                echo '<div class="action-card">';
                echo '<h3>🧹 Clearing Cache...</h3>';
                
                try {
                    require $appPath . '/vendor/autoload.php';
                    $app = require_once $appPath . '/bootstrap/app.php';
                    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                    
                    $commands = ['cache:clear', 'config:clear', 'route:clear', 'view:clear'];
                    $results = [];
                    
                    foreach ($commands as $cmd) {
                        ob_start();
                        $kernel->call($cmd);
                        $results[$cmd] = ob_get_clean();
                    }
                    
                    echo '<div class="result">';
                    echo '<strong>✅ Cache berhasil dibersihkan!</strong><br>';
                    foreach ($results as $cmd => $output) {
                        echo '<strong>' . $cmd . ':</strong><br>';
                        echo '<pre>' . htmlspecialchars($output) . '</pre>';
                    }
                    echo '</div>';
                } catch (Exception $e) {
                    echo '<div class="error">';
                    echo '<strong>❌ Error:</strong><br>';
                    echo htmlspecialchars($e->getMessage());
                    echo '</div>';
                }
                
                echo '</div>';
            }

            if ($action === 'check') {
                echo '<div class="action-card">';
                echo '<h3>🔍 System Check</h3>';
                
                $checks = [
                    'PHP Version' => PHP_VERSION . ' (Required: 8.2+)',
                    'App Path' => $appPath . ' - ' . (is_dir($appPath) ? '✅ Exists' : '❌ Not Found'),
                    'Vendor Path' => $appPath . '/vendor - ' . (is_dir($appPath . '/vendor') ? '✅ Exists' : '❌ Not Found'),
                    '.env File' => $appPath . '/.env - ' . (file_exists($appPath . '/.env') ? '✅ Exists' : '❌ Not Found'),
                    'Storage Writable' => is_writable($appPath . '/storage') ? '✅ Yes' : '❌ No',
                    'Bootstrap Cache Writable' => is_writable($appPath . '/bootstrap/cache') ? '✅ Yes' : '❌ No',
                    'Storage Link' => file_exists($publicPath . '/storage') ? '✅ Exists' : '❌ Not Found',
                ];
                
                echo '<div class="info-box">';
                foreach ($checks as $label => $value) {
                    echo '<strong>' . $label . ':</strong> ' . $value . '<br>';
                }
                echo '</div>';
                
                echo '</div>';
            }
            ?>

            <!-- Action Cards -->
            <div class="action-card">
                <h3>🔍 1. System Check</h3>
                <p>Cek konfigurasi sistem dan path aplikasi</p>
                <a href="?action=check" class="btn">Run Check</a>
            </div>

            <div class="action-card">
                <h3>🗄️ 2. Run Migrations</h3>
                <p>Jalankan database migrations untuk membuat tabel</p>
                <a href="?action=migrate" class="btn">Run Migrations</a>
            </div>

            <div class="action-card">
                <h3>🌱 3. Run Seeders</h3>
                <p>Isi database dengan data awal (admin user, categories, sample products)</p>
                <a href="?action=seed" class="btn">Run Seeders</a>
            </div>

            <div class="action-card">
                <h3>🔗 4. Create Storage Link</h3>
                <p>Buat symbolic link untuk storage (diperlukan untuk upload images)</p>
                <a href="?action=storage-link" class="btn">Create Link</a>
            </div>

            <div class="action-card">
                <h3>⚡ 5. Optimize Application</h3>
                <p>Cache config, routes, dan views untuk performa maksimal</p>
                <a href="?action=optimize" class="btn">Optimize</a>
            </div>

            <div class="action-card">
                <h3>🧹 6. Clear Cache</h3>
                <p>Bersihkan semua cache (gunakan jika ada masalah)</p>
                <a href="?action=clear-cache" class="btn">Clear Cache</a>
            </div>

            <div class="info-box">
                <strong>📝 Urutan Deployment:</strong><br>
                1. System Check<br>
                2. Run Migrations<br>
                3. Run Seeders<br>
                4. Create Storage Link<br>
                5. Optimize Application<br>
                <br>
                <strong>Login Credentials:</strong><br>
                Email: admin@dexornit.store<br>
                Password: password
            </div>

            <div class="action-card">
                <h3>🗑️ Delete This File</h3>
                <p style="color: #dc3545;">Setelah deployment selesai, HAPUS file ini untuk keamanan!</p>
                <a href="?action=delete" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus file ini?')">Delete deploy-helper.php</a>
            </div>

            <?php
            if ($action === 'delete') {
                if (unlink(__FILE__)) {
                    echo '<div class="result">';
                    echo '<strong>✅ File berhasil dihapus!</strong><br>';
                    echo 'Deployment selesai. Silakan akses aplikasi Anda.';
                    echo '</div>';
                } else {
                    echo '<div class="error">';
                    echo '<strong>❌ Gagal menghapus file.</strong><br>';
                    echo 'Silakan hapus manual via File Manager.';
                    echo '</div>';
                }
            }
            ?>
        </div>

        <div class="footer">
            <p>Dexornit Store Deploy Helper v1.0</p>
            <p>⚠️ Jangan lupa hapus file ini setelah deployment selesai!</p>
        </div>
    </div>
</body>
</html>
