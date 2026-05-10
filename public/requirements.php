<?php
/**
 * Dexornit Store — Requirements Checker
 * Persis pola TMail: jika semua OK → return (lanjut boot Laravel)
 * Jika ada yang gagal → tampilkan halaman error dan halt
 */

function checkDexornitRequirements(): array {
    $req = [];

    // PHP Version
    $req['PHP >= 8.1'] = version_compare(PHP_VERSION, '8.1.0', '>=');

    // PHP Extensions
    $extensions = [
        'OpenSSL'   => 'openssl',
        'PDO'       => 'pdo',
        'PDO MySQL' => 'pdo_mysql',
        'Mbstring'  => 'mbstring',
        'Tokenizer' => 'tokenizer',
        'XML'       => 'xml',
        'Ctype'     => 'ctype',
        'JSON'      => 'json',
        'BCMath'    => 'bcmath',
        'Fileinfo'  => 'fileinfo',
    ];
    foreach ($extensions as $label => $ext) {
        $req["Extension: $label"] = extension_loaded($ext);
    }

    // Folder permissions
    $req['storage/ writable']         = is_writable(__DIR__ . '/../storage');
    $req['bootstrap/cache/ writable'] = is_writable(__DIR__ . '/../bootstrap/cache');

    // Vendor exists
    $req['vendor/ folder exists'] = file_exists(__DIR__ . '/../vendor/autoload.php');

    return $req;
}

$requirements = checkDexornitRequirements();
$passed = !in_array(false, $requirements, true);

if ($passed) {
    return; // Semua OK → lanjut boot Laravel
}

// Ada yang gagal → tampilkan halaman requirements dan HALT
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Requirements — Dexornit Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #FFFBF1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card {
            background: #fff;
            border: 2.5px solid #1a1a1a;
            border-radius: 16px;
            box-shadow: 6px 6px 0 #1a1a1a;
            max-width: 520px;
            width: 100%;
            overflow: hidden;
        }
        .card-header {
            background: #EB4C4C;
            padding: 2rem;
            text-align: center;
            border-bottom: 2px solid #1a1a1a;
        }
        .card-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: .25rem;
        }
        .card-header p { color: rgba(255,255,255,.85); font-size: .875rem; }
        .card-body { padding: 1.5rem; }
        .req-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .625rem .75rem;
            border-radius: 8px;
            margin-bottom: .5rem;
            font-size: .875rem;
            font-weight: 500;
        }
        .req-item.ok  { background: #dcfce7; color: #166534; }
        .req-item.err { background: #fee2e2; color: #991b1b; }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .125rem .625rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
        }
        .badge.ok  { background: #bbf7d0; color: #166534; }
        .badge.err { background: #fecaca; color: #991b1b; }
        .btn-reload {
            display: block;
            width: 100%;
            margin-top: 1.25rem;
            padding: .75rem;
            background: #1a1a1a;
            color: #fff;
            border: 2px solid #1a1a1a;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: .9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }
        .btn-reload:hover { background: #333; }
        .hint {
            margin-top: 1rem;
            padding: .875rem;
            background: #FFF9C4;
            border: 1.5px solid #F0C040;
            border-radius: 10px;
            font-size: .8rem;
            color: #7a6000;
            line-height: 1.6;
        }
        .hint strong { display: block; margin-bottom: .25rem; }
    </style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <h1>⚠️ Server Requirements</h1>
        <p>Dexornit Store membutuhkan beberapa persyaratan server</p>
    </div>
    <div class="card-body">
        <?php foreach ($requirements as $label => $ok): ?>
        <div class="req-item <?= $ok ? 'ok' : 'err' ?>">
            <span><?= htmlspecialchars($label) ?></span>
            <span class="badge <?= $ok ? 'ok' : 'err' ?>">
                <?= $ok ? '✓ OK' : '✗ Missing' ?>
            </span>
        </div>
        <?php endforeach; ?>

        <button class="btn-reload" onclick="location.reload()">🔄 Recheck Requirements</button>

        <div class="hint">
            <strong>💡 Cara fix di cPanel RumahWeb:</strong>
            Masuk ke <em>cPanel → Select PHP Version</em>, aktifkan extension yang belum centang.
            Untuk permission: <em>File Manager → klik kanan folder storage/ → Change Permissions → 755</em>.
        </div>
    </div>
</div>
</body>
</html>
<?php
exit();
