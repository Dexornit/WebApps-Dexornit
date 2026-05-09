<?php
// Clear cache via browser for shared hosting
// Access: http://wanseven.com/clear-cache.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Clear all caches
$kernel->call('config:clear');
$kernel->call('cache:clear');
$kernel->call('route:clear');
$kernel->call('view:clear');

echo "✅ Cache cleared successfully!<br>";
echo "Now try accessing: <a href='/install'>Go to Installer</a>";
