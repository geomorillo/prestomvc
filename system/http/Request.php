<?php

namespace system\http;

/**
 * Description of Request
 *
 * @author Daniel Navarro Ramírez7
 * @author Jhobanny Morillo
 */
class Request
{

    /**
     *
     * @var string Requested URL
     */
    private $url;

    /**
     *
     * @var string Main directory 
     */
    private $basePath;

    /**
     *
     * @var string Request method
     */
    private $method;

    /**
     *
     * @var string Content type
     */
    private $type;

    /**
     *
     * @var array Query strings of parameters
     */
    private $query = array();

    /**
     *
     * @var string Upload files
     */
    private $files;

    /**
     *
     * @var string Address IP of Cliente
     */
    private $ip;

    /**
     *
     * @var string AJAX Request
     */
    public $ajax;

    /**
     *
     * @var string Browser information
     */
    private $userAgente;

    /**
     *
     * @var array $_POST data
     */
    public $data;

    /**
     *
     * @var array Storage Server data 
     */
    private $env;

    /**
     * 
     */
    public function __construct()
    {
        $this->env = $_SERVER + $_ENV;

        if (isset($this->env['HTTP_USER_AGENT'])) {
            $this->userAgente = $this->env['HTTP_USER_AGENT'];
        }

        if (isset($this->env['CONTENT_TYPE'])) {
            $this->type = $this->env['CONTENT_TYPE'];
        } else {
            $this->type = "text/html";
        }

        // Setting Ip
        if (isset($this->env['HTTP_CLIENT_IP'])) {
            $this->ip = $this->env['HTTP_CLIENT_IP'];
        } else if (isset($this->env['HTTP_X_FORWARDED_FOR'])) {
            $this->ip = $this->env['HTTP_X_FORWARDED_FOR'];
        } else if (isset($this->env['HTTP_X_FORWARDED'])) {
            $this->ip = $this->env['HTTP_X_FORWARDED'];
        } else if (isset($this->env['HTTP_FORWARDED_FOR'])) {
            $this->ip = $this->env['HTTP_FORWARDED_FOR'];
        } else if (isset($this->env['HTTP_FORWARDED'])) {
            $this->ip = $this->env['HTTP_FORWARDED'];
        } else if (isset($this->env['REMOTE_ADDR'])) {
            $this->ip = $this->env['REMOTE_ADDR'];
        } else {
            $this->ip = 'UNKNOWN';
        }

        if (isset($this->env['HTTP_X_REQUESTED_WITH']) && $this->env['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->ajax = $this->env['HTTP_X_REQUESTED_WITH'];
        }
        $this->method = $this->env['REQUEST_METHOD'];
        if ($this->is("post")) {
            foreach ($_POST as $key => $value) {
                $this->setQuery($key, $value);
            }
        }

        $requestUri = isset($this->env['PATH_INFO']) ? $this->env['PATH_INFO'] : $this->env['REQUEST_URI'];
        if ($this->is("get")) {
            $this->parseQuery($requestUri);
        }
        $requestUri = preg_replace('/\?.+/', '', $requestUri);

        $this->url = rtrim($requestUri, '/');

        if (empty($this->url)) {
            $this->url = '/';
        }

        $this->setUrl($this->url);
    }

    /**
     * Set variable and value for Query strings
     * 
     * @param type $key
     * @param type $value
     */
    public function setQuery($key, $value)
    {
        $this->query[$key] = $value;
    }

    /**
     * 
     * @param string $key
     * @return string with data
     */
    public function getQuery($key)
    {
        return isset($this->query[$key]) ? $this->query[$key] : FALSE;
    }

    /**
     * 
     * @return string With Broser Navigator Data
     */
    public function getUserAgent()
    {
        return $this->userAgente;
    }

    /**
     * 
     * @return string Return URL request
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * 
     * @param string $url To set URL to parse
     */
    public function setUrl($url)
    {
        $parsed = $this->parser($url);

        $this->url = $parsed['path'];
    }

    /**
     * 
     * @return string Ip addres of Cliente
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * 
     * @return string Return Content Type
     */
    public function getContentType()
    {
        return $this->type;
    }

    /**
     * 
     * @return string Base path from project
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    public function setPost($key)
    {
        if (!empty($key)) {
            return array_key_exists($key, $_POST) ? $_POST[$key] : null;
        }

        return isset($_POST) ? $_POST : null;
    }

    public function setGet($key)
    {
        if (!empty($key)) {
            return array_key_exists($key, $_GET) ? $_GET[$key] : null;
        }

        return isset($_GET) ? $_GET : null;
    }

    /**
     * 
     * @return string Method request
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Parse URL request
     * 
     * @param string $url URL to parse
     * @return array path and query from URL
     */
    public function parser($url)
    {
        $parse = ($url) ? parse_url($url) : parse_url($_SERVER['REQUEST_URI']);

        if (preg_match('/[a-zA-Z0-9_]+\.php/i', $parse['path'], $matches)) {
            $parse['path'] = preg_replace("/$matches[0]/", '/', $parse['path']);
        }

        return $parse;
    }

    /**
     * Parse Query Strings from URL passed. Finally storage the queries on $key and $value
     * Use getQuery($key) to return the $value
     * 
     * @param string $url Request Url
     * @return array Storage queries
     */
    public function parseQuery($url)
    {
        $this->query = array();

        $args = parse_url($url);

        if (isset($args['query'])) {
            parse_str($args['query'], $this->query);
        }

        foreach ($this->query as $key => $value) {
            $this->setQuery($key, $value);
        }

        return $this->query;
    }

    /**
     * Return true given the type
     *  
     * @param string $type Method Request
     * @return boolean Method
     */
    public function is($type)
    {
        switch (strtolower($type)) {
            case "ajax":
                return ($this->ajax);
            case "get":
                return ($this->method == "GET");
            case "post":
                return ($this->method == "POST");
            case "put":
                return ($this->method == "PUT");
            case "delete":
                return ($this->method == "DELETE");
            default:
                return false;
        }
    }

}
