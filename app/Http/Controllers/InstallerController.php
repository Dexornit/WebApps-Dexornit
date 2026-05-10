<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;

class InstallerController extends Controller
{
    public function index()
    {
        $requirements = $this->checkRequirements();
        return view('installer.index', compact('requirements'));
    }

    public function install(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'db_host'          => 'required|string',
                'db_port'          => 'required|string',
                'db_name'          => 'required|string',
                'db_user'          => 'required|string',
                'db_pass'          => 'nullable|string',
                'admin_name'       => 'required|string|max:255',
                'admin_email'      => 'required|email|max:255',
                'admin_password'   => 'required|min:8|confirmed',
                'app_name'         => 'required|string|max:255',
                'app_url'          => 'required|url',
            ]);

            // Step 1: Ensure storage directories exist and are writable
            $this->ensureStorageDirectories();

            // Step 2: Write .env file
            $this->writeEnvFile($validated);

            // Step 3: Generate APP_KEY
            Artisan::call('key:generate', ['--force' => true]);

            // Step 4: Clear bootstrap cache files (manual, no artisan needed)
            $this->clearBootstrapCache();

            // Step 5: Re-bootstrap config from new .env
            // We reload the env file manually so DB connection uses new creds
            $this->reloadEnv();

            // Step 6: Test DB connection with new credentials
            try {
                config([
                    'database.connections.mysql.host'     => $validated['db_host'],
                    'database.connections.mysql.port'     => $validated['db_port'],
                    'database.connections.mysql.database' => $validated['db_name'],
                    'database.connections.mysql.username' => $validated['db_user'],
                    'database.connections.mysql.password' => $validated['db_pass'] ?? '',
                ]);
                DB::purge('mysql');
                DB::reconnect('mysql');
                DB::connection('mysql')->getPdo();
            } catch (Exception $dbError) {
                throw new Exception('Database connection failed: ' . $dbError->getMessage());
            }

            // Step 7: Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // Step 8: Create storage symlink (graceful fail on shared hosting)
            if (!file_exists(public_path('storage'))) {
                try {
                    Artisan::call('storage:link');
                } catch (Exception $e) {
                    // Create manual symlink fallback
                    @symlink(storage_path('app/public'), public_path('storage'));
                }
            }

            // Step 9: Create admin user
            User::create([
                'name'     => $validated['admin_name'],
                'email'    => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
            ]);

            // Step 10: Mark as installed
            $this->createInstallationMarker($validated);

            // Step 11: Remove vite hot file (prevent Vite dev server conflicts)
            $hotFile = public_path('hot');
            if (file_exists($hotFile)) {
                @unlink($hotFile);
            }

            return redirect('/login')
                ->with('success', 'Instalasi berhasil! Silakan login dengan akun admin Anda.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Instalasi gagal: ' . $e->getMessage()]);
        }
    }

    private function checkRequirements()
    {
        $requirements = [
            'php_version' => [
                'name'    => 'PHP Version >= 8.1',
                'status'  => version_compare(PHP_VERSION, '8.1.0', '>='),
                'current' => PHP_VERSION,
            ],
            'extensions' => [],
        ];

        $requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];

        foreach ($requiredExtensions as $ext) {
            $requirements['extensions'][$ext] = [
                'name'   => strtoupper($ext) . ' Extension',
                'status' => extension_loaded($ext),
            ];
        }

        $requirements['permissions'] = [
            'storage' => [
                'name'   => 'storage/ directory writable',
                'status' => is_writable(storage_path()),
            ],
            'bootstrap_cache' => [
                'name'   => 'bootstrap/cache/ writable',
                'status' => is_writable(base_path('bootstrap/cache')),
            ],
            'env_writable' => [
                'name'   => '.env file writable',
                'status' => is_writable(base_path('.env')) || is_writable(base_path()),
            ],
        ];

        return $requirements;
    }

    private function ensureStorageDirectories()
    {
        $dirs = [
            storage_path('app'),
            storage_path('app/public'),
            storage_path('app/public/logos'),
            storage_path('app/public/products'),
            storage_path('framework'),
            storage_path('framework/cache'),
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
            base_path('bootstrap/cache'),
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
        }
    }

    private function writeEnvFile($data)
    {
        $appKey    = 'base64:' . base64_encode(random_bytes(32));
        $appName   = addslashes($data['app_name']);
        $appUrl    = rtrim($data['app_url'], '/');
        $dbHost    = $data['db_host'];
        $dbPort    = $data['db_port'];
        $dbName    = $data['db_name'];
        $dbUser    = $data['db_user'];
        $dbPass    = $data['db_pass'] ?? '';

        $env = <<<ENV
APP_NAME="{$appName}"
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_URL={$appUrl}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=10

LOG_CHANNEL=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST={$dbHost}
DB_PORT={$dbPort}
DB_DATABASE={$dbName}
DB_USERNAME={$dbUser}
DB_PASSWORD={$dbPass}

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

CACHE_STORE=file

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="{$appName}"

VITE_APP_NAME="{$appName}"
ENV;

        file_put_contents(base_path('.env'), $env);
    }

    private function clearBootstrapCache()
    {
        $cacheFiles = [
            base_path('bootstrap/cache/config.php'),
            base_path('bootstrap/cache/routes-v7.php'),
            base_path('bootstrap/cache/services.php'),
            base_path('bootstrap/cache/packages.php'),
            base_path('bootstrap/cache/events.php'),
        ];
        foreach ($cacheFiles as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }

    private function reloadEnv()
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) return;

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            if (!str_contains($line, '=')) continue;
            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            putenv("{$key}={$value}");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }

    private function createInstallationMarker($data)
    {
        $appDir = storage_path('app');
        if (!is_dir($appDir)) {
            @mkdir($appDir, 0755, true);
        }

        $markerData = [
            'installed_at' => now()->toDateTimeString(),
            'version'      => '1.0.0',
            'php_version'  => PHP_VERSION,
            'app_name'     => $data['app_name'],
        ];

        file_put_contents(
            storage_path('app/.installed'),
            json_encode($markerData, JSON_PRETTY_PRINT)
        );
    }
}
