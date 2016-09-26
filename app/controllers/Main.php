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

class Main extends Controller
{

    public function index()
    {
        echo View::render('Main/index');
    }

}
