<?php
namespace system\core;

use system\http\Request;
/**
 * Description of Route
 *
 * @author Daniel Navarro Ramírez
 */
class Route {
    
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
    
    /**
     *
     * @var array Matches results 
     */
    public $data;
    
    public $controller;
    public $action;
    public $url;
    
    /**
     *
     * @var array Patterns Regexp 
     */
    private $patterns = array(
        ":any" => ".*",
        ":num" => "\d+",
        ":all" => "\w+",
    );
    
    public $request;
    
    public function __construct($method, $url, $action)
    {
        $this->request = new Request();
        
        $this->method = $method;
        $this->url[] = !empty($url) ? $url : '/';
        
        $action = $this->parseAction($action);
        $this->dispatch($this->request);
    }
    
    /**
     * Match the route, and return all data parse
     * 
     * @param Request $request
     * @return \system\routing\Route
     */
    public function match(Request $request)
    {
        $requestUri = $request->getUrl();
        foreach($this->url as $route) {
            if (strpos($route, ':') !== false) {
                $route = str_replace(array_keys($this->patterns), array_values($this->patterns), $route);
            }

            if (preg_match("@^" . $route . "$@", $requestUri, $matched)) {
                if($matched[0] === $requestUri) {
                    $url = array_shift($matched);
                    
                    $names = explode(',', $this->paramNames);
                    for ($i = 0; $i <= count($names)-1; $i++)
                    {
                        $this->params[$names[$i]] = $matched[$i];
                        $this->route = $url;
                    }
                }
            }
        }
        return $this->route;
    }
    
    protected function parseAction($action)
    {
        $parts = explode("@", $action);
        
        $this->controller = array_shift($parts);
        $this->action = array_shift($parts);
        preg_match("/\[[\w+\s\,]+\]/", $action, $matched);
        
        $asd = implode($matched);        
        $asd = ltrim($asd, "[");
        $asd = rtrim($asd, "]");
        
        $this->paramNames = $asd;
    }
    
    /**
     * Dispatch the controller, method and params
     * 
     * @param Request $request
     */
    public function dispatch(Request $request)
    {
        $this->match($request);
        
        if($request->getMethod() === $this->method && $request->getUrl() === $this->route)
        {
            $filename = explode("\\", $this->controller);
            $filename = end($filename);
            
            $action = explode("[", $this->action);
            $action = array_shift($action);
                
            if(file_exists(CONTROLLER_PATH . $filename . ".php")) {
                 if(class_exists($this->controller)) {
                     if(isset($this->params)) {
                         call_user_func_array(array(new $this->controller, $action), $this->params);
                     } else {
                         call_user_func(array(new $this->controller, $action));
                     }
                 }
             } 
        }
    }
    
}
