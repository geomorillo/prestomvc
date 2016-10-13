<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;

/**
 * Description of AfterLayer
 *
 * @author geomorillo
 */
use Closure;

class AfterLayer implements LayerInterface
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
        $response = $next($object);
        $object->runs[] = 'after';
        call_user_func(array(new $this->controller, $this->method));
        return $response;
    }

}
