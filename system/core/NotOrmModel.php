<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\core;

/**
 * Description of OrmModel
 *
 * @author geomorillo
 */
use system\helpers\DbConfig;

abstract class NotOrmModel
{

    protected $db;
    private $npdo;
  
    private $conectionInfo;
    public function __construct()
    {
        //get a pdo connection from database config
        $dbconfig = new DbConfig();
        $this->conectionInfo = $dbconfig->config;
        $this->strConn($dbconfig->getDefaultConnectionName());
        $this->db = new \NotORM($this->npdo);
        if (ENABLE_DEBUG) {
            \NotOrmTracyPanel::simpleInit($this->db, $this->npdo);
        }
    }
    protected function strConn($default)
    {
        try {
            $driver = $host_name = $db_name = $db_user = $db_password = $dsn = NULL;
            switch ($default) {
                case "sqlite":
                    $dsn = "sqlite:" . $this->conectionInfo[$default]["db_path"];
                    $this->npdo = new \PDO($dsn);
                    break;
                case "pgsql":
                    $driver =  $this->conectionInfo[$default]["driver"];
                    $host_name =  $this->conectionInfo[$default]["host_name"];
                    $db_name =  $this->conectionInfo[$default]["db_name"];
                    $db_user =  $this->conectionInfo[$default]["db_user"];
                    $db_password =  $this->conectionInfo[$default]["db_password"];
                    $dsn = "$driver:user=" . $db_user . 'dbname=' . $db_name . ' password=' . $db_password;
                    $this->npdo = new \PDO($dsn);
                    break;
                default:
                    //mysql,mssql,sybase
                    $driver =  $this->conectionInfo[$default]["driver"]; //exepto sqlite y oci
                    $host_name =  $this->conectionInfo[$default]["host_name"]; //exepto sqlite y oci
                    $db_name =  $this->conectionInfo[$default]["db_name"]; //exepto oci y sqlite
                    $dsn = "$driver:host=$host_name;dbname=$db_name";
                    $db_user =  $this->conectionInfo[$default]["db_user"];
                    $db_password =  $this->conectionInfo[$default]["db_password"];
                    $this->npdo = new \PDO($dsn, $db_user, $db_password);
                    break;
            }
            $this->npdo->exec("set names " . 'utf8');
            $this->npdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (LogException $e) {
            $e->logError();
        }
    }

}
