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
define('ENCRYPT_KEY', "IeT5aprlRw7pImmU73T89tNyk0cOSrkA");//CHANGE THIS WITH YOUR OWN KEY
define('TEMPDATAENCRYPT',FALSE);//Encrypt TempData TRUE/FALSE(for speed)
define("DEFAULT_LANG", "es");//Defaul language spanish supported es/en/nl
define("DB_PREFIX",""); //prefix for tables in database
