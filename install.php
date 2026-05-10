<?php
/**
 * Wanseven Web Installer
 * Simple installation wizard - NO Laravel dependencies needed!
 */

session_start();

// Check if already installed
$installedMarker = __DIR__ . '/storage/app/.installed';
if (file_exists($installedMarker)) {
    header('Location: /');
    exit('Application already installed. Delete storage/app/.installed to reinstall.');
}

// Helper functions
function checkRequirements() {
    return [
        'PHP >= 8.3' => version_compare(PHP_VERSION, '8.3.0', '>='),
        'PDO Extension' => extension_loaded('pdo'),
        'Mbstring Extension' => extension_loaded('mbstring'),
        'OpenSSL Extension' => extension_loaded('openssl'),
        'Tokenizer Extension' => extension_loaded('tokenizer'),
        'XML Extension' => extension_loaded('xml'),
        'Ctype Extension' => extension_loaded('ctype'),
        'JSON Extension' => extension_loaded('json'),
        'BCMath Extension' => extension_loaded('bcmath'),
        'Fileinfo Extension' => extension_loaded('fileinfo'),
        'storage/ writable' => is_writable(__DIR__ . '/storage'),
        'bootstrap/cache/ writable' => is_writable(__DIR__ . '/bootstrap/cache'),
    ];
}

function allRequirementsMet($requirements) {
    foreach ($requirements as $req => $met) {
        if (!$met) return false;
    }
    return true;
}

function updateEnv($key, $value) {
    $envPath = __DIR__ . '/.env';
    $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';
    
    $value = str_replace('"', '\\"', $value);
    if (strpos($value, ' ') !== false || strpos($value, '#') !== false) {
        $value = '"' . $value . '"';
    }
    
    $pattern = "/^{$key}=.*/m";
    $replacement = "{$key}={$value}";
    
    if (preg_match($pattern, $envContent)) {
        $envContent = preg_replace($pattern, $replacement, $envContent);
    } else {
        $envContent .= "\n{$replacement}";
    }
    
    file_put_contents($envPath, $envContent);
}

