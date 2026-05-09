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
            
            // Step 3: Clear config cache
            Artisan::call('config:clear');
            
            // Step 4: Test database connection
            DB::connection()->getPdo();
            
            // Step 5: Run migrations
            Artisan::call('migrate', ['--force' => true]);
            
            // Step 6: Create admin user
            User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
            ]);
            
            // Step 7: Create installation marker
            $this->createInstallationMarker($validated);
            
            return redirect('/login')->with('success', 'Installation completed successfully! Please login with your admin credentials.');
            
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Installation failed: ' . $e->getMessage()]);
        }
    }
    
    private function checkRequirements()
    {
        $requirements = [
            'php_version' => [
                'name' => 'PHP Version >= 8.3',
                'status' => version_compare(PHP_VERSION, '8.3.0', '>='),
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
    
    private function updateEnvFile($data)
    {
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            copy(base_path('.env.example'), $envPath);
        }
        
        $envContent = file_get_contents($envPath);
        
        // Update APP settings
        $envContent = $this->updateEnvValue($envContent, 'APP_NAME', $data['app_name']);
        $envContent = $this->updateEnvValue($envContent, 'APP_URL', $data['app_url']);
        
        // Update DB settings
        if ($data['db_type'] === 'sqlite') {
            $envContent = $this->updateEnvValue($envContent, 'DB_CONNECTION', 'sqlite');
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
        
        file_put_contents($envPath, $envContent);
    }
    
    private function updateEnvValue($envContent, $key, $value)
    {
        $oldValue = env($key);
        $pattern = "/^{$key}=.*/m";
        
        // Escape special characters in value
        $escapedValue = addslashes($value);
        
        // Add quotes if value contains spaces
        if (strpos($value, ' ') !== false) {
            $newLine = "{$key}=\"{$escapedValue}\"";
        } else {
            $newLine = "{$key}={$escapedValue}";
        }
        
        if (preg_match($pattern, $envContent)) {
            return preg_replace($pattern, $newLine, $envContent);
        } else {
            return $envContent . "\n{$newLine}";
        }
    }
    
    private function createInstallationMarker($data)
    {
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
