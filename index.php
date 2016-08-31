<?php
require_once 'system/Boot.php';
use system\Boot;
Boot::run();

// Testing Router

$router = new system\core\Router();
$router->addRoute("/(UsuariosController)/(index)", array("controller", "method"));
$router->addRoute("/(.*)", array("catchall"));
// Printing the catched URL
print_r($router->parse($_SERVER['REQUEST_URI']));