<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\helpers;

/**
 * Gets some properties from database_config
 *
 * @author geomorillo
 */
class DbConfig
{
   public $config;
   
   public function __construct()
   {
       $this->config = include_once CONFIG_PATH."database_config.php";
   }
   public function getDefaultConnectionName()
   {
       return $this->config["default"];
   }
   
   public function getConnectionInfo(){
       return $this->config[$this->getDefaultConnectionName()];
   }
   
    
}
