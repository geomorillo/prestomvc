<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2016
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;

/**
 * Create events system
 *
 * @author geomorillo
 */
class Event
{

    public static $events = array();

    public static function trigger($event, $args = array())
    {
        if (isset(self::$events[$event])) {
            foreach (self::$events[$event] as $func) {
                call_user_func($func, $args);
            }
        }
    }

    public static function create($event, \Closure $function)
    {
        self::$events[$event][] = $function;
    }

}
