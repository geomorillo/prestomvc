<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;

/**
 * Description of BeforeLayer
 *
 * @author geomorillo
 */
use Closure;
class BeforeLayer implements LayerInterface
{
    private $controller;
    private $method;
    public function __construct($controller,$method)
    {
        $this->controller = $controller;
        $this->method = $method;
    }

    public function peel($object, Closure $next)
    {
        $object->runs[] = 'before';
        call_user_func(array(new $this->controller, $this->method));
        return $next($object);
    }

}
