<?php

namespace system\core;

use system\http\Request;
use system\core\View;

/**
 * Description of Route
 *
 * @author Daniel Navarro Ramírez
 * @author Manuel Jhobanny Morillo
 */
class Route
{

    public $routes = [];

    /**
     *
     * @var array Route
     */
    public $route;

    /**
     *
     * @var string Method to request
     */
    public $method;

    /**
     *
     * @var array Name of parameters for values
     */
    private $paramNames;
    private $params;

    /**
     *
     * @var array Matches results 
     */
    public $controller;
    public $action;
    public $url;
    private $view;
    private $onion; //middleware

    /**
     *
     * @var array Patterns Regexp 
     */
    private $patterns = array(
        "[:any]" => '(.*?)',
        "[:num]" => "(\d+)",
        "[:all]" => "\w+",
    );
    public $request;
    private $found = FALSE;

    public function __construct()
    {
        $this->request = new Request();
        $this->view = new View();
        $this->onion = new Onion(); //for middleware
    }

    public function addRoute($method, $url, $action, $before, $after)
    {
        $url = !empty($url) ? $url : '/';
        $this->routes[] = ["method" => $method, "url" => $url, "action" => $action, "before" => $before, "after" => $after];
    }

    /**
     * Match the route, and return all data parse
     * 
     * @param Request $request
     * @return \system\routing\Route
     */
    public function match()
    {
        $requestUri = $this->request->getUrl();
        $route = $this->url;
        if (strpos($route, ':') !== false) {
            $route = "/" . str_replace(array_keys($this->patterns), array_values($this->patterns), $this->url);
        } elseif($route==="/") {
            //do nothing (temporal fix) ugly fix later
        }else{
            $route = "/" . $route;
        }
        $pattern = "@^" . $route . "$@"; //"@^" . $route . "$@";
        if (preg_match($pattern, $requestUri, $matched)) {
            if ($matched[0] === $requestUri) {
                $url = array_shift($matched);
                preg_match('/\w+\//', $url, $replace);
                if (count($replace)) {
                    $value_str = str_replace("/" . $replace[0], "", $url);
                    $values = explode("/", $value_str);

                    for ($i = 0; $i <= count($values) - 1; $i++) {
                        if ($values != '') {
                            $this->params[] = $values[$i];
                        }
                    }
                }
                $this->route = $url;
            }
        } else {
            $this->route = null;
        }
        return $this->route;
    }

    protected function parseAction($action)
    {
        $parts = explode("@", $action);
        $this->controller = array_shift($parts);
        $this->action = array_shift($parts);
    }

    /**
     * Dispatch the controller, method and params
     * 
     * @param Request $request
     */
    public function dispatch()
    {
        foreach ($this->routes as $route) {
            $this->method = $route["method"];
            $this->url = $route["url"];
            if(!($route["action"] instanceof \Closure)){
                $this->parseAction($route["action"]);
            }
            
            if ($this->match()) { //if the requesturl doesn't match the route don't execute it
                if ($this->request->getMethod() === $this->method && $this->request->getUrl() === $this->route) {
                    if ($route["action"] instanceof \Closure) {
                        $route["action"]();
                    } else {

                        $filename = explode("\\", $this->controller);
                        $filename = end($filename);
                        $action = explode("[", $this->action);
                        $action = array_shift($action);
                        $this->action = $action;
                        if (class_exists($this->controller)) {
                            $object = new \stdClass;
                            $object->runs = [];
                            $layers = [];
                            /* MIDDLEWARE */
                            if (method_exists($this->controller, $route["before"])) {
                                $layers[] = new BeforeLayer($this->controller, $route["before"]);
                            }
                            if (method_exists($this->controller, $route["after"])) {
                                $layers[] = new AfterLayer($this->controller, $route["after"]);
                            }

                            $this->onion->layer($layers)
                                    ->peel($object, function($object) {
                                        if (isset($this->params)) {
                                            call_user_func_array(array(new $this->controller, $this->action), $this->params);
                                        } else {
                                            call_user_func(array(new $this->controller, $this->action));
                                        }
                                        $object->runs[] = 'core';
                                        return $object;
                                    });
                        }
                    }
                    $this->found = TRUE;
                }
            }
        }
        if (!$this->found) {

            echo $this->view->useTemplate("error")->render("error/404");
        }
    }

}
