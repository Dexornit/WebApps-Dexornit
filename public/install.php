<?php
/**
 * Wanseven All-in-One Installer
 * MySQL Only - Complete Setup
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(300);

// Check if already installed
if (file_exists(__DIR__ . '/../storage/app/.installed')) {
    die('Already installed! Delete storage/app/.installed to reinstall.');
}

// Ensure storage directories exist
$dirs = [
    __DIR__ . '/../storage/app',
    __DIR__ . '/../storage/framework/cache',
    __DIR__ . '/../storage/framework/sessions',
    __DIR__ . '/../storage/framework/views',
    __DIR__ . '/../storage/logs',
    __DIR__ . '/../bootstrap/cache'
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

session_start();

// Process form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'] ?? 1;
    
    if ($step == 1) {
        header('Location: install.php?step=2');
        exit;
    }
    
    if ($step == 2) {
        $_SESSION['db_host'] = $_POST['db_host'] ?? 'localhost';
        $_SESSION['db_port'] = $_POST['db_port'] ?? '3306';
        $_SESSION['db_name'] = $_POST['db_name'] ?? '';
        $_SESSION['db_user'] = $_POST['db_user'] ?? '';
        $_SESSION['db_pass'] = $_POST['db_pass'] ?? '';
        header('Location: install.php?step=3');
        exit;
    }
    
    if ($step == 3) {
        $_SESSION['admin_name'] = $_POST['admin_name'] ?? '';
        $_SESSION['admin_email'] = $_POST['admin_email'] ?? '';
        $_SESSION['admin_password'] = $_POST['admin_password'] ?? '';
        $_SESSION['admin_password_hash'] = password_hash($_POST['admin_password'] ?? '', PASSWORD_BCRYPT);
        $_SESSION['app_name'] = $_POST['app_name'] ?? 'Wanseven';
        $_SESSION['app_url'] = $_POST['app_url'] ?? '';
        header('Location: install.php?step=4');
        exit;
    }
    
    if ($step == 4) {
        $errors = [];
        $logs = [];
        
        try {
            // 1. Create .env
            $logs[] = "→ Creating .env file...";
            if (!file_exists(__DIR__ . '/../.env.example')) {
                throw new Exception('.env.example not found!');
            }
            
            $env = "APP_NAME=\"" . $_SESSION['app_name'] . "\"
APP_ENV=production
APP_KEY=base64:" . base64_encode(random_bytes(32)) . "
APP_DEBUG=false
APP_URL=" . $_SESSION['app_url'] . "

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=" . $_SESSION['db_host'] . "
DB_PORT=" . $_SESSION['db_port'] . "
DB_DATABASE=" . $_SESSION['db_name'] . "
DB_USERNAME=" . $_SESSION['db_user'] . "
DB_PASSWORD=" . $_SESSION['db_pass'] . "

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

CACHE_STORE=file

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS=\"hello@example.com\"
MAIL_FROM_NAME=\"\${APP_NAME}\"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME=\"\${APP_NAME}\"
";
            
            file_put_contents(__DIR__ . '/../.env', $env);
            $logs[] = "✓ .env created";
            
            // 2. Clear bootstrap cache
            $logs[] = "→ Clearing bootstrap cache...";
            $cacheFiles = [
                __DIR__ . '/../bootstrap/cache/config.php',
                __DIR__ . '/../bootstrap/cache/routes-v7.php',
                __DIR__ . '/../bootstrap/cache/services.php',
                __DIR__ . '/../bootstrap/cache/packages.php'
            ];
            foreach ($cacheFiles as $file) {
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
            $logs[] = "✓ Bootstrap cache cleared";
            
            // 3. Run migrations using Laravel
            $logs[] = "→ Running database migrations...";
            
            if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
                throw new Exception('vendor/ folder not found! Upload it first.');
            }
            
            require __DIR__ . '/../vendor/autoload.php';
            $app = require_once __DIR__ . '/../bootstrap/app.php';
            
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
            $kernel->bootstrap();
            
            // Test database connection
            try {
                \Illuminate\Support\Facades\DB::connection()->getPdo();
                $logs[] = "✓ Database connection successful";
            } catch (Exception $dbError) {
                throw new Exception("Database connection failed: " . $dbError->getMessage());
            }
            
            // Migrate
            $kernel->call('migrate', ['--force' => true]);
            $logs[] = "✓ Migrations completed";
            
            // Create admin user
            $logs[] = "→ Creating admin user...";
            $existingUser = \App\Models\User::where('email', $_SESSION['admin_email'])->first();
            
            if ($existingUser) {
                $existingUser->name = $_SESSION['admin_name'];
                $existingUser->password = $_SESSION['admin_password_hash'];
                $existingUser->save();
                $logs[] = "✓ Admin user updated: " . $_SESSION['admin_email'];
            } else {
                $user = new \App\Models\User();
                $user->name = $_SESSION['admin_name'];
                $user->email = $_SESSION['admin_email'];
                $user->password = $_SESSION['admin_password_hash'];
                $user->save();
                $logs[] = "✓ Admin user created: " . $_SESSION['admin_email'];
            }
            
            // Seed data (optional)
            try {
                $kernel->call('db:seed', ['--force' => true]);
                $logs[] = "✓ Sample data seeded";
            } catch (Exception $e) {
                $logs[] = "⚠ Seeding skipped (optional)";
            }
            
            // Clear all caches
            $logs[] = "→ Clearing application caches...";
            try {
                $kernel->call('config:clear');
                $kernel->call('cache:clear');
                $kernel->call('route:clear');
                $kernel->call('view:clear');
                $logs[] = "✓ All caches cleared";
            } catch (Exception $e) {
                $logs[] = "⚠ Cache clearing skipped";
            }
            
            // Create installation marker
            $logs[] = "→ Finalizing installation...";
            $marker = [
                'installed_at' => date('Y-m-d H:i:s'),
                'admin_email' => $_SESSION['admin_email'],
                'admin_name' => $_SESSION['admin_name'],
                'version' => '1.0.0'
            ];
            file_put_contents(__DIR__ . '/../storage/app/.installed', json_encode($marker, JSON_PRETTY_PRINT));
            $logs[] = "✓ Installation complete!";
            
            $_SESSION['install_success'] = true;
            $_SESSION['install_logs'] = $logs;
            session_write_close();
            
            header('Location: install.php?step=5');
            exit;
            
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
            $_SESSION['install_errors'] = $errors;
            $_SESSION['install_logs'] = $logs;
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
        <div class="bg-white rounded-lg shadow-xl p-8 max-w-2xl w-full">
            <h1 class="text-3xl font-bold text-center mb-2">Wanseven</h1>
            <p class="text-center text-gray-600 mb-8">Installation Wizard</p>
            
            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex justify-between mb-2">
                    <span class="text-xs <?= $step >= 1 ? 'text-blue-600 font-semibold' : 'text-gray-400' ?>">Check</span>
                    <span class="text-xs <?= $step >= 2 ? 'text-blue-600 font-semibold' : 'text-gray-400' ?>">Database</span>
                    <span class="text-xs <?= $step >= 3 ? 'text-blue-600 font-semibold' : 'text-gray-400' ?>">Admin</span>
                    <span class="text-xs <?= $step >= 4 ? 'text-blue-600 font-semibold' : 'text-gray-400' ?>">Install</span>
                    <span class="text-xs <?= $step >= 5 ? 'text-green-600 font-semibold' : 'text-gray-400' ?>">Done</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all" style="width: <?= ($step / 5) * 100 ?>%"></div>
                </div>
            </div>
            
            <?php if ($step == 1): ?>
                <!-- Step 1: Requirements Check -->
                <h2 class="text-2xl font-semibold mb-4">📋 System Requirements</h2>
                
                <?php
                $checks = [];
                $checks[] = ['name' => 'PHP Version', 'status' => version_compare(PHP_VERSION, '8.1.0', '>='), 'value' => PHP_VERSION];
                $checks[] = ['name' => 'Vendor Folder', 'status' => file_exists(__DIR__ . '/../vendor/autoload.php'), 'value' => file_exists(__DIR__ . '/../vendor/autoload.php') ? 'Found' : 'Missing'];
                $checks[] = ['name' => 'Storage Writable', 'status' => is_writable(__DIR__ . '/../storage'), 'value' => is_writable(__DIR__ . '/../storage') ? 'Yes' : 'No'];
                $checks[] = ['name' => 'Cache Writable', 'status' => is_writable(__DIR__ . '/../bootstrap/cache'), 'value' => is_writable(__DIR__ . '/../bootstrap/cache') ? 'Yes' : 'No'];
                
                $allGood = true;
                foreach ($checks as $check) {
                    $allGood = $allGood && $check['status'];
                    $bgColor = $check['status'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
                    $icon = $check['status'] ? '✅' : '❌';
                    echo "<div class='mb-3 p-3 border rounded $bgColor'>";
                    echo "<div class='flex justify-between'>";
                    echo "<span class='font-medium'>{$icon} {$check['name']}</span>";
                    echo "<span class='text-sm text-gray-600'>{$check['value']}</span>";
                    echo "</div>";
                    echo "</div>";
                }
                ?>
                
                <?php if (!$allGood): ?>
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                        <p class="font-bold text-red-800">⚠️ Requirements Not Met</p>
                        <p class="text-sm text-red-700 mt-1">Please fix the issues above before continuing.</p>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="step" value="1">
                    <button type="submit" <?= !$allGood ? 'disabled' : '' ?> class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold <?= !$allGood ? 'opacity-50 cursor-not-allowed' : '' ?>">
                        Continue to Database Setup
                    </button>
                </form>
            
            <?php elseif ($step == 2): ?>
                <!-- Step 2: Database Setup -->
                <h2 class="text-2xl font-semibold mb-4">🗄️ MySQL Database</h2>
                
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <p class="text-sm text-yellow-800"><strong>⚠️ Important:</strong> Create your MySQL database first via cPanel/phpMyAdmin before continuing!</p>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="step" value="2">
                    <div class="space-y-3 mb-4">
                        <input type="text" name="db_host" placeholder="Database Host" value="localhost" required class="w-full px-3 py-2 border rounded">
                        <input type="text" name="db_port" placeholder="Database Port" value="3306" required class="w-full px-3 py-2 border rounded">
                        <input type="text" name="db_name" placeholder="Database Name" required class="w-full px-3 py-2 border rounded">
                        <input type="text" name="db_user" placeholder="Database Username" required class="w-full px-3 py-2 border rounded">
                        <input type="password" name="db_pass" placeholder="Database Password" class="w-full px-3 py-2 border rounded">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">Continue to Admin Setup</button>
                </form>
            
            <?php elseif ($step == 3): ?>
                <!-- Step 3: Admin Setup -->
                <h2 class="text-2xl font-semibold mb-4">👤 Admin Account</h2>
                <form method="POST">
                    <input type="hidden" name="step" value="3">
                    <div class="space-y-3 mb-4">
                        <input type="text" name="admin_name" placeholder="Admin Name" required class="w-full px-3 py-2 border rounded">
                        <input type="email" name="admin_email" placeholder="Admin Email" required class="w-full px-3 py-2 border rounded">
                        <input type="password" name="admin_password" placeholder="Password (min 8 characters)" required minlength="8" class="w-full px-3 py-2 border rounded">
                        <input type="text" name="app_name" placeholder="Application Name" value="Wanseven" required class="w-full px-3 py-2 border rounded">
                        <input type="url" name="app_url" placeholder="Application URL" value="<?= 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') ?>" required class="w-full px-3 py-2 border rounded">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">Continue to Installation</button>
                </form>
            
            <?php elseif ($step == 4): ?>
                <!-- Step 4: Installing -->
                <h2 class="text-2xl font-semibold mb-4">⚙️ Installing...</h2>
                
                <?php if (isset($_SESSION['install_errors'])): ?>
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                        <p class="font-bold text-red-800">❌ Installation Failed</p>
                        <?php foreach ($_SESSION['install_errors'] as $error): ?>
                            <p class="text-sm text-red-700 mt-1"><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (isset($_SESSION['install_logs'])): ?>
                        <div class="bg-gray-900 text-green-400 p-4 rounded font-mono text-xs overflow-auto max-h-64 mb-4">
                            <?php foreach ($_SESSION['install_logs'] as $log): ?>
                                <?= htmlspecialchars($log) ?><br>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <a href="install.php?step=1" class="inline-block w-full text-center bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">Start Over</a>
                    
                    <?php
                    unset($_SESSION['install_errors']);
                    unset($_SESSION['install_logs']);
                    ?>
                <?php else: ?>
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded mb-4">
                        <p class="text-blue-800">⏳ Setting up your application...</p>
                        <p class="text-sm text-blue-600 mt-2">This may take 1-2 minutes.</p>
                    </div>
                    
                    <form method="POST" id="installForm">
                        <input type="hidden" name="step" value="4">
                        <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-semibold">
                            🚀 Start Installation
                        </button>
                    </form>
                <?php endif; ?>
            
            <?php elseif ($step == 5): ?>
                <!-- Step 5: Success -->
                <div class="text-center">
                    <svg class="w-20 h-20 text-green-500 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <h2 class="text-3xl font-bold text-green-600 mb-4">🎉 Installation Complete!</h2>
                    <p class="text-gray-600 mb-6">Your application is ready to use.</p>
                    
                    <?php if (isset($_SESSION['install_logs'])): ?>
                        <details class="mb-6 text-left">
                            <summary class="cursor-pointer text-sm text-blue-600 hover:text-blue-800 mb-2">View Installation Log</summary>
                            <div class="bg-gray-900 text-green-400 p-4 rounded font-mono text-xs overflow-auto max-h-64">
                                <?php foreach ($_SESSION['install_logs'] as $log): ?>
                                    <?= htmlspecialchars($log) ?><br>
                                <?php endforeach; ?>
                            </div>
                        </details>
                    <?php endif; ?>
                    
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded mb-6 text-left">
                        <h3 class="font-bold text-blue-800 mb-2">📝 Your Login Credentials:</h3>
                        <p class="text-sm text-blue-700">Email: <strong><?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?></strong></p>
                        <p class="text-sm text-blue-700">Password: <em>(the password you entered)</em></p>
                    </div>
                    
                    <a href="/" class="inline-block bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 font-semibold text-lg">
                        Open Website →
                    </a>
                    
                    <p class="text-xs text-gray-500 mt-4">You can delete install.php for security.</p>
                </div>
                
                <?php session_destroy(); ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
