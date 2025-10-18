<?php

// Define test environment
define('TESTING', true);
define('ENABLE_DEBUG', true);

// Define test environment first
if (!defined('TESTING')) {
    define('TESTING', true);
}
if (!defined('ENABLE_DEBUG')) {
    define('ENABLE_DEBUG', true);
}

// Initialize basic constants
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('ROOT')) {
    define('ROOT', realpath(__DIR__ . '/../') . DS);
}
if (!defined('WEBROOT')) {
    define('WEBROOT', '/');
}

// Load essential parts for testing
require_once ROOT . 'system/core/functions.php';
require_once ROOT . 'system/config/constants.php';

// Load additional constants that might be needed
if (!defined('USE_SESSIONS')) {
    define('USE_SESSIONS', true); // Enable sessions for testing
}
if (!defined('MAX_USERNAME_LENGTH')) {
    define('MAX_USERNAME_LENGTH', 30);
}
if (!defined('MIN_USERNAME_LENGTH')) {
    define('MIN_USERNAME_LENGTH', 5);
}
if (!defined('MAX_PASSWORD_LENGTH')) {
    define('MAX_PASSWORD_LENGTH', 30);
}
if (!defined('MIN_PASSWORD_LENGTH')) {
    define('MIN_PASSWORD_LENGTH', 5);
}
if (!defined('MAX_EMAIL_LENGTH')) {
    define('MAX_EMAIL_LENGTH', 100);
}
if (!defined('MIN_EMAIL_LENGTH')) {
    define('MIN_EMAIL_LENGTH', 5);
}
if (!defined('RANDOM_KEY_LENGTH')) {
    define('RANDOM_KEY_LENGTH', 15);
}
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', ROOT . 'app' . DS . 'config' . DS);
}

// Define missing constants for testing
if (!defined('TEMPLATE_PATH')) {
    define('TEMPLATE_PATH', ROOT . 'app' . DS . 'templates' . DS);
}
if (!defined('LOG_PATH')) {
    define('LOG_PATH', ROOT . 'log' . DS);
}
if (!defined('LOG_FILENAME')) {
    define('LOG_FILENAME', 'debug.log');
}

// Load essential classes for testing
spl_autoload_register(function($class) {
    $class = explode("\\", $class);
    $className = array_pop($class);
    $path = implode(DS, $class);
    $fullPath = ROOT . $path . DS . $className . '.php';
    if (file_exists($fullPath)) {
        require_once($fullPath);
    }
});

// Load test base class
require_once __DIR__ . '/TestCase.php';

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