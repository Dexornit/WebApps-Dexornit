<?php
/**
 * Dexornit Store — EZ Setup (All-in-One Installer)
 * Standalone PHP — tidak butuh Laravel/Composer untuk jalan
 * Buka: yourdomain.com/setup.php
 */

ini_set('display_errors', 0);
error_reporting(E_ALL);
set_time_limit(300);

$ROOT = dirname(__DIR__);

// ─── Sudah terinstall? ──────────────────────────────────────────────────────
if (file_exists($ROOT . '/storage/app/.installed')) {
    die('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Already Installed</title><style>body{font-family:sans-serif;background:#f0fdf4;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}.box{background:#fff;border:2px solid #16a34a;border-radius:12px;padding:2rem 3rem;text-align:center}h2{color:#16a34a}a{color:#16a34a;font-weight:bold}</style></head><body><div class="box"><h2>✅ Sudah Terinstall!</h2><p>Aplikasi sudah diinstall sebelumnya.</p><p><a href="/">Buka Website →</a></p><hr><p style="font-size:.8rem;color:#888">Untuk install ulang, hapus file <code>storage/app/.installed</code></p></div></body></html>');
}

// ─── Helper functions ───────────────────────────────────────────────────────
function rootPath(string $path = ''): string {
    return dirname(__DIR__) . ($path ? '/' . ltrim($path, '/') : '');
}

function ensureDirs(): void {
    $dirs = [
        rootPath('storage/app'),
        rootPath('storage/app/public'),
        rootPath('storage/app/public/logos'),
        rootPath('storage/app/public/products'),
        rootPath('storage/framework/cache/data'),
        rootPath('storage/framework/sessions'),
        rootPath('storage/framework/views'),
        rootPath('storage/logs'),
        rootPath('bootstrap/cache'),
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
    }
}

function generateKey(): string {
    return 'base64:' . base64_encode(random_bytes(32));
}

function testDbConnection(string $host, string $port, string $db, string $user, string $pass): void {
    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}

function writeEnv(array $d): void {
    $key  = generateKey();
    $name = $d['app_name'];
    $url  = rtrim($d['app_url'], '/');
    $content = <<<ENV
APP_NAME="{$name}"
APP_ENV=production
APP_KEY={$key}
APP_DEBUG=false
APP_URL={$url}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file
BCRYPT_ROUNDS=10

LOG_CHANNEL=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST={$d['db_host']}
DB_PORT={$d['db_port']}
DB_DATABASE={$d['db_name']}
DB_USERNAME={$d['db_user']}
DB_PASSWORD={$d['db_pass']}

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
MAIL_FROM_NAME="{$name}"

VITE_APP_NAME="{$name}"
ENV;
    file_put_contents(rootPath('.env'), $content);
}

function clearBootstrapCache(): void {
    foreach (['config.php','routes-v7.php','services.php','packages.php','events.php'] as $f) {
        $p = rootPath("bootstrap/cache/$f");
        if (file_exists($p)) @unlink($p);
    }
}

