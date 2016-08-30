<?php

<<<<<<< HEAD
include "system/Boot.php";

$app = new Boot();
$app::run();
=======
/*
session_start();
 
define( "APP_PATH", dirname( __FILE__ ) ."/app/" );

 use system\core\Registro;
>>>>>>> upstream/master

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
