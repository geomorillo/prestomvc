<?php


/*
session_start();
 
define( "APP_PATH", dirname( __FILE__ ) ."/app/" );

 use system\core\Registro;

require_once('system/core/Registro.php');
$registry = Registro::singleton();
$registry->storeCoreObjects();
//imprime la version
print $registry->getFrameworkName();
 
exit();
*/
require_once 'system/core/Boot.php';
use system\core\Boot;
Boot::run();
