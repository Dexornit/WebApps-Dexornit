<?php
/**
 * Wanseven Simple Installer
 * NO dependencies, PURE PHP
 */

// Enable error display for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if already installed
if (file_exists(__DIR__ . '/../storage/app/.installed')) {
    die('Already installed! Delete storage/app/.installed to reinstall.');
}

// Ensure storage directory exists
if (!is_dir(__DIR__ . '/../storage/app')) {
    mkdir(__DIR__ . '/../storage/app', 0755, true);
}

session_start();

// Process form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'] ?? 1;
    
    if ($step == 1) {
        // Save database config
        $_SESSION['db_type'] = $_POST['db_type'] ?? 'sqlite';
        $_SESSION['db_host'] = $_POST['db_host'] ?? 'localhost';
        $_SESSION['db_port'] = $_POST['db_port'] ?? '3306';
        $_SESSION['db_name'] = $_POST['db_name'] ?? '';
        $_SESSION['db_user'] = $_POST['db_user'] ?? '';
        $_SESSION['db_pass'] = $_POST['db_pass'] ?? '';
        header('Location: install.php?step=2');
        exit;
    }
    
    if ($step == 2) {
        // Save admin config
        $_SESSION['admin_name'] = $_POST['admin_name'] ?? '';
        $_SESSION['admin_email'] = $_POST['admin_email'] ?? '';
        $_SESSION['admin_password'] = password_hash($_POST['admin_password'] ?? '', PASSWORD_BCRYPT);
        $_SESSION['app_name'] = $_POST['app_name'] ?? 'Wanseven';
        $_SESSION['app_url'] = $_POST['app_url'] ?? '';
        header('Location: install.php?step=3');
        exit;
    }
    
    if ($step == 3) {
        // DO INSTALLATION
        try {
            // Ensure .env.example exists
            if (!file_exists(__DIR__ . '/../.env.example')) {
                throw new Exception('.env.example file not found!');
            }
            
            // Create .env
            if (!file_exists(__DIR__ . '/../.env')) {
                if (!copy(__DIR__ . '/../.env.example', __DIR__ . '/../.env')) {
                    throw new Exception('Failed to create .env file. Check permissions.');
                }
            }
            
            // Update .env
            $env = file_get_contents(__DIR__ . '/../.env');
            if ($env === false) {
                throw new Exception('Failed to read .env file.');
            }
            
            $env = preg_replace('/^APP_NAME=.*/m', 'APP_NAME="' . $_SESSION['app_name'] . '"', $env);
            $env = preg_replace('/^APP_URL=.*/m', 'APP_URL=' . $_SESSION['app_url'], $env);
            $env = preg_replace('/^APP_KEY=.*/m', 'APP_KEY=base64:' . base64_encode(random_bytes(32)), $env);
            
            if ($_SESSION['db_type'] === 'sqlite') {
                $env = preg_replace('/^DB_CONNECTION=.*/m', 'DB_CONNECTION=sqlite', $env);
                $dbPath = __DIR__ . '/../database/database.sqlite';
                if (!file_exists($dbPath)) {
                    if (!touch($dbPath)) {
                        throw new Exception('Failed to create SQLite database file.');
                    }
                }
            } else {
                $env = preg_replace('/^DB_CONNECTION=.*/m', 'DB_CONNECTION=mysql', $env);
                $env = preg_replace('/^DB_HOST=.*/m', 'DB_HOST=' . $_SESSION['db_host'], $env);
                $env = preg_replace('/^DB_PORT=.*/m', 'DB_PORT=' . $_SESSION['db_port'], $env);
                $env = preg_replace('/^DB_DATABASE=.*/m', 'DB_DATABASE=' . $_SESSION['db_name'], $env);
                $env = preg_replace('/^DB_USERNAME=.*/m', 'DB_USERNAME=' . $_SESSION['db_user'], $env);
                $env = preg_replace('/^DB_PASSWORD=.*/m', 'DB_PASSWORD=' . $_SESSION['db_pass'], $env);
            }
            
            if (file_put_contents(__DIR__ . '/../.env', $env) === false) {
                throw new Exception('Failed to write .env file. Check permissions.');
            }
            
            // Ensure storage/app directory exists
            if (!is_dir(__DIR__ . '/../storage/app')) {
                mkdir(__DIR__ . '/../storage/app', 0755, true);
            }
            
            // Create marker
            $marker = [
                'installed_at' => date('Y-m-d H:i:s'),
                'admin_email' => $_SESSION['admin_email'],
                'admin_name' => $_SESSION['admin_name'],
                'admin_password_hash' => $_SESSION['admin_password'],
                'note' => 'Upload vendor/ folder or run composer install, then run: php artisan migrate && php artisan db:seed'
            ];
            
            if (file_put_contents(__DIR__ . '/../storage/app/.installed', json_encode($marker, JSON_PRETTY_PRINT)) === false) {
                throw new Exception('Failed to create installation marker. Check storage/app permissions.');
            }
            
            session_destroy();
            header('Location: install.php?step=4');
            exit;
            
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

$step = $_GET['step'] ?? 1;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wanseven Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full">
            <h1 class="text-3xl font-bold text-center mb-2">Wanseven</h1>
            <p class="text-center text-gray-600 mb-8">Quick Installer</p>
            
            <?php if ($step == 1): ?>
                <h2 class="text-xl font-semibold mb-4">Database Setup</h2>
                <form method="POST">
                    <input type="hidden" name="step" value="1">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Database Type</label>
                        <select name="db_type" id="dbType" onchange="toggleDb()" class="w-full px-3 py-2 border rounded">
                            <option value="sqlite">SQLite (Easy)</option>
                            <option value="mysql">MySQL</option>
                        </select>
                    </div>
                    <div id="mysqlFields" style="display:none">
                        <input type="text" name="db_host" placeholder="Host" value="localhost" class="w-full px-3 py-2 border rounded mb-2">
                        <input type="text" name="db_port" placeholder="Port" value="3306" class="w-full px-3 py-2 border rounded mb-2">
                        <input type="text" name="db_name" placeholder="Database Name" class="w-full px-3 py-2 border rounded mb-2">
                        <input type="text" name="db_user" placeholder="Username" class="w-full px-3 py-2 border rounded mb-2">
                        <input type="password" name="db_pass" placeholder="Password" class="w-full px-3 py-2 border rounded mb-2">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Next</button>
                </form>
                <script>
                    function toggleDb() {
                        document.getElementById('mysqlFields').style.display = 
                            document.getElementById('dbType').value === 'mysql' ? 'block' : 'none';
                    }
                </script>
            
            <?php elseif ($step == 2): ?>
                <h2 class="text-xl font-semibold mb-4">Admin & Settings</h2>
                <form method="POST">
                    <input type="hidden" name="step" value="2">
                    <input type="text" name="admin_name" placeholder="Admin Name" required class="w-full px-3 py-2 border rounded mb-2">
                    <input type="email" name="admin_email" placeholder="Admin Email" required class="w-full px-3 py-2 border rounded mb-2">
                    <input type="password" name="admin_password" placeholder="Password (min 8)" required minlength="8" class="w-full px-3 py-2 border rounded mb-2">
                    <input type="text" name="app_name" placeholder="App Name" value="Wanseven" required class="w-full px-3 py-2 border rounded mb-2">
                    <input type="url" name="app_url" placeholder="App URL" value="<?= 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') ?>" required class="w-full px-3 py-2 border rounded mb-4">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Next</button>
                </form>
            
            <?php elseif ($step == 3): ?>
                <h2 class="text-xl font-semibold mb-4">Ready to Install</h2>
                <p class="text-gray-600 mb-4">Click install to complete setup.</p>
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="step" value="3">
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Install Now</button>
                </form>
            
            <?php elseif ($step == 4): ?>
                <div class="text-center">
                    <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <h2 class="text-2xl font-bold text-green-600 mb-4">✅ Instalasi Berhasil!</h2>
                    <p class="text-gray-600 mb-6">Konfigurasi dasar sudah selesai.</p>
                    
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 text-left">
                        <h3 class="font-bold text-yellow-800 mb-2">⚠️ Langkah Selanjutnya:</h3>
                        <ol class="text-sm text-yellow-700 space-y-2 list-decimal list-inside">
                            <li><strong>Upload folder vendor/</strong> dari komputer lokal ke server, ATAU</li>
                            <li><strong>Jalankan via SSH:</strong> <code class="bg-yellow-100 px-2 py-1 rounded">composer install</code></li>
                            <li><strong>Jalankan migrasi:</strong> <code class="bg-yellow-100 px-2 py-1 rounded">php artisan migrate</code></li>
                            <li><strong>Buat admin user:</strong> <code class="bg-yellow-100 px-2 py-1 rounded">php artisan db:seed</code></li>
                        </ol>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded mb-6 text-left text-sm">
                        <p class="text-blue-800"><strong>💡 Tips:</strong> Jika tidak ada akses SSH, upload folder <code>vendor/</code> menggunakan FTP/File Manager. Folder ini berisi semua library Laravel yang dibutuhkan.</p>
                    </div>
                    
                    <a href="/" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-semibold">Buka Website</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
