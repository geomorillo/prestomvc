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
            $_query = '',
            $_results,
            $_count,
            $_where = "WHERE",
            $_typeQuery = '';
    protected $table;
    protected $config;

    public function __construct()
    {
        $this->config = include CONFIG_PATH . 'database_config.php';
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
     * DB::connect()
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
            $this->_query = "";
            $this->_where = "WHERE";
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
                $this->_results = $query->fetchAll($this->config["fetch"]);
                $this->_count = $query->rowCount();
            }
        } catch (LogException $e) {
            $this->_results = NULL;
            $this->_error = true;
            $e->logError();
        }
        return $this;
    }

    /**
     * DB::insert()
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

            $sql = "UPDATE {$this->_table} SET {$set} " . $this->_query;
            // check if query is not have an error
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
        $sql = "SELECT " . implode(', ', $fields) . " FROM {$this->_table} {$this->_query}";
        $this->_query = $sql;
        return $this->query($sql)->results();
    }

    /**
     * delete from table
     * @return bool
     */
    public function delete()
    {
        $this->_typeQuery = "DELETE";
        $sql = "DELETE FROM $this->_table " . $this->_query;
        $delete = $this->query($sql);
        if ($delete)
            return true;
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
        $this->_query = '';
        $this->_where = "WHERE";
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
        if (!is_numeric($value))
            $value = "'$value'";
        $this->_query .= " $this->_where $field $operator $value";
        $this->_where = "AND";
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
            $this->_query .= " $this->_where $field BETWEEN '$values[0]' and '$values[1]'";
            $this->_where = "AND";
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
        $this->_query .= " $this->_where $field LIKE '%$value%'";
        $this->_where = "AND";
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
        $this->_query .= " OR $field $operator '$value'";
        $this->_where = "AND";
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
            $this->_query .= " $this->_where $field IN (" . implode(",", $values) . ")";
            $this->_where = "AND";
        }
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
            $this->_query .= " $this->_where $field NOT IN (" . implode(",", $values) . ")";
            $this->_where = "AND";
        }
    }

    /**
     * get first row from query results
     * @return array
     */
    public function first($selectNew = true)
    {
        if ($selectNew === true)
            $first = $this->select();
        else
            $first = $this->results();
        if (count($first))
            return $first[0];

        return [];
    }

    /**
     * [limit description]
     * @param  [type] $limit [description]
     * @return [type]        [description]
     */
    public function limit($limit)
    {
        $this->_query .= " LIMIT " . (int) $limit;
        return $this;
    }

    /**
     * [offset description]
     * @param  [type] $offset [description]
     * @return [type]         [description]
     */
    public function offset($offset)
    {
        $this->_query .=" OFFSET " . $offset;
        return $this;
    }

    /**
     * set _table var value
     * @param  string $table the table name
     * @return object - DBContent
     */
    public function table($table)
    {
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
     * $db = PHPtricks\Database\Database::connect();
     * $db->table("blog")->join("comments", ["comments.id", "=", blog.id], "left");
     *
     * sql = SELECT * FROM blog LEFT JOIN comments ON comments.id = blog.id
     */
    public function join($table, $condition = [], $join = '')
    {
        // make sure the $condition has 3 indexes (`table_one.field`, operator, `table_tow.field`)
        if (count($condition) == 3)
            $this->_query .= strtoupper($join) . // convert $join to upper case (left -> LEFT)
                    " JOIN {$table} ON {$condition[0]} {$condition[1]} {$condition[2]}";

        // that's it now return object from this class
        return $this;
    }

    /**
     * view query results in table
     * we need to create a simple table to view results of query
     * @return string (html)
     */

    /**
     * How to Use:
     *
     * $db = PHPtricks\Database\Database::connect();
     * $db->table("blog")->where("vote", ">", 2)->select();
     * echo $db->dataView();
     */
    public function dataView()
    {
        // get columns count to create the table
        $colsCount = count($this->first(false));
        // if no data received so return no data found!
        if ($colsCount <= 0) {
            return config("pagination.no_data_found_message");
        }
        // get Columns name's
        $colsName = array_keys((array) $this->first(false));

        // init html <table> tag
        $html = "<table border=1><thead><tr>";

        /**
         * create table header
         * its contain table columns names
         */
        foreach ($colsName as $colName) {
            $html .= "<th>";
            $html .= $colName;
            $html .= "</th>";
        }

        // end table header tag and open table body tag
        $html .= "</tr></thead><tbody>";
        // loop all results to create the table (tr's and td's)
        foreach ((array) $this->results() as $row) {
            $row = (array) $row; // make sure the $row is array and not an object
            $html .= "<tr>"; // open tr tag
            // loop all columns in row to create <td>'s tags
            for ($i = 0; $i <= $colsCount + 1; $i++) {
                $html .= "<td>";
                $html .= $row[$colsName[$i]]; // get current data from the row
                $html .= "</td>";
            }
            $html .= "</tr>";
        }

        $html .= "</tbody></table>";

        return $html; // return created table
    }

    /**
     * DB::error()
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

    public function count()
    {
        return $this->_count;
    }

}
