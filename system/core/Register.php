<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;

/**
 * Description of RegisterLib
 *
 * @author geomorillo
 */
class Register
{

    public static function lib()
    {
        //leer el config file
        $register = include_once  CONFIG_PATH . "registerlib.php";
        //cargar cada archivo registrado
        if (count($register)) {
            foreach ($register as $libName => $libInfo) {
                include_once $libInfo["path"];
                if (isset($libInfo["callback"])) {
                    call_user_func($libInfo["callback"]);
                }
            }
        }
    }

}
