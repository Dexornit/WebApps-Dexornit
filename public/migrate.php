<?php
/**
 * Web-based Migration Runner
 * For users without SSH access
 */

// Security: Only allow if not migrated yet
if (file_exists(__DIR__ . '/../storage/app/.migrated')) {
    die('Migrations already completed! Delete storage/app/.migrated to re-run.');
}

// Check if vendor exists
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('Error: vendor/ folder not found! Upload it first.');
}

// Check if installed
if (!file_exists(__DIR__ . '/../storage/app/.installed')) {
    die('Error: Run /install.php first!');
}

set_time_limit(300); // 5 minutes
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Run Migrations</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-6">🔧 Database Migration</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_migration'])) {
            echo "<div class='bg-blue-50 border border-blue-200 p-4 rounded mb-4'>";
            echo "<p class='font-semibold'>⏳ Running migrations...</p>";
            echo "</div>";
            
            echo "<div class='bg-gray-900 text-green-400 p-4 rounded font-mono text-sm overflow-auto max-h-96 mb-4'>";
            
            try {
                // Change to root directory
                chdir(__DIR__ . '/..');
                
                // Run migrations
                echo "→ Running migrations...\n";
                flush();
                
                exec('php artisan migrate --force 2>&1', $output, $return);
                echo implode("\n", $output) . "\n\n";
                
                if ($return !== 0) {
                    throw new Exception("Migration failed with exit code: $return");
                }
                
                // Run seeders
                echo "→ Seeding database...\n";
                flush();
                
                exec('php artisan db:seed --force 2>&1', $output2, $return2);
                echo implode("\n", $output2) . "\n\n";
                
                if ($return2 !== 0) {
                    echo "⚠️ Warning: Seeding failed (this is optional)\n\n";
                }
                
                // Create migration marker
                file_put_contents(__DIR__ . '/../storage/app/.migrated', json_encode([
                    'migrated_at' => date('Y-m-d H:i:s'),
                    'method' => 'web'
                ]));
                
                echo "✅ Migration completed successfully!\n";
                echo "</div>";
                
                echo "<div class='bg-green-50 border border-green-200 p-4 rounded mb-4'>";
                echo "<p class='font-semibold text-green-700'>✅ Database setup complete!</p>";
                echo "<a href='/' class='inline-block mt-4 bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700'>Go to Website</a>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "\n❌ Error: " . $e->getMessage() . "\n";
                echo "</div>";
                
                echo "<div class='bg-red-50 border border-red-200 p-4 rounded'>";
                echo "<p class='font-semibold text-red-700'>❌ Migration failed!</p>";
                echo "<p class='text-sm text-red-600 mt-2'>Please run migrations via SSH instead:</p>";
                echo "<code class='block bg-gray-900 text-green-400 p-2 rounded mt-2'>php artisan migrate && php artisan db:seed</code>";
                echo "</div>";
            }
            
        } else {
            // Show form
            ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <h3 class="font-bold text-yellow-800 mb-2">⚠️ Important</h3>
                <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                    <li>This will create database tables and seed initial data</li>
                    <li>Make sure your database credentials in .env are correct</li>
                    <li>This process may take 1-2 minutes</li>
                </ul>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 p-4 rounded mb-6">
                <h3 class="font-bold text-blue-800 mb-2">📋 What will be created:</h3>
                <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                    <li>Users table (for authentication)</li>
                    <li>Categories table (for product categories)</li>
                    <li>Products table (for products)</li>
                    <li>Product variants and images tables</li>
                    <li>Admin user account (from installer data)</li>
                </ul>
            </div>
            
            <form method="POST" onsubmit="return confirm('Ready to run migrations?');">
                <button type="submit" name="run_migration" value="1" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">
                    🚀 Run Migrations Now
                </button>
            </form>
            
            <div class="mt-4 text-center">
                <a href="/check.php" class="text-blue-600 hover:underline text-sm">← Back to Status Check</a>
            </div>
            
            <div class="mt-6 p-4 bg-gray-50 rounded border text-sm">
                <p class="font-semibold mb-2">💡 Alternative (SSH):</p>
                <p class="text-gray-600 mb-2">If you have SSH access, you can run:</p>
                <code class="block bg-gray-900 text-green-400 p-2 rounded">php artisan migrate && php artisan db:seed</code>
            </div>
            <?php
        }
        ?>
    </div>
</body>
</html>
