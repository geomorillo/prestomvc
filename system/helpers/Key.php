<?php

/**
 * Simple helper to generate a key for Encrypter
 *
 * @author  Manuel Jhobanny Morillo Ordoñez geomorillo@yahoo.com 
 */
namespace system\helpers;
class Key {
    //suported $length 16, 32 the algo should be automatically picked by the encrypter
    /**
     * key - this will generate a 32 character key
     * @return string
     */
    public static function generate($length = 32)
    {
       $chars = "!@#$%^&*()_+-=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
       $key = "";

       for ($i = 0; $i < $length; $i++) {
           $key .= $chars{rand(0, strlen($chars) - 1)};
       }

       return $key;
    }
    
}
