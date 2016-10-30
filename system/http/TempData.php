<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2016
 * Contacto: geomorillo@yahoo.com
 */

namespace system\http;

/**
 * TempData 
 * Temporarily store data between requests per session
 * @author geomorillo
 */
use system\core\Encrypter;

class TempData
{

    protected static function sid()
    {
        if (session_status() != PHP_SESSION_NONE) {
            session_start();
        }
        return session_id();
    }

    public static function get($name)
    {
        $name = $name . static::sid();
        if (isset($_SESSION[$name])) {
            $value = (TEMPDATAENCRYPT ? Encrypter::decrypt($_SESSION[$name]) : $_SESSION[$name]);
            unset($_SESSION[$name]);
            return $value;
        } else {
            return FALSE;
        }
    }

    public static function set($name, $value)
    {
        $_SESSION[$name . static::sid()] = (TEMPDATAENCRYPT ? Encrypter::encrypt($value) : $value);
    }

}
