<?php

use Tracy\IBarPanel;
use Tracy\Debugger;

class NotOrmTracyPanel implements IBarPanel
{

    /** @var int */
    static public $maxQueries = 200;

    /** @var int maximum SQL length */
    static public $maxLength = 500;

    /** @var int */
    private $count = 0;

    /** @var array */
    private $queries = array();

    /** @var NotOrmTracyPanel singleton instance */
    private static $instance;

    /** @var string */
    private $platform = '';

    /** @var int */
    private $total_time = 0;

    /** @var bool */
    public $disabled = false;

    /** @var \NotORM */
    private $notorm;

    /** @var PDO */
    private $pdo;

    /** @var bool|string explain queries? */
    public $explain = true;

    public static function simpleInit(\NotORM $notorm, PDO $pdo = null)
    {
        $self = self::getInstance();
        $self->setNotOrm($notorm);

        if ($pdo) {
            $self->setPdo($pdo);
        }

        $notorm->debug = function ($query, $parameters) use ($self) {
            $self->logQuery($query, $parameters);
            $self->startQueryTimer($self->getIndex());
        };

        $notorm->debugTimer = function () use ($self) {
            $self->stopQueryTimer($self->getIndex());
        };

        $self->setPlatform($pdo->getAttribute(PDO::ATTR_DRIVER_NAME));

        Debugger::getBar()->addPanel($self);
    }

    public function setNotOrm(\NotORM $notorm)
    {
        $this->notorm = $notorm;
    }

    public function getNotOrm()
    {
        return $this->notorm;
    }

    public function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPdo()
    {
        if (!$this->pdo && $this->notorm) {
            $notorm = new ReflectionProperty('\NotORM', 'connection');
            $notorm->setAccessible(true);
            $this->pdo = $notorm->getValue($this->notorm);
        }

        return $this->pdo;
    }

    public function getPlatform()
    {
        return $this->platform;
    }

    public function setPlatform($platform)
    {
        $this->platform = $platform;
    }

    public function __construct()
    {
        self::$instance = $this;
    }

    public static function getInstance()
    {
        return self::$instance ? : self::$instance = new self();
    }

    public function getId()
    {
        return 'NotORM';
    }

    public function getTab()
    {
        return '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAHpJREFUOMvVU8ENgDAIBON8dgY7yU3SHTohfoQUi7FGH3pJEwI9oBwl+j1YDRGR8AIzA+hiAIxLsoOW1R3zB9Cks1VKmaQWXz3wHWEJpBbilF3wivxKB9OdiUfDnJ6Q3RNGyWp3MraytbKqjADkrIvhPYgSDG3itz/TBsqre3ItA1W8AAAAAElFTkSuQmCC" />'
                . ($this->count . ' ' . ($this->count === 1 ? 'query' : 'queries'))
                . ($this->total_time ? sprintf(' / %0.3f ms', $this->total_time * 1000) : '');
    }

    /**
     * @return string HTML code for Debugbar detail
     */
    public function getPanel()
    {
        $this->disabled = true;

        if (!$this->count) {
            return null;
        }

        $s = '<style>';
        $s .= '#tracy-debug-panel-NotOrmTracyPanel table { width:100% }';
        $s .= '#tracy-debug-panel-NotOrmTracyPanel pre.tracy-dump span { color:#c16549 }';
        $s .= '#tracy-debug-panel-NotOrmTracyPanel .tracy-alt td.notorm-sql { background: #f5f5f5 }';
        $s .= '#tracy-debug #tracy-debug-panel-NotOrmTracyPanel td.notorm-sql { background: #fff }';
        $s .= '</style>';
        $s .= '<h1>'
                . (($this->count === 1 ? 'Query' : 'Queries') . ": $this->count")
                . ($this->total_time ? sprintf(' / %0.3f ms', $this->total_time * 1000) : '')
                . '</h1>';
        $s .= '<div class="tracy-inner">';
        $s .= '<table>';
        $s .= '<tr><th colspan="3">Connection Platform</th></tr>';
        $s .= '<tr><td colspan="3">' . $this->getPlatform() . '</td></tr>';
        $s .= '<tr><th>Time&nbsp;ms</th><th>SQL&nbsp;Statement</th><th>Params</th></tr>';

        if ($this->queries) {
            foreach ($this->queries as $query) {
                list($sql, $params, $time) = $query;

                $explain = null;
                if ($this->explain && preg_match('#\s*\(?\s*SELECT\s#iA', $sql) && ($connection = $this->getPdo())) {
                    try {
                        $cmd = is_string($this->explain) ? $this->explain : 'EXPLAIN';
                        $sth = $connection->prepare("$cmd $sql");
                        $sth->execute($params);
                        $explain = $sth->fetchAll();
                    } catch (\PDOException $e) {
                        
                    }
                }

                $s .= '<tr>';
                $s .= '<td>' . ($time ? sprintf('%0.3f', $time * 1000) : '');

                static $counter;

                if ($explain) {
                    $counter++;
                    $s .= "<br /><a class='tracy-toggle tracy-collapsed' href='#notorm-tracy-DbConnectionPanel-row-$counter'>explain</a>";
                }

                $s .= '</td>';
                $s .= '<td class="notorm-sql">' . self::dump($sql);

                if ($explain) {
                    $s .= "<table id='notorm-tracy-DbConnectionPanel-row-$counter' class='tracy-collapsed'><tr>";
                    foreach ($explain[0] as $col => $foo) {
                        $s .= '<th>' . htmlspecialchars($col) . '</th>';
                    }
                    $s .= '</tr>';
                    foreach ($explain as $row) {
                        $s .= '<tr>';
                        foreach ($row as $col) {
                            $s .= '<td>' . htmlspecialchars($col) . '</td>';
                        }
                        $s .= '</tr>';
                    }
                    $s .= '</table>';
                }

                $s .= '</td>';
                $s .= '<td>' . Debugger::dump($params, true) . '</td>';
                $s .= '</tr>';
            }
        } else {
            $s .= '<tr><td colspan="3">No SQL logs found</td></tr>';
        }

        $s .= '</table>';

        if (count($this->queries) < $this->count) {
            $s .= '<p>...and more</p>';
        }

        $s .= '</div>';

        return $s;
    }

