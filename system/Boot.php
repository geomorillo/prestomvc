<?php

namespace system;

use system\core\Router;
use system\http\SessionManager;
use system\core\Register;

class Boot
{

    public static function run()
    {
        self::init();
        self::autoload();
        self::dispatcher();
    }

    private static function init()
    {
       
        // Define path constants
        define("DS", DIRECTORY_SEPARATOR);
        define("ROOT", getcwd() . DS);
        define("APP_PATH", ROOT . 'app' . DS);
        define("SYSTEM_PATH", ROOT . "system" . DS);
        define("PUBLIC_PATH", ROOT . "public" . DS);
        define("CONFIG_PATH", APP_PATH . "config" . DS);
        define("CONTROLLER_PATH", APP_PATH . "controllers" . DS);
        define("MODEL_PATH", APP_PATH . "models" . DS);
        define("VIEW_PATH", APP_PATH . "views" . DS);
        define("TEMPLATE_PATH", APP_PATH . "templates" . DS);
        define("LANGUAGE_PATH", APP_PATH . "languages" . DS);
        define("ASSET_PATH", APP_PATH . "assets" . DS);
        define("CORE_PATH", SYSTEM_PATH . "core" . DS);
        define("HTTP_PATH", SYSTEM_PATH . "http" . DS);
        define('DB_PATH', SYSTEM_PATH . "database" . DS);
        define("LIB_PATH", SYSTEM_PATH . "libraries" . DS);
        define("HELPER_PATH", SYSTEM_PATH . "helpers" . DS);
        define("UPLOAD_PATH", PUBLIC_PATH . "uploads" . DS);
        define("LOG_PATH", ROOT . "log".DS);
        define("MODULES_PATH", ROOT . "modules" . DS);
        define("CONTROLLERS_NAMESPACE","app\\controllers\\");//Controllers directory do not change
         //load some general functions
        include_once CORE_PATH.'functions.php';
        define("WEBROOT", getWebroot());
        include_once APP_PATH . 'config/config.php';
    }

    // Autoloading
    private static function autoload()
    {
        spl_autoload_register(function($class) {
            $class = explode("\\", $class);
            $className = array_pop($class);
            $path = implode(DS, $class);
            if (file_exists($path . DS . $className . '.php')) {
                require_once($path . DS . $className . '.php');
            }
        });
    }

    private static function dispatcher()
    {
        // Set security headers
        self::setSecurityHeaders();

        Register::lib(); //register an included lib
        if (USE_SESSIONS) {
            $ses_handler = new SessionManager();
            session_set_save_handler($ses_handler, TRUE);
            register_shutdown_function('session_write_close');
            isset($_SESSION) || session_start();
        }
        //include some assets
        include CONFIG_PATH . "assets.php";
        include CONFIG_PATH . 'events.php';
        $router = new Router();
        // Include the routes
        include "app" . DS . "routes.php";
        Register::modules($router);
        $router->dispatch();
    }

    /**
     * Set basic security headers to protect against common attacks
     */
    private static function setSecurityHeaders()
    {
        // Prevent clickjacking attacks
        header('X-Frame-Options: SAMEORIGIN');

        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');

        // Enable XSS protection
        header('X-XSS-Protection: 1; mode=block');

        // Prevent referrer leakage for HTTPS sites
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }

        // Content Security Policy (basic)
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");

        // HSTS for HTTPS sites
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}
