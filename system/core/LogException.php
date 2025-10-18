<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;

use Exception;
use system\core\Logger;

abstract class LogException extends \Exception
{
    protected $httpCode = 500;
    protected $logLevel = LogLevel::ERROR;

    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->autoLogError();
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    public function getLogLevel()
    {
        return $this->logLevel;
    }

    protected function autoLogError()
    {
        $context = [
            'exception' => $this,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTraceAsString()
        ];

        // Usar Logger con el nivel apropiado
        $level = strtolower($this->logLevel);
        Logger::$level($this->formatLogMessage(), $context);
    }

    protected function formatLogMessage()
    {
        return sprintf(
            '[%s] %s',
            get_class($this),
            $this->getMessage()
        );
    }

    // Mantener métodos legacy para compatibilidad
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
