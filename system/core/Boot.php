<?php

namespace system\core;

class Boot
{

    public static function run()
    {
        self::init();
        self::autoload();
        self::dispatch();
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
        define("CORE_PATH", SYSTEM_PATH . "core" . DS);
        define('DB_PATH', SYSTEM_PATH . "database" . DS);
        define("LIB_PATH", SYSTEM_PATH . "libraries" . DS);
        define("HELPER_PATH", SYSTEM_PATH . "helpers" . DS);
        define("UPLOAD_PATH", PUBLIC_PATH . "uploads" . DS);
        define("DEFAULTCONTROLLER", 'UsuariosController');
        define("DEFAULTMETHOD", 'index');
        define("CURR_CONTROLLER_PATH", CONTROLLER_PATH);
        define("CURR_VIEW_PATH", VIEW_PATH . DS);
        session_start();
    }

    // Autoloading
    private static function autoload()
    {

        spl_autoload_register(function($class) {
            $class = explode("\\", $class);
            $class = end($class);
            $class = str_replace('\\', '/', $class);
            $paths = array(
                CORE_PATH,
                CONTROLLER_PATH,
                MODEL_PATH,
                HELPER_PATH,
                VIEW_PATH
            );
            foreach ($paths as $path) {

                if (file_exists($path . $class . '.php')) {
                    require_once($path . $class . '.php');
                }
            }
        });
    }

    // Routing and dispatching 
    private static function dispatch()
    {
        $controller_name = DEFAULTCONTROLLER;
        $action_name = DEFAULTMETHOD;
        $controller = new $controller_name;
        $controller->$action_name();
    }

}
