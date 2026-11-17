<?php

// Filesystem Vercel read-only (kecuali /tmp). Semua path tulis Laravel diarahkan ke /tmp.
$tmp = '/tmp';
foreach ([
    'storage/app',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
] as $dir) {
    if (!is_dir("$tmp/$dir")) {
        mkdir("$tmp/$dir", 0777, true);
    }
}

$paths = [
    'APP_SERVICES_CACHE'   => "$tmp/services.php",
    'APP_PACKAGES_CACHE'   => "$tmp/packages.php",
    'APP_CONFIG_CACHE'     => "$tmp/config.php",
    'APP_ROUTES_CACHE'     => "$tmp/routes.php",
    'APP_EVENTS_CACHE'     => "$tmp/events.php",
    'VIEW_COMPILED_PATH'   => "$tmp/storage/framework/views",
    'LARAVEL_STORAGE_PATH' => "$tmp/storage",
];

// Log ke stderr supaya muncul di Logs dashboard Vercel (kalau belum diatur di env).
if (getenv('LOG_CHANNEL') === false) {
    $paths['LOG_CHANNEL'] = 'stderr';
}

foreach ($paths as $key => $value) {
    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

require __DIR__ . '/../public/index.php';
