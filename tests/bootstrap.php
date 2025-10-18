<?php

// Define test environment
define('TESTING', true);
define('ENABLE_DEBUG', true);

// Load framework
require_once __DIR__ . '/../index.php';

// Additional test setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create test directories if needed
$testDirs = [
    ROOT . CACHE_DEFAULT_DIR,
    __DIR__ . '/reports'
];

foreach ($testDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Clean test cache before running tests
$cacheFiles = glob(ROOT . CACHE_DEFAULT_DIR . DS . 'test_*' . CACHE_FILE_EXTENSION);
foreach ($cacheFiles as $file) {
    unlink($file);
}