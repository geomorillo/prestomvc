<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

/**
 *
 * @author geomorillo
 */

namespace modules\dashboard\controllers;

use system\core\Controller;

class Dashboard extends Controller
{

    public function index()
    {
        echo $this->view->render("y");
    }

}
