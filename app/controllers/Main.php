<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace app\controllers;

/**
 * Description of Main
 *
 * @author geomorillo
 */
use system\core\View;
use system\core\Controller;
use system\core\Language as L;

class Main extends Controller
{
    public function index()
    {
        echo L::translate("bienvenido",["title"=>"PrestoMvc","version"=>"1.0"]);
     
    }
}
