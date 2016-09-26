<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;

/**
 * Description of Model
 *
 * @author geomorillo
 */
use system\database\Database;

abstract class Model
{

    protected $db; //database connection object

    public function __construct()
    {
        $this->db = Database::connect();
   
    }
}
