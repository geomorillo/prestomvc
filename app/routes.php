<?php
namespace app;
use system\core\View;
/*
 * Adding new routes.
 * 
 * $router->get($route, $action)
 * 
 * string @route The url or route
 * string @action Controller@Method  
 * Examples: 
 * $router->get("/",'app\controllers\Main@index') or shorter version $router->get("/","Main@index")
 * $router->get("login",'app\controllers\Main@login') or shorter version $router->get("/","Main@login")
 */
$router->get("/","Main@index");
$router->get("login","Main@login");