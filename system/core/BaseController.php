<?php
namespace system\core;
use system\core\Loader;

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */


class BaseController
{
    protected $loader;

    public function __construct()
    {

        $this->loader = new Loader();
    }

    public function redirect($url, $message, $wait = 0)
    {

        if ($wait == 0) {

            header("Location:$url");
        } else {

            include CURR_VIEW_PATH . "message.html";
        }
        exit;
    }

}
