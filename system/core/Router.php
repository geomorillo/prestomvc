<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace system\core;

use system\core\Route;

/**
 * Description of Router
 *
 * @author Daniel Navarro Ramírez
 */
class Router {
    private $route;
        
     public function __construct()
     {
         $this->route = new Route();
     }
    
    public function get($route, $action)
    {
       return $this->route->addRoute('GET', $route, $action);
    }
    
    public function post($route, $action)
    {
        return $this->route->addRoute('POST', $route, $action);
    }
    
    public function put($route, $action)
    {
       return $this->route->addRoute('PUT', $route, $action); 
    }
    
    public function delete($route, $action)
    {
        return $this->route->addRoute('DELETE', $route, $action);
    }

}
