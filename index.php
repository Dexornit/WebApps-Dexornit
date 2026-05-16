<?php
/**
 * Root entry point untuk shared hosting.
 * Meneruskan semua request ke public/index.php (Laravel entry point).
 */

// Jika .env belum ada, redirect ke setup wizard
if (!file_exists(__DIR__ . '/public/.env') && !file_exists(__DIR__ . '/.env')) {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if (!str_contains($uri, 'setup.php') && !str_contains($uri, 'requirements.php')) {
        header('Location: /public/setup.php');
        exit;
    }
}

chdir(__DIR__ . '/public');
require __DIR__ . '/public/index.php';
