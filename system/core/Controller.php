<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

/**
 * Description of Controller
 *
 * @author geomorillo
 */

namespace system\core;

abstract class Controller
{

    public $view;

    public function __construct()
    {
        $this->view = new View();
        $this->view->setCaller($this->get_namespace($this));
    }

    private function get_namespace($instance)
    {
        $namespace = get_class($instance);
        $namespace = explode("\\controllers", $namespace);
        $namespace = explode("\\", $namespace[0]);
        $namespace = implode(DS, $namespace);
        return  $namespace;
    }

    abstract function index();
}
