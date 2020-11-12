<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;

/**
 * Description of Csrf
 *
 * @author geomorillo
 */
use system\core\Encrypter;
use system\core\LogException;

class Csrf
{
    static $log;


    public static function generate()
    {
       $log =  new LogException();
        if (!USE_SESSIONS) {
           $log->errorMessage("Sessions not enabled");
            return FALSE;
        }
        //generate csrf
        $token_id = base64_encode(Encrypter::get_random_bytes(32));
        //store in sessions
        static::save("token_id", $token_id);
        
        //return generated csrf
        return $token_id;
    }

    public static function validate($token)
    {
        $log =  new LogException();
        if (!USE_SESSIONS) {
            $log->errorMessage("Sessions not enabled");
            return FALSE;
        }
        //validate csrf
        //get stored csrf
        $token_id = static::get_key("token_id");
        //if equal then valid
        if($token_id === $token){
            return TRUE;
        }else{
            static::destroy($token);
            return FALSE;
        }
    }

    public static function save($key, $value)
    {
        if (isset($_SESSION)) {
            $_SESSION[$key] = $value;
        }
    }

    public static function destroy($key)
    {
        $_SESSION[$key] = ' ';
        unset($_SESSION[$key]);
    }

    public static function get_key($key)
    {
        if (!isset($_SESSION[$key])) {
            return FALSE;
        }
        return $_SESSION[$key];
    }

}
