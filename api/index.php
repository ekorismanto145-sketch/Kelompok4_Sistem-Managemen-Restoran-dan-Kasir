<?php

// Ensure writable /tmp directories exist on Vercel's read-only filesystem
foreach ([
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
] as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

require __DIR__.'/../public/index.php';
