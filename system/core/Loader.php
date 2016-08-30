<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;
class Loader{

    public function helper($helper){

        include HELPER_PATH . "{$helper}.php";

    }

}