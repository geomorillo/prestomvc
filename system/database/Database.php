<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\database;

/**
 * Description of Database
 *
 * @author geomorillo
 */
use system\core\LogException;

class Database
{

    /**
     * @var $_instace type object
     * store DB class object to allow one connection with database (deny duplicate)
     * @access private
     */
    private static $_instance;

    /**
     * @var $_pdo type object PDO object
     * @var $_query type string store sql statement
     * @var $_results type array store sql statement result
     * @var $_count type int store row count for _results variable
     * @var $_error type bool if cant fetch sql statement = true otherwise = false
     */
    private $_pdo,
            $_results,
            $_count,
            $_typeQuery = '',
            $_isJoin = FALSE;
    private $queries = [];
    protected $table;
    protected $config;

    public function __construct()
    {
        $this->config = include CONFIG_PATH . 'database_config.php';
        $this->queries['where'] = [];
        $this->queries['orwhere'] = [];
        $this->queries['orderby'] = [];
        $this->queries['join'] = [];
        $this->queries['limit'] = '';
        $this->queries['offset'] = '';
        call_user_func_array(array(__NAMESPACE__ . '\Database', 'strConn'), [$this->config["default"]]);
    }

    protected function strConn($default)
    {
        try {
            $driver = $host_name = $db_name = $db_user = $db_password = $dsn = NULL;
            switch ($default) {
                case "sqlite":
                    $dsn = "sqlite:" . $this->config[$default]["db_path"];
                    $this->_pdo = new \PDO($dsn);
                    break;
                case "oci":
                    $tns = $this->config[$default]["tns"];
                    $db_user = $this->config[$default]["db_user"];
                    $db_password = $this->config[$default]["db_password"];
                    $this->_pdo = new \PDO("oci:dbname=" . $tns, $db_user, $db_password);
                    break;
                case "pgsql":
                    $driver = $this->config[$default]["driver"];
                    $host_name = $this->config[$default]["host_name"];
                    $db_name = $this->config[$default]["db_name"];
                    $db_user = $this->config[$default]["db_user"];
                    $db_password = $this->config[$default]["db_password"];
                    $dsn = "$driver:user=" . $db_user . 'dbname=' . $db_name . ' password=' . $db_password;
                    $this->_pdo = new \PDO($dsn);
                    break;
                default:
                    //mysql,mssql,sybase
                    $driver = $this->config[$default]["driver"]; //exepto sqlite y oci
                    $host_name = $this->config[$default]["host_name"]; //exepto sqlite y oci
                    $db_name = $this->config[$default]["db_name"]; //exepto oci y sqlite
                    $dsn = "$driver:host=$host_name;dbname=$db_name";
                    $db_user = $this->config[$default]["db_user"];
                    $db_password = $this->config[$default]["db_password"];
                    $this->_pdo = new \PDO($dsn, $db_user, $db_password);
                    break;
            }
            $this->_pdo->exec("set names " . 'utf8');
            $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (LogException $e) {
            $e->logError();
        }
    }

    /**
     * Singleton Database
     * return instace
     * @return object
     */
    public static function connect()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new Database();
        }
        return self::$_instance;
    }

    /**
     * DB::query()
     * check if sql statement is prepared
     * append value for sql statement if $paramer is set
     * fetch results
     * @param string $sql
     * @param array $params
     * @return mixed
     */
    public function query($sql, $params = [])
    {
        try {
            $this->_error = false;
            // check if sql statement is prepared
            $query = $this->_pdo->prepare($sql);
            // if $params isset
            if (count($params)) {
                foreach ($params as $param => &$value) {
                    $query->bindParam(":" . $param, $value);
                }
            }
            $result = $query->execute();
            $this->typeQuery($sql); //determina si es un select en un raw query
            if ($this->_typeQuery == "SELECT" && $result) {
                if ($this->_isJoin) {
                    $this->_results = json_decode(json_encode($query->fetchAll(\PDO::FETCH_NAMED))); //avoid column name colision
                    $this->_isJoin = FALSE;
                } else {
                    $this->_results = $query->fetchAll($this->config["fetch"]);
                }
                $this->_count = $query->rowCount();
            } else {
                $this->clean();
            }
        } catch (LogException $e) {
            $this->_results = NULL;
            $this->_error = true;
            $e->logError();
        }
        return $this;
    }

    /**
     * insert into database tables
     * @param string $table
     * @param array $values
     * @return bool
     */
    public function insert($values = [])
    {
        if (count($values)) {
            $this->_typeQuery = "INSERT";
            // check if $values set

            $fields = array_keys($values);
            $value = [];
            foreach ($values as $param => $paramValue) {
                $value[] = " :$param ";
            }
            $value = implode(",", $value);
            // generate sql statement
            $sql = "INSERT INTO {$this->_table} (`" . implode('`,`', $fields) . "`)";
            $sql .= " VALUES ({$value})";
            // check if query has an error
            if (!$this->query($sql, $values)->error()) {
                return true;
            }
        }
        return false;
    }

    /**
     * DB::update()
     *
     * @param string $table
     * @param array $values
     * @param array $where
     * @return bool
     */
    public function update($values = [])
    {
        if (count($values)) {

            $this->_typeQuery = "UPDATE";

            $set = [];
            foreach ($values as $param => $paramValue) {
                $set[] = " $param = :$param ";
            }
            $set = implode(",", $set);

            $sql = "UPDATE {$this->_table} SET {$set} " . $this->contructQuery();
            // check if query is not having an error
            if (!$this->query($sql, $values)->error()) {
                return true;
            }
        }
        return false;
    }

    /**
     * select from database
     * @param  array  $fields fields we need to select
     * @return array  result of select
     */
    public function select($fields = ['*'])
    {
        $this->_typeQuery = "SELECT";
        $sql = "SELECT " . implode(', ', $fields) . " FROM {$this->_table} {$this->contructQuery()}";
        $this->queries = NULL;
        return $this->query($sql)->results();
    }

    /**
     * delete from table
     * @return bool
     */
    public function delete()
    {
        $this->_typeQuery = "DELETE";
        $sql = "DELETE FROM $this->_table " . $this->contructQuery();
        $delete = $this->query($sql);
        if ($delete) {
            return true;
        }
        $this->_error = true;
        return false;
    }

    /**
     * find single row from table via id
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function find($id)
    {
        $find = $this->where("id", $id)
                ->select();
        return isset($find[0]) ? $find[0] : [];
    }

    /**
     * add where condition to sql statement
     * @param  string  $field    field name from table
     * @param  string  $operator operator (= , <>, .. etc)
     * @param  mix $value    the value
     * @return object        this class
     */
    public function where($field, $operator, $value = false)
    {
        /**
         * if $value is not set then set $operator to (=) and
         * $value to $operator
         */
        if ($value === false) {
            $value = $operator;
            $operator = "=";
        }
        if (!is_numeric($value)) {
            $value = "'$value'";
        }
        $this->queries['where'][] = "($field $operator $value)";


        return $this;
    }

    /**
     * between condition
     * @param  string $field  table field name
     * @param  arrya $values ['from', 'to']
     * @return object        this class
     */
    public function whereBetween($field, $values = [])
    {
        if (count($values)) {
            $this->queries['where'][] = "($field BETWEEN '$values[0]' AND '$values[1]')";
        }
        return $this;
    }

    /**
     * Like whare
     * @param  string $field database field name
     * @param  string $value value
     * @return object 	this class
     */

    /**
     * we can do that with where() methode
     * $db->table('test')->where('name', 'LIKE', '%moha%');
     */
    public function likeWhere($field, $value)
    {
        $this->queries['where'][] = "($field LIKE '%$value%')";
        return $this;
    }

    /**
     * to add OR condition
     * where() method add (AND) this method add (OR)
     * @param  [type]  $field    [description]
     * @param  [type]  $operator [description]
     * @param  boolean $value    [description]
     * @return [type]            [description]
     */
    public function orWhere($field, $operator, $value = false)
    {
        if ($value === false) {
            $value = $operator;
            $operator = "=";
        }
        $this->queries['orwhere'][] = "($field $operator '$value')";
        return $this;
    }

    /**
     * [in description]
     * @param  [type] $field  [description]
     * @param  array  $values [description]
     * @return [type]         [description]
     */
    public function in($field, $values = [])
    {
        if (count($values)) {
            $this->queries['where'][] = "($field IN (" . implode(",", $values) . "))";
        }
        return $this;
    }

    /**
     * [notIn description]
     * @param  [type] $field  [description]
     * @param  array  $values [description]
     * @return [type]         [description]
     */
    public function notIn($field, $values = [])
    {
        if (count($values)) {
            $this->queries['where'][] = "($field NOT IN (" . implode(",", $values) . "))";
        }
        return $this;
    }

    /**
     * get first row from query results
     * @return array
     */
    public function first()
    {

        $first = $this->select();

        if (count($first)) {
            return $first[0];
        }

        return [];
    }

    /**
     * [limit description]
     * @param  [type] $limit [description]
     * @return [type]        [description]
     */
    public function limit($limit)
    {
        if (!$this->queries['limit']) { //only one allowed
            $this->queries['limit'] = " LIMIT " . (int) $limit;
        }
        return $this;
    }

    /**
     * [offset description]
     * @param  [type] $offset [description]
     * @return [type]         [description]
     */
    public function offset($offset)
    {
        if (!$this->queries['offset']) { //only one allowed
            $this->queries['offset'] = " OFFSET " . $offset;
        }
        return $this;
    }

    /**
     * set _table var value
     * @param  string $table the table name
     * @return object - DBContent
     */
    public function table($table)
    {
        $this->clean();
        $this->_table = $table;
        return $this;
    }

    /**
     * make join between tables
     * @param string $table
     * @param array $condition
     * @param string $join
     * @return $this
     */

    /**
     * How to use :
     * 
     * $db->table("blog")->join("comments", ["comments.id", "=", blog.id], "left");
     *
     * sql = SELECT * FROM blog LEFT JOIN comments ON comments.id = blog.id
     */
    public function join($table, $condition = [], $join = '')
    {
        // make sure the $condition has 3 indexes (`table_one.field`, operator, `table_tow.field`)
        if (count($condition) == 3)
            $this->queries['join'][] = strtoupper($join) . // convert $join to upper case (left -> LEFT)
                    " JOIN {$table} ON {$condition[0]} {$condition[1]} {$condition[2]}";
        $this->_isJoin = TRUE; //is join then change to PDO::FETCH_NUM to avoid column colission

        return $this;
    }

    public function orderBy($field, $position = 'ASC')
    {
        $position = strtoupper($position);
        $this->queries['orderby'][] = "$field $position";
        return $this;
    }

    /**
     * This will return if any error happened
     * return _error variable
     * @return bool
     */
    public function error()
    {
        return $this->_error;
    }

    public function results()
    {
        return $this->_results;
    }

    public function lastInsertId()
    {
        return $this->_pdo->lastInsertId();
    }

    public function typeQuery($sql)
    {
        $sql = strtoupper($sql);
        if (strpos($sql, "SELECT") !== FALSE) {
            $this->_typeQuery = "SELECT";
        } else {
            $this->_typeQuery = "OTRO";
        }
    }

    public function quote($string)
    {
        return $this->_pdo->quote($string);
    }

    public function rowCount()
    {
        return count($this->select());
    }

    /**
     * Constructs the query
     */
    public function contructQuery()
    {
        $query = '';
        if ($this->queries['join'] && count($this->queries['join'])) {
            $query .= implode(" ", $this->queries['join']);
        }
        if ($this->queries['where'] && count($this->queries['where'])) {
            $query .= ' WHERE ' . implode(" AND ", $this->queries['where']);
        }
        if ($this->queries['orwhere'] && count($this->queries['orwhere'])) {
            $query .= ' OR ' . implode(" OR ", $this->queries['orwhere']);
        }
        if ($this->queries['orderby'] && count($this->queries['orderby'])) {
            $query .= ' ORDER BY ' . implode(', ', $this->queries['orderby']);
        }

        if ($this->queries['limit']) {
            $query .= $this->queries['limit'];
        }
        if ($this->queries['offset']  && $this->_typeQuery == 'SELECT') {
            $query .= $this->queries['offset'];
        }

        return $query;
    }

    /**
     * Clean some variables
     */
    public function clean()
    {
        $this->_results = NULL;
        $this->queries['where'] = [];
        $this->queries['orwhere'] = [];
        $this->queries['orderby'] = [];
        $this->queries['join'] = [];
        $this->queries['limit'] = '';
        $this->queries['offset'] = '';
        $this->table = '';
    }

    /**
     * Show columns from a table
     * @param type $tableName
     * @return 
     */
    public function desc($tableName)
    {
        $sql = "select COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS 
                where TABLE_NAME = '$tableName'";
        return $this->_pdo->exec($sql);
    }

}
