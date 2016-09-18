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

    public function render($path, array $data = array())
    {
        $this->path = $path;
        $this->data = $data;
        ob_start();
        extract($this->data);
        try {
            $viewPath = APP_PATH . "views" . DS . $this->path . ".php";
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
