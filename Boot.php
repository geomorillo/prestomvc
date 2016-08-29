<?php

/* function __autoload($class_name)
  //{
  //    require_once $class_name . '.php';
  //}
 */

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

        //define("ROOT", getcwd() . DS);
        define('ROOT', dirname(__FILE__));

        define("APP_PATH", ROOT . DS . 'app' . DS);

        define("SYSTEM_PATH", ROOT . DS . "system" . DS);

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


        // Define platform, controller, action, for example:
        // index.php?p=admin&c=Goods&a=add

        define("PLATFORM", isset($_REQUEST['p']) ? $_REQUEST['p'] : 'home');

        define("DEFAULTCONTROLLER", 'Usuarios');

        define("DEFAULTMETHOD", 'index');


        define("CURR_CONTROLLER_PATH", CONTROLLER_PATH);

        define("CURR_VIEW_PATH", VIEW_PATH . PLATFORM . DS);

        session_start();
    }

    // Autoloading

    private static function autoload()
    {
        // require_once("Autoloader.php");
        spl_autoload_register(array(__CLASS__, 'load'));
        // spl_autoload_register('Autoloader::loader');
    }

// Define a custom load method

    private static function load($className)
    {

        $className = strtolower($className);
        $className = end(explode("\\", $className));
        $paths = array(
            CORE_PATH,
            CONTROLLER_PATH,
            MODEL_PATH,
            HELPER_PATH,
            VIEW_PATH
        );

        // Buscamos en cada ruta los archivos
        foreach ($paths as $path) {
            $file = "$path$className.php";
            $exists = file_exists($file);
            if ($exists) {
                echo require_once $file;
                 if (!class_exists($className, false)) {
                    throw new RuntimeException('Class ' . $className . ' has not been loaded yet');
                }
                return TRUE;
               
            }
        }
        return FALSE;
    }

    // Routing and dispatching

    private static function dispatch()
    {

        $controller_name = DEFAULTCONTROLLER . "Controller";

        $action_name = DEFAULTMETHOD . "Action";

        $controller = new $controller_name;

        $controller->$action_name();
    }

}