    public function logQuery($query, $parameters)
    {
        if ($this->disabled) {
            return;
        }

        $this->count++;

        if ($this->count < self::$maxQueries) {
            $this->queries[$this->count - 1] = array($query, $parameters, null);
        }
    }

    public function startQueryTimer($index)
    {
        if (isset($this->queries[$index])) {
            Debugger::timer(__CLASS__ . ":$index");
        }
    }

    public function stopQueryTimer($index)
    {
        if (isset($this->queries[$index])) {
            $time = Debugger::timer(__CLASS__ . ":$index");
            $this->total_time += $time;
            $this->queries[$index][2] = $time;
        }
    }

    public function getCount()
    {
        return $this->count;
    }

    public function getIndex()
    {
        return $this->count - 1;
    }

    public static function dump($sql)
    {
        $keywords1 = 'CREATE\s+TABLE|CREATE(?:\s+UNIQUE)?\s+INDEX|SELECT|UPDATE|INSERT(?:\s+INTO)?|REPLACE(?:\s+INTO)?|DELETE|FROM|WHERE|HAVING|GROUP\s+BY|ORDER\s+BY|LIMIT|SET|VALUES|LEFT\s+JOIN|INNER\s+JOIN|TRUNCATE';
        $keywords2 = 'ALL|DISTINCT|DISTINCTROW|AS|USING|ON|AND|OR|IN|IS|NOT|NULL|LIKE|TRUE|FALSE|INTEGER|CLOB|VARCHAR|DATETIME|TIME|DATE|INT|SMALLINT|BIGINT|BOOL|BOOLEAN|DECIMAL|FLOAT|TEXT|VARCHAR|DEFAULT|AUTOINCREMENT|PRIMARY\s+KEY';

        // insert new lines
        $sql = " $sql ";
        $sql = static::findAndReplace($sql, "#(?<=[\\s,(])($keywords1)(?=[\\s,)])#", "\n\$1");
        if (strpos($sql, "CREATE TABLE") !== false)
            $sql = static::findAndReplace($sql, "#,\s+#i", ", \n");

        // reduce spaces
        $sql = static::findAndReplace($sql, '#[ \t]{2,}#', " ");

        $sql = wordwrap($sql, 100);
        $sql = htmlSpecialChars($sql);
        $sql = static::findAndReplace($sql, "#([ \t]*\r?\n){2,}#", "\n");
        $sql = static::findAndReplace($sql, "#VARCHAR\\(#", "VARCHAR (");

        // syntax highlight
        $sql = static::findAndReplace($sql, "#(/\\*.+?\\*/)|(\\*\\*.+?\\*\\*)|(?<=[\\s,(])($keywords1)(?=[\\s,)])|(?<=[\\s,(=])($keywords2)(?=[\\s,)=])#s", function ($matches) {
                    if (!empty($matches[1])) // comment
                        return '<em style="color:gray">' . $matches[1] . '</em>';

                    if (!empty($matches[2])) // error
                        return '<strong style="color:red">' . $matches[2] . '</strong>';

                    if (!empty($matches[3])) // most important keywords
                        return '<strong style="color:blue">' . $matches[3] . '</strong>';

                    if (!empty($matches[4])) // other keywords
                        return '<strong style="color:green">' . $matches[4] . '</strong>';
                }
        );
        $sql = trim($sql);

        return '<pre class="dump">' . $sql . "</pre>\n";
    }

    public static function findAndReplace($text, $params)
    {
        preg_match_all("/{(.*?)}/", $text, $matches);
        foreach ($matches[1] as $match) {
            $text = str_replace("{" . $match . "}", $params[$match], $text);
        }
        return $text;
    }

}
