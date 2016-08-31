<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */
namespace app\models;
use system\core\Model;

class UserModel extends Model
{

    public function getUsers()
    {   
        $allData = $this->db->table('usuarios')->select();
        return $allData;

    }
}
