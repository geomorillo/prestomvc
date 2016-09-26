<?php

namespace system\core;

use system\core\Route;

/**
 * Description of Router
 *
 * @author Daniel Navarro Ramírez
 */
class Router extends Route{

    public function get($route, $action)
    {
       return $this->addRoute('GET', $route, $action);
    }
    
    public function post($route, $action)
    {
        return $this->addRoute('POST', $route, $action);
    }
    
    public function put($route, $action)
    {
       return $this->addRoute('PUT', $route, $action); 
    }
    
    public function delete($route, $action)
    {
        return $this->addRoute('DELETE', $route, $action);
    }

}
