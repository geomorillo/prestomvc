<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */
namespace app\controllers;
use system\core\Controller;
use system\core\View;
class UsuariosController extends Controller{

    public function index()
    {
        $userModel = new \app\models\UserModel();

        $users = $userModel->getUsers();
        
        $view = new View("usuarios", array("users" => $users));
        echo $view->render();
    }
    
    public function test()
    {
        echo "Esto es una prueba";
    }

}
