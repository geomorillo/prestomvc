<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;

/**
 * Description of Logger
 *
 * @author geomorillo
 */
use system\core\LogLevel;

class Logger implements LoggerInterface
{

    private static $file = "./debug.log";

    public static function emergency($message, array $context = array())
    {
        self::writeFile(LogLevel::EMERGENCY, $message, $context);
    }

    public static function critical($message, array $context = array())
    {
        self::writeFile(LogLevel::CRITICAL, $message, $context);
    }

    public static function debug($message, array $context = array())
    {
        self::writeFile(LogLevel::DEBUG, $message, $context);
    }

    public static function error($message, array $context = array())
    {
        self::writeFile(LogLevel::ERROR, $message, $context);
    }

    public static function info($message, array $context = array())
    {

        self::writeFile(LogLevel::INFO, $message, $context);
    }

    public static function log($level, $message, array $context = array())
    {
        self::writeFile("LOG LEVEL $level:", $message, $context);
    }

    public static function notice($message, array $context = array())
    {
        self::writeFile(LogLevel::NOTICE, $message, $context);
    }

    public static function warning($message, array $context = array())
    {
        self::writeFile(LogLevel::WARNING, $message, $context);
    }

    public static function alert($message, array $context = array())
    {
        self::writeFile(LogLevel::ALERT, $message, $context);
    }

    /**
     * Interpolates context values into the message {placeholders}
     */
    private static function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    private static function writeFile($level, $message, $context)
    {
        if ($level) {
            $level.= ":";
        }
        if ($context) {
            $message = self::interpolate($message, $context);
        }
        $log = date("F j, Y, g:i a") . " $level " . " $message " . PHP_EOL;
        //Save string to log, use FILE_APPEND to append.
        file_put_contents(self::$file, $log, FILE_APPEND);
    }

}
