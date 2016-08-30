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
class View
{

    protected $data = array();
    protected $path;

    function __construct($path, array $data = array())
    {
        $this->path = $path;
        $this->data = $data;
    }
    public function render()
    {
        ob_start();
        extract($this->data);
        try {
            include "app/views/". $this->path .".php";
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
        return ob_get_clean();
    }

}
