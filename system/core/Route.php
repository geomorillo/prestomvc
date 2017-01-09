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
        "[:num]" => '(\d+)',
        "[:all]" => '\w+',
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
        if ($this->checkActionFormat($action)) {
            $this->routes[] = ["method" => $method, "url" => $url, "action" => $action, "before" => $before, "after" => $after];
        }
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
        if (WEBROOT != "/" && WEBROOT != '') {//si no estan en la raiz (localhost/directory)
            if (strpos($route, ':')) {
                $route = baseUrl('') . '/' . str_replace(array_keys($this->patterns), array_values($this->patterns), $this->url);
            } elseif ($route !== '/') {
                $route = baseUrl('') . "/" . $route;
            } elseif ($route === '/') {
                $route = baseUrl('');
            }
        } else {
          
            if (strpos($route, ':')) {
                $route = '/' . str_replace(array_keys($this->patterns), array_values($this->patterns), $this->url);
            } elseif ($route !== '/') {
                $route = "/" . $route;
            }
        }

        /*
          if (strpos($route, ':')) {
          $route = WEBROOT. '/' . str_replace(array_keys($this->patterns), array_values($this->patterns), $this->url);
          } elseif ($route !== '/' && WEBROOT!='/') {
          $route = WEBROOT. "/".$route;
          }elseif($route =='/'){
          $route = WEBROOT;
          } */
        $pattern = "@^" . $route . "$@"; //"@^" . $route . "$@";
        if (preg_match($pattern, $requestUri, $matched)) {
            if ($matched[0] === $requestUri) {
                $url = array_shift($matched);
                preg_match('/\w+\//', $url, $replace);
                if (count($matched)) {
                    $this->params = $matched;
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
        $currentMethod = $this->request->getMethod();
        $currentUrl = $this->request->getUrl();
        foreach ($this->routes as $route) {
            if ($route["method"] === 'ANY') {
                $this->method = $currentMethod;
            } else {
                $this->method = $route["method"];
            }
            $this->url = $route["url"];
            if (!($route["action"] instanceof \Closure)) {
                $this->parseAction($route["action"]);
            }

            if ($this->match()) { //if the requesturl doesn't match the route don't execute it
                if ($currentMethod === $this->method && $currentUrl === $this->route) {
                    if ($route["action"] instanceof \Closure) {
                        $this->found = TRUE;
                        $route["action"]();
                        break;
                    } else {
                        $this->controller = $this->removeSpecialChars($this->controller);
                        if (class_exists($this->controller)) {//handle double quote string
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
                            if (count($layers) > 0) {
                                $this->onion->layer($layers)
                                        ->peel($object, function($object) {
                                            $this->executeDispatch($this->controller, $this->action, $this->params);
                                            $object->runs[] = 'core';
                                            return $object;
                                        });
                            } else {
                                $this->executeDispatch($this->controller, $this->action, $this->params);
                            }
                            $this->found = TRUE;
                            break;
                        } else {
                            $this->found = FALSE;
                        }
                    }
                }
            }
        }//endforeach
        if (!$this->found) {
            echo $this->view->useTemplate("error")->render("error/404");
        }
    }

    /**
     * Handle double quoted escaped characters \n \t \f \c in route action
     * @param type $action
     * @return type
     */
    public function removeSpecialChars($action)
    {

        $some_special_chars = array("\t", "\n", "\f", "\r", "\e", "\v");
        $replacement_chars = array("\\t", "\\n", "\\f", "\\r", "\\e", "\\v");
        $rep = str_replace($some_special_chars, $replacement_chars, $action);
        return $rep;
    }

    /**
     * Checks if action has correct formar Controller@function
     * @param mixed $action
     * @return boolean
     */
    public function checkActionFormat($action)
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

    private function executeDispatch($controller, $action, $params)
    {
        if (isset($params)) {
            call_user_func_array(array(new $controller, $action), $params);
        } else {
            call_user_func(array(new $controller, $action));
        }
    }

}
