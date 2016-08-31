<?php

namespace system\core;

/**
 * Description of ControllerFactory
 *
 * @author daniel
 */
class ControllerFactory {
    
    public function createRouter(Router $router)
    {
        $result = $router->parse($_SERVER['REQUEST_URI']);

        if(isset($result['controller']))
        {
            $controller = NAMESPACE_CONTROLLERS . $result['controller'] . "Controller";
            if(class_exists($controller))
            {
                return new $controller();
            }
        }
    }
    
    public static function route(Router $router)
    {
        // Get the URL and parse it
        $results = $router->parse($_SERVER['REQUEST_URI']);
        $callController = NAMESPACE_CONTROLLERS . $results["controller"];

        if(class_exists($callController)) {
            $controller = new $callController;
            // If exists the method called then ...
            if(method_exists($controller, $results["method"])) {
                $controller->$results["method"]();
            } else {
                return $controller->index();
            }
        } else {
            throw new \Exception($callController .' -- Controller not found --'); 
        }
    }
}
