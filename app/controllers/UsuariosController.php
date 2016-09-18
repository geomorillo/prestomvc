<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace app\controllers;

use system\core\Controller;
use system\core\View;
use system\core\Logger;
use system\core\LogException;

class UsuariosController extends Controller
{

    public function index()
    {
        $userModel = new \app\models\UserModel();

        $users = $userModel->getUsers();
        
        echo View::render("usuarios", array("users" => $users));
    }

    public function testLog()
    {
            Logger::alert("Ok");
            Logger::info("{usuario} tiene {edad}", array("usuario" => "geo", "edad" => "36"));
    }

    public function testLogExeption()
    {
        try {
            throw new LogException;
        } catch (LogException $exc) {
            $exc->logError();
            $exc->errorMessage("Aqui va un error");
        }
    }
    public function show($id){
        echo $id;
    }

}
