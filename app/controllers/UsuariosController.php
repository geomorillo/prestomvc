<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */
namespace app\controllers;
use \system\core\Controller;

class UsuariosController extends Controller{

    public function index()
    {
        $userModel = new \app\models\UserModel("user");

        $users = $userModel->getUsers();
        
        print_r($users);
    }

}
