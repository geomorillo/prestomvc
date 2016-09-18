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
//$router->get("/", "app\controllers\UsuariosController@index");
//$router->get("/users/(:num)/show", "app\controllers\UsuariosController@show", array("id"));
//$router->post("/ajax", "app\controllers\Main@testAjax");
//$router->get( "/ajaxcall", "app\controllers\Main@ajaxcall");
//$router->get("/session", "app\controllers\Main@session");
//$router->get( "/db", "app\controllers\Main@db");
//$router->get( "/encrypt", "app\controllers\Main@encrypt");


//$router->get("/", "app\controllers\UsuariosController@index");
//$router->get("/nombre/(:num)", "app\controllers\UsuariosController@show[id]");
//$router->get("/usuarios/(:any)/(:num)", "app\controllers\UsuariosController@test[name, id]");
$router->get("/closure",function(){
    echo "ok";
});
$router->get("/x",function(){
    echo "okx";
});