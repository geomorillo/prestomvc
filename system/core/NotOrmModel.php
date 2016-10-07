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

    public function __construct()
    {
        //get a pdo connection from database config
        $dbconfig = new DbConfig();

        $conectionInfo = $dbconfig->getConnectionInfo();

        $dsn = "$conectionInfo[driver]:dbname=$conectionInfo[db_name]";

        $pdo = new \PDO($dsn, $conectionInfo["db_user"], $conectionInfo["db_password"]);
        //$pdo = \system\database\Database::connect();
        $this->db = new \NotORM($pdo);
        if (ENABLE_DEBUG) {
            \NotOrmTracyPanel::simpleInit($this->db, $pdo);
        }
    }

}
