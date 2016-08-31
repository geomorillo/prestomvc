<?php
require_once 'system/Boot.php';
use system\Boot;
Boot::run();

// Testing Router

$router = new system\core\Router();
$router->addRoute("/test", array("UsuariosController", "test"));
$router->addRoute("/admin/index/otro", array("UsuariosController", "test"));


$controllerFactory = new system\core\ControllerFactory();
//$controller = $controllerFactory->createRouter($router);
$controller = $controllerFactory::route($router);