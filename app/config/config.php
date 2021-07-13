<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */
define("DEFAULTCONTROLLER", 'Main');//Default controller to be executed check app/routes.php
define("DEFAULTMETHOD", 'index');// Defaul method to be executed
define("USE_SESSIONS", FALSE);//if you want to use sessions class must enable this
define("ENABLE_DEBUG",TRUE);//TRUE = DEVELOPMENT, FALSE = PRODUCCION
define('ENCRYPT_KEY', "e224cda70cfb2e70586d8d1d28424b31");//CHANGE THIS WITH YOUR OWN KEY (SEE KEY HELPER)
define('TEMPDATAENCRYPT',FALSE);//Encrypt TempData TRUE/FALSE(for speed)
define("DEFAULT_LANG", "en");//Defaul language spanish : es/en
define("DB_PREFIX",""); //prefix for tables in database
//define the path where to save the logs don't forget the / at the end
define("LOG_FILENAME","debug.log");//define the log's name
