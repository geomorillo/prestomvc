<?php

require_once 'system/core/Boot.php';
use system\core\Boot;

Boot::run();

$router = new system\core\Router();
$router->addRoute("/(UsuariosController)/(index)", array("controller", "method"));
$router->addRoute("/(.*)", array("catchall"));
print_r($router->parse($_SERVER['REQUEST_URI']));
