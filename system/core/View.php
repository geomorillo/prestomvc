<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;

/**
 * Description of View
 *
 * @author geomorillo
 */
use system\core\LogException;

class View
{

    protected static $data = array();
    protected static $path;

    function __construct()
    {
        
    }

    public static function render($path, array $data = array())
    {
        self::$path = $path;
        self::$data = $data;
        ob_start();
        extract(self::$data);
        try {
            $header = TEMPLATE_PATH . "default" . DS . "header.php";
            $footer = TEMPLATE_PATH . "default" . DS . "footer.php";
            $viewPath = VIEW_PATH . self::$path . ".php";
            $useTemplate = false;
            if (file_exists($header) && file_exists($footer)) {
                $useTemplate = true;
            }
            if ($useTemplate) {
                include $header;
            }
            include $viewPath;
            if ($useTemplate) {
                include $footer;
            }
        } catch (LogException $le) {
            ob_end_clean();
            throw $le->logError();
        }
        return ob_get_clean();
    }

    function __toString()
    {
        return self::render();
    }

}
