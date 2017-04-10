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
use system\core\Controller;
use system\core\Assets;
class Main extends Controller
{

    public function index()
    {
        echo $this->view->render('main/index');
    }
    public function login(){
        $asset = ["login" => "css/login.css"];// login css
        Assets::add($asset);//add css to assets
        echo $this->view->useTemplate("login")->render('main/login');
    }

}
