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

    protected $data = array();
    protected $path;

    function __construct()
    {
        
    }

    public static function render($path, array $data = array())
    {
        self::$path = $path;
         self::$data = $data;
        ob_start();
        extract( self::$data);
        try {
            $viewPath = APP_PATH . "views" . DS .  self::$path . ".php";
            include $viewPath;
        } catch (LogException $le) {
            ob_end_clean();
            throw $le->logError();
        }
        return ob_get_clean();
    }
    
    function __toString()
    {
        return $this->render();
    }

}
