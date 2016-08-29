<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

use system\Model;

class UserModel extends Model
{

    public function getUsers()
    {

        $sql = "select * from $this->table";

        $users = $this->db->getAll($sql);

        return $users;
    }

}