function runMigrations(array $db): array {
    $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $logs = [];

    // Baca semua migration files
    $migrationPath = rootPath('database/migrations');
    $files = glob($migrationPath . '/*.php');
    sort($files);

    // Buat tabel migrations jika belum ada
    $pdo->exec("CREATE TABLE IF NOT EXISTS `migrations` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `migration` varchar(255) NOT NULL,
        `batch` int NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $ran = $pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
    $batch = (int)($pdo->query("SELECT MAX(batch) FROM migrations")->fetchColumn() ?: 0) + 1;

    foreach ($files as $file) {
        $name = basename($file, '.php');
        if (in_array($name, $ran)) {
            $logs[] = "⏭ Skipped (already ran): {$name}";
            continue;
        }

        // Load migration class & run
        require_once $file;
        // Laravel migrations are anonymous classes — we use artisan for that
        // Instead, we flag them and let Laravel do it after .env is written
        $logs[] = "📄 Queued: {$name}";
    }

    return $logs;
}

function createAdminUser(array $db, array $admin): void {
    $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $hash = password_hash($admin['password'], PASSWORD_BCRYPT, ['cost' => 10]);
    $now  = date('Y-m-d H:i:s');

    $existing = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $existing->execute([$admin['email']]);

    if ($existing->fetchColumn()) {
        $stmt = $pdo->prepare("UPDATE users SET name=?, password=?, updated_at=? WHERE email=?");
        $stmt->execute([$admin['name'], $hash, $now, $admin['email']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?,?,?,?,?)");
        $stmt->execute([$admin['name'], $admin['email'], $hash, $now, $now]);
    }
}

// ─── Boot Laravel untuk migrate (setelah .env ditulis) ─────────────────────
function bootLaravelAndMigrate(): array {
    $logs = [];
    try {
        if (!class_exists('Illuminate\\Foundation\\Application')) {
            require rootPath('vendor/autoload.php');
        }
        $app    = require rootPath('bootstrap/app.php');
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        Illuminate\Support\Facades\DB::connection()->getPdo();
        $logs[] = '✓ Database terhubung';

        $kernel->call('migrate', ['--force' => true]);
        $logs[] = '✓ Migrasi selesai';

        try {
            $kernel->call('db:seed', ['--force' => true]);
            $logs[] = '✓ Data awal di-seed';
        } catch (\Exception $e) {
            $logs[] = '⚠ Seeding dilewati (opsional)';
        }

        // Storage symlink
        if (!file_exists(rootPath('public/storage'))) {
            try {
                $kernel->call('storage:link');
                $logs[] = '✓ Storage symlink dibuat';
            } catch (\Exception $e) {
                @symlink(rootPath('storage/app/public'), rootPath('public/storage'));
                $logs[] = '✓ Storage symlink (manual)';
            }
        }

    } catch (\Exception $e) {
        $logs[] = '✗ ERROR: ' . $e->getMessage();
        throw $e;
    }
    return $logs;
}

// ─── Process POST ───────────────────────────────────────────────────────────
$error  = null;
$logs   = [];
$done   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appName  = trim($_POST['app_name'] ?? 'Dexornit Store');
    $appUrl   = trim($_POST['app_url'] ?? '');
    $dbHost   = trim($_POST['db_host'] ?? 'localhost');
    $dbPort   = trim($_POST['db_port'] ?? '3306');
    $dbName   = trim($_POST['db_name'] ?? '');
    $dbUser   = trim($_POST['db_user'] ?? '');
    $dbPass   = $_POST['db_pass'] ?? '';
    $admName  = trim($_POST['admin_name'] ?? '');
    $admEmail = trim($_POST['admin_email'] ?? '');
    $admPass  = $_POST['admin_password'] ?? '';
    $admPass2 = $_POST['admin_password_confirm'] ?? '';

    // Validasi sederhana
    if (!$appUrl || !$dbName || !$dbUser || !$admName || !$admEmail || !$admPass) {
        $error = 'Semua field wajib diisi.';
    } elseif ($admPass !== $admPass2) {
        $error = 'Password admin tidak cocok.';
    } elseif (strlen($admPass) < 8) {
        $error = 'Password admin minimal 8 karakter.';
    } else {
        try {
            $logs[] = '→ Membuat direktori storage...';
            ensureDirs();
            $logs[] = '✓ Direktori storage siap';

            $logs[] = '→ Testing koneksi database...';
            testDbConnection($dbHost, $dbPort, $dbName, $dbUser, $dbPass);
            $logs[] = '✓ Koneksi database berhasil';

            $logs[] = '→ Menulis file .env...';
            writeEnv([
                'app_name' => $appName, 'app_url' => $appUrl,
                'db_host'  => $dbHost,  'db_port' => $dbPort,
                'db_name'  => $dbName,  'db_user' => $dbUser, 'db_pass' => $dbPass,
            ]);
            $logs[] = '✓ File .env berhasil ditulis';

            $logs[] = '→ Membersihkan bootstrap cache...';
            clearBootstrapCache();
            $logs[] = '✓ Bootstrap cache bersih';

            $logs[] = '→ Menjalankan migrasi database...';
            $migLogs = bootLaravelAndMigrate();
            $logs = array_merge($logs, $migLogs);

            $logs[] = '→ Membuat akun admin...';
            createAdminUser(
                ['host' => $dbHost, 'port' => $dbPort, 'name' => $dbName, 'user' => $dbUser, 'pass' => $dbPass],
                ['name' => $admName, 'email' => $admEmail, 'password' => $admPass]
            );
            $logs[] = '✓ Akun admin dibuat: ' . htmlspecialchars($admEmail);

            $logs[] = '→ Menandai instalasi selesai...';
            file_put_contents(rootPath('storage/app/.installed'), json_encode([
                'installed_at' => date('Y-m-d H:i:s'),
                'version'      => '1.0.0',
                'php_version'  => PHP_VERSION,
                'app_name'     => $appName,
            ], JSON_PRETTY_PRINT));
            $logs[] = '✓ Instalasi selesai!';

            $done = true;

        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// ─── Cek requirements sebelum tampil form ──────────────────────────────────
$reqs   = [
    'PHP >= 8.1'               => version_compare(PHP_VERSION, '8.1.0', '>='),
    'Extension: pdo_mysql'     => extension_loaded('pdo_mysql'),
    'Extension: mbstring'      => extension_loaded('mbstring'),
    'Extension: openssl'       => extension_loaded('openssl'),
    'Extension: fileinfo'      => extension_loaded('fileinfo'),
    'vendor/ folder'           => file_exists(rootPath('vendor/autoload.php')),
    'storage/ writable'        => is_writable(rootPath('storage')),
    'bootstrap/cache/ writable'=> is_writable(rootPath('bootstrap/cache')),
];
$allOk  = !in_array(false, $reqs, true);

$autoUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
         . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup — Dexornit Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --cream: #FFFBF1; --red: #EB4C4C; --green: #CADCAE;
            --yellow: #FCF596; --blue: #80C4E9; --dark: #1a1a1a;
        }
        body { font-family: 'Inter', sans-serif; background: var(--cream); min-height: 100vh; padding: 2rem 1rem; }
        .wrap { max-width: 580px; margin: 0 auto; }

        /* Header */
        .header { text-align: center; margin-bottom: 2rem; }
        .header h1 { font-size: 2rem; font-weight: 800; color: var(--dark); }
        .header p  { color: #555; margin-top: .35rem; }

        /* Card */
        .card {
            background: #fff;
            border: 2.5px solid var(--dark);
            border-radius: 16px;
            box-shadow: 5px 5px 0 var(--dark);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .card-head {
            padding: 1rem 1.5rem;
            border-bottom: 2px solid var(--dark);
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .card-head.red    { background: var(--red);    color: #fff; }
        .card-head.green  { background: var(--green);  color: var(--dark); }
        .card-head.yellow { background: var(--yellow); color: var(--dark); }
        .card-head.blue   { background: var(--blue);   color: var(--dark); }
        .card-head.dark   { background: var(--dark);   color: #fff; }
        .card-body { padding: 1.5rem; }

        /* Requirements */
        .req-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: .5rem .625rem; border-radius: 8px; margin-bottom: .4rem;
            font-size: .875rem; font-weight: 500;
        }
        .req-row.ok  { background: #dcfce7; color: #166534; }
        .req-row.err { background: #fee2e2; color: #991b1b; }
        .badge { padding: .1rem .6rem; border-radius: 999px; font-size: .75rem; font-weight: 700; }
        .badge.ok  { background: #bbf7d0; }
        .badge.err { background: #fecaca; }

        /* Form */
        .form-group { margin-bottom: 1.1rem; }
        label { display: block; font-size: .85rem; font-weight: 600; color: #333; margin-bottom: .4rem; }
        input[type=text], input[type=url], input[type=email], input[type=password] {
            width: 100%; padding: .65rem .9rem;
            border: 2px solid #ddd; border-radius: 10px;
            font-family: 'Inter', sans-serif; font-size: .9rem;
            outline: none; transition: border-color .15s;
        }
        input:focus { border-color: var(--dark); }
        .row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        small { font-size: .78rem; color: #888; margin-top: .25rem; display: block; }

        /* Alert */
        .alert {
            padding: .875rem 1rem; border-radius: 10px; margin-bottom: 1rem;
            font-size: .875rem; font-weight: 500;
        }
        .alert.err { background: #fee2e2; border: 1.5px solid #fca5a5; color: #991b1b; }

        /* Log */
        .log-box {
            background: #0f172a; color: #4ade80; font-family: monospace;
            font-size: .8rem; padding: 1rem; border-radius: 10px;
            max-height: 240px; overflow-y: auto; line-height: 1.8;
        }

        /* Button */
        .btn {
            display: block; width: 100%; padding: .875rem;
            background: var(--dark); color: #fff;
            border: 2.5px solid var(--dark); border-radius: 12px;
            font-family: 'Inter', sans-serif; font-size: 1rem; font-weight: 700;
            cursor: pointer; transition: opacity .15s; margin-top: 1.25rem;
        }
        .btn:hover   { opacity: .85; }
        .btn:disabled{ opacity: .5; cursor: not-allowed; }
        .btn.green-btn { background: #16a34a; border-color: #16a34a; }

        /* Success */
        .success-box { text-align: center; padding: 1rem; }
        .success-box h2 { font-size: 1.5rem; font-weight: 800; color: #16a34a; margin-bottom: .5rem; }
        .cred-box {
            background: var(--yellow); border: 2px solid var(--dark); border-radius: 10px;
            padding: 1rem; margin: 1rem 0; text-align: left; font-size: .875rem;
        }
        .cred-box strong { display: block; margin-bottom: .5rem; font-size: 1rem; }
        .open-btn {
            display: inline-block; padding: .75rem 2rem;
            background: var(--dark); color: #fff; border-radius: 12px;
            font-weight: 700; text-decoration: none; font-size: 1rem;
        }
        .open-btn:hover { opacity: .8; }

        @media(max-width: 480px) { .row2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>🛒 Dexornit Store</h1>
        <p>Easy Setup — isi form di bawah, selesai!</p>
    </div>

    <?php if ($done): ?>
    <!-- ═══════════════ SUKSES ═══════════════ -->
    <div class="card">
        <div class="card-head green">✅ Instalasi Berhasil!</div>
        <div class="card-body">
            <div class="success-box">
                <h2>🎉 Selesai!</h2>
                <p>Aplikasi berhasil diinstall dan siap digunakan.</p>
                <div class="cred-box">
                    <strong>📝 Informasi Login Admin:</strong>
                    Email: <strong><?= htmlspecialchars($_POST['admin_email'] ?? '') ?></strong><br>
                    Password: <em>(password yang kamu masukkan tadi)</em>
                </div>
                <a href="/" class="open-btn">🚀 Buka Website →</a>
                <p style="margin-top:1rem;font-size:.8rem;color:#888">
                    File <code>setup.php</code> bisa dihapus untuk keamanan.
                </p>
            </div>
            <?php if ($logs): ?>
            <div class="log-box" style="margin-top:1rem">
                <?php foreach ($logs as $l) echo htmlspecialchars($l) . "\n"; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>

    <!-- ═══════════════ REQUIREMENTS ═══════════════ -->
    <div class="card">
        <div class="card-head <?= $allOk ? 'green' : 'red' ?>">
            <?= $allOk ? '✅' : '⚠️' ?> Server Requirements
        </div>
        <div class="card-body">
            <?php foreach ($reqs as $label => $ok): ?>
            <div class="req-row <?= $ok ? 'ok' : 'err' ?>">
                <span><?= htmlspecialchars($label) ?></span>
                <span class="badge <?= $ok ? 'ok' : 'err' ?>"><?= $ok ? '✓ OK' : '✗ Missing' ?></span>
            </div>
            <?php endforeach; ?>
            <?php if (!$allOk): ?>
            <p style="font-size:.8rem;color:#991b1b;margin-top:.75rem">
                ⚠ Pastikan semua requirements terpenuhi sebelum install.<br>
                cPanel → <strong>Select PHP Version</strong> → aktifkan extension yang missing.
            </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ═══════════════ FORM INSTALLER ═══════════════ -->
    <form method="POST" onsubmit="handleSubmit(this)">

        <?php if ($error): ?>
        <div class="alert err">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($logs): ?>
        <div class="log-box" style="margin-bottom:1rem">
            <?php foreach ($logs as $l) echo htmlspecialchars($l) . "\n"; ?>
        </div>
        <?php endif; ?>

        <!-- App Settings -->
        <div class="card">
            <div class="card-head blue">🌐 Pengaturan Aplikasi</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Aplikasi</label>
                    <input type="text" name="app_name" value="<?= htmlspecialchars($_POST['app_name'] ?? 'Dexornit Store') ?>" required>
                </div>
                <div class="form-group">
                    <label>URL Aplikasi</label>
                    <input type="url" name="app_url" value="<?= htmlspecialchars($_POST['app_url'] ?? $autoUrl) ?>" required placeholder="https://yourdomain.com">
                    <small>Tanpa trailing slash. Contoh: https://wanseven.com</small>
                </div>
            </div>
        </div>

        <!-- Database -->
        <div class="card">
            <div class="card-head yellow">🗄️ Konfigurasi Database (MySQL)</div>
            <div class="card-body">
                <p style="font-size:.82rem;background:#FFF9C4;border:1.5px solid #F0C040;border-radius:8px;padding:.6rem .9rem;margin-bottom:1rem;color:#7a6000">
                    💡 Buat database MySQL dulu di <strong>cPanel → MySQL Databases</strong> sebelum mengisi form ini.
                </p>
                <div class="row2">
                    <div class="form-group">
                        <label>Host</label>
                        <input type="text" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Port</label>
                        <input type="text" name="db_port" value="<?= htmlspecialchars($_POST['db_port'] ?? '3306') ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nama Database</label>
                    <input type="text" name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? '') ?>" required placeholder="nama_database">
                </div>
                <div class="row2">
                    <div class="form-group">
                        <label>Username DB</label>
                        <input type="text" name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Password DB</label>
                        <input type="password" name="db_pass" value="">
                        <small>Kosongkan jika tidak ada password</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin -->
        <div class="card">
            <div class="card-head dark">👤 Akun Admin</div>
            <div class="card-body">
                <div class="form-group">
                    <label>Nama Admin</label>
                    <input type="text" name="admin_name" value="<?= htmlspecialchars($_POST['admin_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Admin</label>
                    <input type="email" name="admin_email" value="<?= htmlspecialchars($_POST['admin_email'] ?? '') ?>" required>
                </div>
                <div class="row2">
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="admin_password" required minlength="8">
                        <small>Minimal 8 karakter</small>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="admin_password_confirm" required minlength="8">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn" id="submitBtn" <?= !$allOk ? 'disabled title="Penuhi requirements dulu"' : '' ?>>
            🚀 Install Sekarang
        </button>
        <p style="text-align:center;font-size:.78rem;color:#888;margin-top:.75rem">
            Proses install membutuhkan 30–60 detik. Jangan tutup halaman ini.
        </p>
    </form>

    <?php endif; ?>
</div>

<script>
function handleSubmit(form) {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = '⏳ Sedang menginstall... Harap tunggu';
    return true;
}
</script>
</body>
</html>
