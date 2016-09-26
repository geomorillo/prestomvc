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
//use system\core\View;
use system\core\Controller;
use system\helpers\Auth\Auth;

class Main extends Controller
{

    private $auth;

    public function __construct()
    {
        $this->auth = new Auth();
    }

    public function index()
    {
        //echo L::translate("bienvenido",["title"=>"PrestoMvc","version"=>"1.0"]);
         if ($this->auth->isLogged()) {
            echo "logged";
            // "User is logged you could load a view here if you want";
        } else {
            echo "not logged";
            //user not authenticated load a login view with a form with login and password inputs
        }
    }
    
    public function login()
    {
        $username = "auser";
        $password = "12345";

        if ($this->auth->login($username, $password)) {
            //you can use a redirect or load a view or to index for example
            $this->index();
        } else {
            echo "not authenticated some error ocurred";
            // not authenticated you can redirect to a login view
        }
    }

}
