<?php
namespace system\core;

/**
 * Description of Roter
 *
 * @author daniel
 */
class Router {
    
    private $routes = array();
    private $controller;
    private $method;
    private $params;

    public function addRoute($route, array $tokens = array())
    {
        $this->routes[] = array(
            "route" => $route,
            "tokens" => $tokens
        );
    }

    public function parse($url)
    {
        print_r($url);
        echo "<br>";
        print_r($this->routes[0]["route"]);
        if ($url === $this->routes[0]["route"]) {
            echo "<br>Work<br>";
            $tokens = array();
        
            // Getting if something has inside some params
            $pattern = "/:[[:alpha:]]+/";
            preg_match_all($pattern, $this->routes[0]['route'], $matches);
            $this->params = $matches[0];

            /*foreach($this->params as $param)
            {
                $param = explode(":", $param);
                unset($param[0]);

                echo $param[1];
                echo "<br>";

            }*/

            // Getting controller and method

            foreach ($this->routes as $route)
            {
                $this->controller = isset($route['tokens'][0]) ? $route['tokens'][0] : "UsuariosController";
                $this->method = isset($route['tokens'][1]) ? $route['tokens'][1] : "index";

                $tokens["controller"] = $this->controller;
                $tokens["method"] = $this->method;
            }

            return $tokens;
        }

    }
}
