<?php

namespace system\core;

use system\core\Route;

/**
 * Description of Router
 *
 * @author Daniel Navarro Ramírez
 */
class Router extends Route
{

    public function get($route, $action, $before = NULL, $after = NULL)
    {
        return $this->addRoute('GET', $route, $action, $before, $after);
    }

    public function post($route, $action, $before = NULL, $after = NULL)
    {
        return $this->addRoute('POST', $route, $action, $before, $after);
    }

    public function put($route, $action, $before = NULL, $after = NULL)
    {
        return $this->addRoute('PUT', $route, $action, $before, $after);
    }

    public function delete($route, $action, $before = NULL, $after = NULL)
    {
        return $this->addRoute('DELETE', $route, $action, $before, $after);
    }

    public function any($route, $action, $before = NULL, $after = NULL)
    {
        return $this->addRoute('ANY', $route, $action, $before, $after);
    }

}
