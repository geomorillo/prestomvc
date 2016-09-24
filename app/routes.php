<?php
namespace app;
use system\core\View;
/*
 * Adding new routes.
 * 
 * $router->get($route, $action)
 * 
 * string @route get the uri
 * strin @action get namespace and controller split by "@" then method from class controller
 */

$router->get("/","app\controllers\Main@index");