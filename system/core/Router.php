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

    public function get($route, $action, $before = NULL, $after = NULL)
    {
        if ($this->checkActionFormat($action)) {
            $this->addRoute('GET', $route, $action, $before, $after);
        }
    }

    public function post($route, $action, $before = NULL, $after = NULL)
    {
        if ($this->checkActionFormat($action)) {
            $this->addRoute('POST', $route, $action, $before, $after);
        }
    }

    public function put($route, $action, $before = NULL, $after = NULL)
    {
        if ($this->checkActionFormat($action)) {
            $this->addRoute('PUT', $route, $action, $before, $after);
        }
    }

    public function delete($route, $action, $before = NULL, $after = NULL)
    {
        if ($this->checkActionFormat($action)) {
            $this->addRoute('DELETE', $route, $action, $before, $after);
        }
    }

    public function any($route, $action, $before = NULL, $after = NULL)
    {
        if ($this->checkActionFormat($action)) {
            $this->addRoute('ANY', $route, $action, $before, $after);
        }
    }

    /**
     * Checks if action has correct formar Controller@function
     * @param mixed $action
     * @return boolean
     */
    private function checkActionFormat($action)
    {
        if ($action instanceof \Closure) { //closures are ok
            return TRUE;
        } elseif (is_string($action)) {
            $parts = explode("@", $action); //check if action has correct format do not add them
            if (count($parts) > 1) {
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

}
