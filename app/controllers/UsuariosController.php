<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */
namespace app\controllers;
<<<<<<< HEAD
use system\core\BaseController;
use app\models\UserModel;
=======
use system\core\Controller;
>>>>>>> upstream/master

class UsuariosController extends Controller{

    public function index()
    {
<<<<<<< HEAD
        // $userModel = new UserModel("user");
=======
        $userModel = new \app\models\UserModel("user");
>>>>>>> upstream/master

        // $users = $userModel->getUsers();
        
<<<<<<< HEAD
        echo "Hola Mundo!";
=======
        print_r($users);
>>>>>>> upstream/master
    }

}
