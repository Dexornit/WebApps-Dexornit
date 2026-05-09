<?php

function checkServerRequirements(): array {
    $requirements = [];

    // PHP version
    $requirements['PHP >= 8.3'] = version_compare(PHP_VERSION, '8.3.0', '>=');

    // PHP extensions
    $extensions = [
        'OpenSSL'   => 'openssl',
        'PDO'       => 'pdo',
        'Mbstring'  => 'mbstring',
        'Tokenizer' => 'tokenizer',
        'XML'       => 'xml',
        'Ctype'     => 'ctype',
        'JSON'      => 'json',
        'BCMath'    => 'bcmath',
        'Fileinfo'  => 'fileinfo',
    ];

    foreach ($extensions as $label => $ext) {
        $requirements["PHP Extension: $label"] = extension_loaded($ext);
    }

    // Folder permissions
    $requirements['storage/ writable'] = is_writable(__DIR__ . '/../storage');
    $requirements['bootstrap/cache/ writable'] = is_writable(__DIR__ . '/../bootstrap/cache');

    return $requirements;
}

$requirements = checkServerRequirements();
$passed = true;
foreach ($requirements as $requirement => $value) {
    if ($value == false) {
        $passed = false;
    }
}
if ($passed) {
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wanseven Installer - Requirements Check</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <main class="py-12">
        <div class="max-w-lg mx-auto px-6">
            <div class="bg-white border border-gray-200 overflow-hidden rounded-2xl shadow-lg">
                <div class="pt-12 pb-6">
                    <h1 class="text-center text-3xl font-bold text-gray-800">Wanseven</h1>
                    <p class="text-center text-gray-600 mt-2">Installation Requirements</p>
                </div>
                <div class="p-10 flex flex-col gap-5">
                    <p class="text-center text-xl font-semibold text-gray-700">Server Requirements</p>
                    <div class="space-y-2">
                        <?php foreach ($requirements as $requirement => $value) { ?>
                            <div class="flex items-center justify-between py-3 px-4 rounded-lg hover:bg-gray-50 border border-gray-100">
                                <div class="text-gray-700"><?php echo htmlspecialchars($requirement) ?></div>
                                <?php if ($value) { ?>
                                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                <?php } else { ?>
                                    <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    
                    <?php if (!$passed) { ?>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mt-4">
                            <p class="text-red-800 text-sm">
                                <strong>⚠️ Requirements not met!</strong><br>
                                Please fix the issues above before proceeding with installation.
                            </p>
                        </div>
                    <?php } ?>
                    
                    <button onclick="location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-3 cursor-pointer font-semibold transition">
                        Recheck Requirements
                    </button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
<?php
exit();
?>
