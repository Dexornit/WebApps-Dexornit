<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$envExample = __DIR__ . '/.env.example';
$envFile = __DIR__ . '/.env';

if (file_exists($envFile)) {
    die("✓ .env file already exists!<br><a href='/install'>Go to Installer</a>");
}

if (!file_exists($envExample)) {
    die("❌ .env.example not found! Cannot create .env file.");
}

// Copy .env.example to .env
if (copy($envExample, $envFile)) {
    echo "✓ .env file created successfully!<br><br>";
    
    // Generate APP_KEY
    $key = 'base64:' . base64_encode(random_bytes(32));
    
    // Read .env content
    $envContent = file_get_contents($envFile);
    
    // Replace APP_KEY
    $envContent = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $envContent);
    
    // Write back
    file_put_contents($envFile, $envContent);
    
    echo "✓ APP_KEY generated: $key<br><br>";
    echo "<strong>Next steps:</strong><br>";
    echo "1. <a href='/install'>Go to Installer</a><br>";
    echo "2. Complete the installation form<br>";
} else {
    echo "❌ Failed to create .env file. Check permissions!";
}
