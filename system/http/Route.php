<?php

namespace system\http;

use system\http\Request;

/**
 * Description of Route
 *
 * @author Daniel Navarro Ramírez
 */
class Route
{

    /**
     *
     * @var array Added routes
     */
    public $routes;

    /**
     *
     * @var string Method to request
     */
    public $method;

    /**
     *
     * @var string Controller and action (method)
     */
    private $callback;

    /**
     *
     * @var array Values of parameters passed from URL 
     */
    private $request;

    /**
     *
     * @var array Matches results 
     */
    public $data;

    /**
     *
     * @var array Patterns Regexp 
     */
    private $patterns = array(
        ":any" => ".*",
        ":num" => "\d+",
        ":all" => "\w+",
    );

    public function __construct()
    {
        $this->request = new Request();
    }

    /**
     * Adding route from file Routes.php on app/ folder.
     * 
     * to use this method or function just write this line of code
     * $route->addRoute('GET', '/your-route', 'app\controllers\YourController@yourmethod');
     * 
     * if you want to give variables to your route use the next patterns
     * (:any) => Reads any data, like numbers, and alpha words
     * (:num) => Reads only numbers
     * (:all) => Reads all data that you pass to your route
     * 
     * @param string $method
     * @param string $route
     * @param string $callback
     * @param array $action
     * @return array
     */
    public function addRoute($method, $route, $callback, array $action = null)
    {
        $this->routes[$route] = array(
            "route" => $route,
            "method" => $method,
            "callback" => $callback,
            "paramNames" => $action
        );

        return;
    }

    /**
     * Match the route, and return all data parse
     * @return \system\http\Route
     */
    public function match()
    {
        $requestUri = $this->request->getUrl();

        foreach ($this->routes as $route) {
            if (strpos($route["route"], ':') !== false) {
                $route["route"] = str_replace(array_keys($this->patterns), array_values($this->patterns), $route["route"]);
            }

            if (preg_match("@^" . $route["route"] . "$@", $requestUri, $matched)) {
                array_shift($matched);
                $this->data[$requestUri] = $matched;
                $this->method = $route["method"];
                $this->callback = $route["callback"];
                $this->paramValues = $matched;
            }
        }

        return $this;
    }

    /**
     * Dispatch the controller, method and params
     * 
     * @param Request $request
     */
    public function dispatch()
    {
        $matched = $this->match();

        if (isset($matched->data)) {
            $url = implode(array_keys($matched->data));

            if ($url == $this->request->getUrl()) {
                $parts = explode("@", $matched->callback);

                $controller_name = array_shift($parts);
                $method_name = array_shift($parts);

                $filename = explode("\\", $controller_name);
                $filename = end($filename);

                if (file_exists(CONTROLLER_PATH . $filename . ".php")) {
                    if (class_exists($controller_name)) {
                        if (isset($matched->paramValues)) {
                            call_user_func_array(array(new $controller_name, $method_name), $matched->paramValues);
                        } else {
                            call_user_func(array(new $controller_name, $method_name));
                        }
                    }
                }
            }
        } else {
            echo "Error 404 - Route doesn't exists";
        }
    }

}
