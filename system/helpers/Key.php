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
        if (function_exists('random_bytes')) {
            $bytes = random_bytes($length / 2);
        } else {
            $bytes = openssl_random_pseudo_bytes($length / 2);
        }
        return bin2hex($bytes);

    }
    
}
