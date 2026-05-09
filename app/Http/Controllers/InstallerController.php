<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Exception;

class InstallerController extends Controller
{
    public function index()
    {
        // Check environment requirements
        $requirements = $this->checkRequirements();
        
        return view('installer.index', compact('requirements'));
    }
    
    public function install(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'db_type' => 'required|in:sqlite,mysql',
                'db_host' => 'required_if:db_type,mysql',
                'db_port' => 'required_if:db_type,mysql',
                'db_name' => 'required_if:db_type,mysql',
                'db_user' => 'required_if:db_type,mysql',
                'db_pass' => 'nullable',
                'admin_name' => 'required|string|max:255',
                'admin_email' => 'required|email|max:255',
                'admin_password' => 'required|min:8|confirmed',
                'app_name' => 'required|string|max:255',
                'app_url' => 'required|url',
            ]);
            
            // Step 1: Update .env file
            $this->updateEnvFile($validated);
            
            // Step 2: Generate APP_KEY
            Artisan::call('key:generate', ['--force' => true]);
            
            // Step 3: Clear all caches
            try { Artisan::call('config:clear'); } catch (Exception $e) {}
            try { Artisan::call('cache:clear'); } catch (Exception $e) {}
            try { Artisan::call('route:clear'); } catch (Exception $e) {}
            try { Artisan::call('view:clear'); } catch (Exception $e) {}
            
            // Step 4: Ensure storage directories exist and are writable
            $this->ensureStorageDirectories();
            
            // Step 5: Re-load config with new .env values
            // Purge config cache so new DB connection is used
            app()->forgetInstance('config');
            
            // Step 6: Test database connection
            DB::purge();
            DB::reconnect();
            DB::connection()->getPdo();
            
            // Step 7: Run migrations
            Artisan::call('migrate', ['--force' => true]);
            
            // Step 8: Create storage symlink if not exists
            if (!file_exists(public_path('storage'))) {
                try {
                    Artisan::call('storage:link');
                } catch (Exception $e) {
                    // Ignore if symlink cannot be created
                }
            }
            
            // Step 9: Create admin user
            User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
            ]);
            
            // Step 10: Create installation marker
            $this->createInstallationMarker($validated);
            
            // Step 11: Remove vite hot file if exists (production mode)
            $hotFile = public_path('hot');
            if (file_exists($hotFile)) {
                @unlink($hotFile);
            }
            
            return redirect('/login')->with('success', 'Instalasi berhasil! Silakan login dengan akun admin Anda.');
            
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Instalasi gagal: ' . $e->getMessage()]);
        }
    }
    
    private function checkRequirements()
    {
        $requirements = [
            'php_version' => [
                'name' => 'PHP Version >= 8.1',
                'status' => version_compare(PHP_VERSION, '8.1.0', '>='),
                'current' => PHP_VERSION,
            ],
            'extensions' => [],
        ];
        
        $requiredExtensions = ['pdo', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
        
        foreach ($requiredExtensions as $ext) {
            $requirements['extensions'][$ext] = [
                'name' => strtoupper($ext) . ' Extension',
                'status' => extension_loaded($ext),
            ];
        }
        
        $requirements['permissions'] = [
            'storage' => [
                'name' => 'storage/ directory',
                'status' => is_writable(storage_path()),
            ],
            'bootstrap_cache' => [
                'name' => 'bootstrap/cache/ directory',
                'status' => is_writable(base_path('bootstrap/cache')),
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
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
        }
    }
    
    private function updateEnvFile($data)
    {
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            if (file_exists(base_path('.env.example'))) {
                copy(base_path('.env.example'), $envPath);
            } else {
                file_put_contents($envPath, '');
            }
        }
        
        $envContent = file_get_contents($envPath);
        
        // Update APP settings
        $envContent = $this->updateEnvValue($envContent, 'APP_NAME', $data['app_name']);
        $envContent = $this->updateEnvValue($envContent, 'APP_URL', $data['app_url']);
        $envContent = $this->updateEnvValue($envContent, 'APP_ENV', 'production');
        $envContent = $this->updateEnvValue($envContent, 'APP_DEBUG', 'false');
        
        // Update DB settings
        if ($data['db_type'] === 'sqlite') {
            $envContent = $this->updateEnvValue($envContent, 'DB_CONNECTION', 'sqlite');
            
            // Remove or comment mysql-specific settings
            $envContent = $this->updateEnvValue($envContent, 'DB_HOST', '127.0.0.1');
            $envContent = $this->updateEnvValue($envContent, 'DB_PORT', '3306');
            $envContent = $this->updateEnvValue($envContent, 'DB_DATABASE', database_path('database.sqlite'));
            $envContent = $this->updateEnvValue($envContent, 'DB_USERNAME', 'root');
            $envContent = $this->updateEnvValue($envContent, 'DB_PASSWORD', '');
            
            // Ensure SQLite file exists
            $dbPath = database_path('database.sqlite');
            if (!file_exists($dbPath)) {
                touch($dbPath);
            }
        } else {
            $envContent = $this->updateEnvValue($envContent, 'DB_CONNECTION', 'mysql');
            $envContent = $this->updateEnvValue($envContent, 'DB_HOST', $data['db_host']);
            $envContent = $this->updateEnvValue($envContent, 'DB_PORT', $data['db_port']);
            $envContent = $this->updateEnvValue($envContent, 'DB_DATABASE', $data['db_name']);
            $envContent = $this->updateEnvValue($envContent, 'DB_USERNAME', $data['db_user']);
            $envContent = $this->updateEnvValue($envContent, 'DB_PASSWORD', $data['db_pass'] ?? '');
        }
        
        // Set session, cache, queue to file/cookie-based for shared hosting compatibility
        $envContent = $this->updateEnvValue($envContent, 'SESSION_DRIVER', 'file');
        $envContent = $this->updateEnvValue($envContent, 'CACHE_STORE', 'file');
        $envContent = $this->updateEnvValue($envContent, 'QUEUE_CONNECTION', 'sync');
        
        file_put_contents($envPath, $envContent);
    }
    
    private function updateEnvValue($envContent, $key, $value)
    {
        $pattern = "/^{$key}=.*/m";
        
        // Add quotes if value contains spaces or special characters
        if (preg_match('/\s|[#&]/', $value)) {
            $escapedValue = str_replace('"', '\\"', $value);
            $newLine = "{$key}=\"{$escapedValue}\"";
        } else {
            $newLine = "{$key}={$value}";
        }
        
        if (preg_match($pattern, $envContent)) {
            return preg_replace($pattern, $newLine, $envContent);
        } else {
            return $envContent . "\n{$newLine}";
        }
    }
    
    private function createInstallationMarker($data)
    {
        $appDir = storage_path('app');
        if (!is_dir($appDir)) {
            @mkdir($appDir, 0775, true);
        }
        
        $markerData = [
            'installed_at' => now()->toDateTimeString(),
            'version' => '1.0.0',
            'php_version' => PHP_VERSION,
            'database_type' => $data['db_type'],
            'app_name' => $data['app_name'],
        ];
        
        file_put_contents(
            storage_path('app/.installed'),
            json_encode($markerData, JSON_PRETTY_PRINT)
        );
    }
}
