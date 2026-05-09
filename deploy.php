<?php
/**
 * Wanseven Auto Deployer
 * This script will install Composer dependencies automatically
 */

set_time_limit(300); // 5 minutes timeout
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuration
$baseDir = __DIR__;
$composerPath = $baseDir . '/composer.phar';
$vendorDir = $baseDir . '/vendor';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wanseven Deployer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .log-box {
            background: #1e1e1e;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            padding: 20px;
            border-radius: 8px;
            max-height: 400px;
            overflow-y: auto;
        }
        .log-line { margin: 2px 0; }
        .log-success { color: #00ff00; }
        .log-error { color: #ff0000; }
        .log-info { color: #00aaff; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">🚀 Wanseven Deployer</h1>
            <p class="text-gray-600 mb-6">Automatic Composer Installation</p>
            
            <div class="log-box mb-6" id="logBox">
                <div class="log-line log-info">→ Starting deployment process...</div>
            </div>
            
            <div id="actions" class="flex gap-4">
                <button onclick="startDeploy()" id="deployBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                    Start Deployment
                </button>
                <button onclick="location.href='/'" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold">
                    Go to Homepage
                </button>
            </div>
        </div>
    </div>

    <script>
        function log(message, type = 'info') {
            const logBox = document.getElementById('logBox');
            const line = document.createElement('div');
            line.className = 'log-line log-' + type;
            line.textContent = message;
            logBox.appendChild(line);
            logBox.scrollTop = logBox.scrollHeight;
        }

        async function startDeploy() {
            const btn = document.getElementById('deployBtn');
            btn.disabled = true;
            btn.textContent = 'Deploying...';
            
            try {
                const response = await fetch('?action=deploy');
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                
                while (true) {
                    const {done, value} = await reader.read();
                    if (done) break;
                    
                    const text = decoder.decode(value);
                    const lines = text.split('\n');
                    
                    lines.forEach(line => {
                        if (line.trim()) {
                            const parts = line.split('|');
                            if (parts.length === 2) {
                                log(parts[1], parts[0]);
                            } else {
                                log(line, 'info');
                            }
                        }
                    });
                }
                
                btn.textContent = 'Deployment Complete!';
                btn.className = 'bg-green-600 text-white px-6 py-3 rounded-lg font-semibold';
                
            } catch (error) {
                log('Error: ' + error.message, 'error');
                btn.disabled = false;
                btn.textContent = 'Retry Deployment';
            }
        }
    </script>
</body>
</html>

<?php

if (isset($_GET['action']) && $_GET['action'] === 'deploy') {
    // Set headers for streaming
    header('Content-Type: text/plain');
    header('X-Accel-Buffering: no');
    ob_implicit_flush(true);
    ob_end_flush();
    
    function streamLog($message, $type = 'info') {
        echo "$type|$message\n";
        flush();
    }
    
    try {
        streamLog('Starting deployment...', 'info');
        
        // Step 1: Check if vendor already exists
        if (is_dir($vendorDir) && file_exists($vendorDir . '/autoload.php')) {
            streamLog('✓ Vendor directory already exists!', 'success');
            streamLog('→ Skipping composer install', 'info');
        } else {
            // Step 2: Download Composer if not exists
            if (!file_exists($composerPath)) {
                streamLog('→ Downloading Composer...', 'info');
                
                $composerSetup = file_get_contents('https://getcomposer.org/installer');
                if ($composerSetup === false) {
                    throw new Exception('Failed to download Composer installer');
                }
                
                file_put_contents($baseDir . '/composer-setup.php', $composerSetup);
                
                // Run composer setup
                ob_start();
                include $baseDir . '/composer-setup.php';
                ob_end_clean();
                
                unlink($baseDir . '/composer-setup.php');
                
                if (!file_exists($composerPath)) {
                    throw new Exception('Composer installation failed');
                }
                
                streamLog('✓ Composer downloaded successfully', 'success');
            } else {
                streamLog('✓ Composer already exists', 'success');
            }
            
            // Step 3: Run composer install
            streamLog('→ Running composer install (this may take a few minutes)...', 'info');
            
            $command = "php " . escapeshellarg($composerPath) . " install --no-dev --optimize-autoloader --no-interaction 2>&1";
            
            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ];
            
            $process = proc_open($command, $descriptors, $pipes, $baseDir);
            
            if (is_resource($process)) {
                fclose($pipes[0]);
                
                while ($line = fgets($pipes[1])) {
                    streamLog('  ' . trim($line), 'info');
                }
                
                fclose($pipes[1]);
                fclose($pipes[2]);
                
                $returnCode = proc_close($process);
                
                if ($returnCode === 0) {
                    streamLog('✓ Composer install completed successfully!', 'success');
                } else {
                    throw new Exception('Composer install failed with code: ' . $returnCode);
                }
            } else {
                throw new Exception('Failed to execute composer command');
            }
        }
        
        // Step 4: Set permissions
        streamLog('→ Setting permissions...', 'info');
        @chmod($baseDir . '/storage', 0775);
        @chmod($baseDir . '/bootstrap/cache', 0775);
        streamLog('✓ Permissions set', 'success');
        
        // Step 5: Check .env
        if (!file_exists($baseDir . '/.env')) {
            streamLog('→ Creating .env file...', 'info');
            if (file_exists($baseDir . '/.env.example')) {
                copy($baseDir . '/.env.example', $baseDir . '/.env');
                streamLog('✓ .env file created from .env.example', 'success');
            }
        } else {
            streamLog('✓ .env file already exists', 'success');
        }
        
        // Step 6: Generate APP_KEY if needed
        $envContent = file_get_contents($baseDir . '/.env');
        if (strpos($envContent, 'APP_KEY=') !== false && strpos($envContent, 'APP_KEY=base64:') === false) {
            streamLog('→ Generating APP_KEY...', 'info');
            $key = 'base64:' . base64_encode(random_bytes(32));
            $envContent = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $envContent);
            file_put_contents($baseDir . '/.env', $envContent);
            streamLog('✓ APP_KEY generated', 'success');
        } else {
            streamLog('✓ APP_KEY already set', 'success');
        }
        
        streamLog('', 'info');
        streamLog('═══════════════════════════════════════', 'success');
        streamLog('✓ DEPLOYMENT COMPLETED SUCCESSFULLY!', 'success');
        streamLog('═══════════════════════════════════════', 'success');
        streamLog('', 'info');
        streamLog('→ You can now access your application', 'info');
        streamLog('→ Go to: ' . ($_SERVER['HTTP_HOST'] ?? 'your-domain.com'), 'info');
        
    } catch (Exception $e) {
        streamLog('', 'error');
        streamLog('✗ DEPLOYMENT FAILED!', 'error');
        streamLog('Error: ' . $e->getMessage(), 'error');
        streamLog('', 'error');
        streamLog('Please check the error above and try again', 'error');
    }
    
    exit;
}
?>