function testDatabaseConnection($config) {
    try {
        if ($config['type'] === 'sqlite') {
            $dbPath = __DIR__ . '/database/database.sqlite';
            if (!file_exists($dbPath)) {
                touch($dbPath);
            }
            new PDO('sqlite:' . $dbPath);
            return ['success' => true];
        } else {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            new PDO($dsn, $config['username'], $config['password']);
            return ['success' => true];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Handle form submissions
$step = $_GET['step'] ?? 1;
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 2) {
        // Database configuration
        $dbType = $_POST['db_type'] ?? 'sqlite';
        
        if ($dbType === 'sqlite') {
            $config = ['type' => 'sqlite'];
        } else {
            $config = [
                'type' => 'mysql',
                'host' => $_POST['db_host'] ?? 'localhost',
                'port' => $_POST['db_port'] ?? '3306',
                'database' => $_POST['db_name'] ?? '',
                'username' => $_POST['db_user'] ?? '',
                'password' => $_POST['db_pass'] ?? '',
            ];
        }
        
        $result = testDatabaseConnection($config);
        
        if ($result['success']) {
            $_SESSION['db_config'] = $config;
            header('Location: ?step=3');
            exit;
        } else {
            $error = 'Database connection failed: ' . $result['error'];
        }
    }
    
    if ($step == 3) {
        // Admin account
        $name = $_POST['admin_name'] ?? '';
        $email = $_POST['admin_email'] ?? '';
        $password = $_POST['admin_password'] ?? '';
        $passwordConfirm = $_POST['admin_password_confirmation'] ?? '';
        
        if (empty($name) || empty($email) || empty($password)) {
            $error = 'All fields are required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters';
        } elseif ($password !== $passwordConfirm) {
            $error = 'Passwords do not match';
        } else {
            $_SESSION['admin'] = [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            ];
            header('Location: ?step=4');
            exit;
        }
    }
    
    if ($step == 4) {
        // Application settings
        $appName = $_POST['app_name'] ?? 'Wanseven';
        $appUrl = $_POST['app_url'] ?? '';
        
        if (empty($appUrl) || !filter_var($appUrl, FILTER_VALIDATE_URL)) {
            $error = 'Valid application URL is required';
        } else {
            $_SESSION['app_settings'] = [
                'name' => $appName,
                'url' => $appUrl,
            ];
            header('Location: ?step=5');
            exit;
        }
    }
    
    if ($step == 5) {
        // Final installation
        try {
            // Create .env if not exists
            if (!file_exists(__DIR__ . '/.env')) {
                copy(__DIR__ . '/.env.example', __DIR__ . '/.env');
            }
            
            // Update .env with settings
            $dbConfig = $_SESSION['db_config'];
            $appSettings = $_SESSION['app_settings'];
            
            updateEnv('APP_NAME', $appSettings['name']);
            updateEnv('APP_URL', $appSettings['url']);
            updateEnv('APP_KEY', 'base64:' . base64_encode(random_bytes(32)));
            
            if ($dbConfig['type'] === 'sqlite') {
                updateEnv('DB_CONNECTION', 'sqlite');
            } else {
                updateEnv('DB_CONNECTION', 'mysql');
                updateEnv('DB_HOST', $dbConfig['host']);
                updateEnv('DB_PORT', $dbConfig['port']);
                updateEnv('DB_DATABASE', $dbConfig['database']);
                updateEnv('DB_USERNAME', $dbConfig['username']);
                updateEnv('DB_PASSWORD', $dbConfig['password']);
            }
            
            // Run migrations (if vendor exists)
            if (file_exists(__DIR__ . '/vendor/autoload.php')) {
                require __DIR__ . '/vendor/autoload.php';
                
                // Bootstrap Laravel
                $app = require_once __DIR__ . '/bootstrap/app.php';
                $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
                
                // Run migrations
                $kernel->call('migrate', ['--force' => true]);
                
                // Create admin user
                $admin = $_SESSION['admin'];
                \App\Models\User::create([
                    'name' => $admin['name'],
                    'email' => $admin['email'],
                    'password' => $admin['password'],
                ]);
            }
            
            // Create installation marker
            $markerData = [
                'installed_at' => date('Y-m-d H:i:s'),
                'version' => '1.0.0',
                'php_version' => PHP_VERSION,
                'database_type' => $dbConfig['type'],
            ];
            file_put_contents($installedMarker, json_encode($markerData, JSON_PRETTY_PRINT));
            
            // Clear session
            session_destroy();
            
            $success = true;
            
        } catch (Exception $e) {
            $error = 'Installation failed: ' . $e->getMessage();
        }
    }
}

$requirements = checkRequirements();
$allMet = allRequirementsMet($requirements);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wanseven Installer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-12">
        <div class="max-w-2xl mx-auto px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800">Wanseven</h1>
                <p class="text-gray-600 mt-2">Installation Wizard</p>
            </div>

            <!-- Progress Steps -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center">
                    <?php
                    $steps = [
                        1 => 'Requirements',
                        2 => 'Database',
                        3 => 'Admin Account',
                        4 => 'Settings',
                        5 => 'Complete'
                    ];
                    foreach ($steps as $num => $label) {
                        $active = $num == $step;
                        $completed = $num < $step;
                        $class = $active ? 'bg-blue-600 text-white' : ($completed ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600');
                        echo "<div class='flex flex-col items-center'>";
                        echo "<div class='w-10 h-10 rounded-full flex items-center justify-center {$class} font-semibold'>{$num}</div>";
                        echo "<span class='text-xs mt-2 text-gray-600'>{$label}</span>";
                        echo "</div>";
                        if ($num < 5) echo "<div class='flex-1 h-1 bg-gray-300 mx-2 mt-5'></div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Main Content -->
            <div class="bg-white rounded-lg shadow-md p-8">
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($step == 1): ?>
                    <!-- Step 1: Requirements -->
                    <h2 class="text-2xl font-bold mb-4">Server Requirements</h2>
                    <div class="space-y-2 mb-6">
                        <?php foreach ($requirements as $req => $met): ?>
                            <div class="flex items-center justify-between py-2 px-4 rounded hover:bg-gray-50">
                                <span><?= htmlspecialchars($req) ?></span>
                                <?php if ($met): ?>
                                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                <?php else: ?>
                                    <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($allMet): ?>
                        <a href="?step=2" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-3 rounded-lg font-semibold">
                            Next: Database Setup
                        </a>
                    <?php else: ?>
                        <button onclick="location.reload()" class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center py-3 rounded-lg font-semibold">
                            Recheck Requirements
                        </button>
                    <?php endif; ?>

                <?php elseif ($step == 2): ?>
                    <!-- Step 2: Database -->
                    <h2 class="text-2xl font-bold mb-4">Database Configuration</h2>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Database Type</label>
                            <select name="db_type" id="dbType" onchange="toggleDbFields()" class="w-full px-4 py-2 border rounded-lg">
                                <option value="sqlite">SQLite (Recommended)</option>
                                <option value="mysql">MySQL</option>
                            </select>
                        </div>
                        
                        <div id="mysqlFields" style="display:none;" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Host</label>
                                <input type="text" name="db_host" value="localhost" class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Port</label>
                                <input type="text" name="db_port" value="3306" class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Database Name</label>
                                <input type="text" name="db_name" class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                <input type="text" name="db_user" class="w-full px-4 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <input type="password" name="db_pass" class="w-full px-4 py-2 border rounded-lg">
                            </div>
                        </div>
                        
                        <div class="flex gap-4">
                            <a href="?step=1" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center py-3 rounded-lg font-semibold">
                                Previous
                            </a>
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
                                Test & Continue
                            </button>
                        </div>
                    </form>
                    <script>
                        function toggleDbFields() {
                            const type = document.getElementById('dbType').value;
                            document.getElementById('mysqlFields').style.display = type === 'mysql' ? 'block' : 'none';
                        }
                    </script>

                <?php elseif ($step == 3): ?>
                    <!-- Step 3: Admin Account -->
                    <h2 class="text-2xl font-bold mb-4">Create Admin Account</h2>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="admin_name" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="admin_email" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password (min 8 characters)</label>
                            <input type="password" name="admin_password" required minlength="8" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" name="admin_password_confirmation" required minlength="8" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div class="flex gap-4">
                            <a href="?step=2" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center py-3 rounded-lg font-semibold">
                                Previous
                            </a>
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
                                Continue
                            </button>
                        </div>
                    </form>

                <?php elseif ($step == 4): ?>
                    <!-- Step 4: Application Settings -->
                    <h2 class="text-2xl font-bold mb-4">Application Settings</h2>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                            <input type="text" name="app_name" value="Wanseven" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Application URL</label>
                            <input type="url" name="app_url" value="<?= htmlspecialchars('http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?>" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div class="flex gap-4">
                            <a href="?step=3" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center py-3 rounded-lg font-semibold">
                                Previous
                            </a>
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
                                Continue
                            </button>
                        </div>
                    </form>

                <?php elseif ($step == 5): ?>
                    <!-- Step 5: Installation -->
                    <?php if ($success): ?>
                        <div class="text-center">
                            <svg class="w-20 h-20 text-green-500 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <h2 class="text-3xl font-bold text-green-600 mb-4">Installation Complete!</h2>
                            <p class="text-gray-600 mb-6">Your application has been installed successfully.</p>
                            <a href="/login" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold">
                                Go to Login
                            </a>
                        </div>
                    <?php else: ?>
                        <h2 class="text-2xl font-bold mb-4">Ready to Install</h2>
                        <p class="text-gray-600 mb-6">Click the button below to complete the installation.</p>
                        <form method="POST">
                            <div class="flex gap-4">
                                <a href="?step=4" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white text-center py-3 rounded-lg font-semibold">
                                    Previous
                                </a>
                                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold">
                                    Install Now
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
