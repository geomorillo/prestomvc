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

Abstract Class Controller
{

    protected $model, $view;

    function __construct()
    {
        
    }

    abstract function index();
}
