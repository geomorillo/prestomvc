<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;

use Exception;
use system\core\Logger;

class LogException extends \Exception
{

    public function logError()
    {
        $errorMsg = 'Error on line ' . $this->getLine() . ' in ' . $this->getFile() . ':' . $this->getTraceAsString();
        Logger::error($errorMsg);
        return $errorMsg;
    }

    public function errorMessage($message)
    {
        $errorMsg = 'Error on line ' . $this->getLine() . ' in ' . $this->getFile() . ': ' . $message;
        Logger::error($errorMsg);
        return $errorMsg;
    }

}
