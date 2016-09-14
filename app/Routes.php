<?php
namespace app;
/*
 * Adding new routes.
 * 
 * $router->add($route, $action)
 * 
 * string @route get the uri
 * strin @action get namespace and controller split by "@" then method from class controller
 */
// Add new routes
//$route->addRoute("GET", "/", "app\controllers\UsuariosController@index");
//$route->addRoute("GET", "/users/(:num)/show", "app\controllers\UsuariosController@show", array("id"));
//$route->addRoute("POST", "/ajax", "app\controllers\Main@testAjax");
//$route->addRoute("POST", "/ajaxcall", "app\controllers\Main@ajaxcall");
//$route->addRoute("GET", "/session", "app\controllers\Main@session");
//$route->addRoute("GET", "/db", "app\controllers\Main@db");
//$route->addRoute("GET", "/encrypt", "app\controllers\Main@encrypt");
$router->get("/", "app\controllers\UsuariosController@index");
$router->get("/nombre/(:num)", "app\controllers\UsuariosController@show[id]");
$router->get("/usuarios/(:any)/(:num)", "app\controllers\UsuariosController@test[name, id]");