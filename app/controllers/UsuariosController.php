<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

use system\core\Controller;

class UsuariosController extends Controller{

    public function index()
    {
        $userModel = new UserModel("user");

        $users = $userModel->getUsers();
        
    }

}
