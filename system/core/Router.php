<?php
namespace system\core;

/**
 * Description of Roter
 *
 * @author daniel
 */
class Router {
    
    private $routes = array();

    public function addRoute($pattern, $tokens = array())
    {
        $this->routes[] = array(
            "pattern" => $pattern,
            "tokens" => $tokens
        );
    }

    public function parse($url)
    {

        $tokens = array();

        foreach ($this->routes as $route)
        {
            preg_match("@^" . $route['pattern'] . "$@", $url, $matches);
            if($matches)
            {
                foreach($matches as $key=>$match)
                {
                    if($key == 0)
                    {
                        continue;
                    }

                    $tokens[$route['tokens'][$key-1]] = $match;

                }

                return $tokens;

            }
        }

        return $tokens;

    }
}
