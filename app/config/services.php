<?php

/**
 * Dependency Injection Services Configuration
 * Register services for dependency injection container
 */

use system\core\Route;

// Core system services
Route::registerService('system\database\Database', function() {
    return \system\database\Database::connect();
});

Route::registerService('system\helpers\Auth\Auth', function() {
    return new \system\helpers\Auth\Auth();
});

Route::registerService('system\http\Response', function() {
    return new \system\http\Response();
});

Route::registerService('system\http\Request', function() {
    return new \system\http\Request();
});

Route::registerService('system\core\Logger', function() {
    return new \system\core\Logger();
});

Route::registerService('system\core\Email', function() {
    return new \system\core\Email();
});

// Application services (add your custom services here)
// Example:
// Route::registerService('App\Services\UserService', function() {
//     $db = \system\database\Database::connect();
//     return new App\Services\UserService($db);
// });