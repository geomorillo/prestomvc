<?php

namespace system\core;

use system\core\Route;

/**
 * Description of Router
 *
 * @author Daniel Navarro Ramírez
 * @author Manuel Jhobanny Morillo
 */
class Router extends Route
{

    public function get($route, $action, $before = "before", $after = "after")
    {
        $this->addRoute('GET', $route, $action, $before, $after);
    }

    public function post($route, $action, $before = "before", $after = "after")
    {
            $this->addRoute('POST', $route, $action, $before, $after);
    }

    public function put($route, $action, $before = "before", $after = "after")
    {
            $this->addRoute('PUT', $route, $action, $before, $after);
    }

    public function delete($route, $action, $before = "before", $after = "after")
    {
            $this->addRoute('DELETE', $route, $action, $before, $after);
    }

    public function any($route, $action, $before = "before", $after = "after")
    {
            $this->addRoute('ANY', $route, $action, $before, $after);
    }

}
