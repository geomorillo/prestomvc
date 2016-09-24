<?php

/**
 * Easily handles ajax requests.
 * @author Jhobanny Morillo geomorillo@yahoo.com
 * @version 1.3.1
 */

namespace system\http;
use system\http\Request;
use system\http\Response;
class AjaxHandler {
    
    private static $timers = array();
    private static $callback;
    private static $request, $responser;
    private static $lazy;
    private static $initialized;
    private static $requestType;

    /**
     * "Automagically" load init function
     */
    public static function __callStatic($method, $arguments) {

        if (method_exists(get_class(), 'x_' . $method)) {
            if (!self::$initialized) {
                self::init();
            }
            return call_user_func_array(array(get_class(), 'x_' . $method), $arguments);
        }
    }

    /**
     * Initializer function
     * @constructor
     * @param array $request $_GET, $_POST, $_REQUEST or and associative array as a request parameters if empty $_REQUEST will be used
     * @param array array of URL folders.
     */
    private static function init() {
        self::$initialized = true;
        self::$request = new Request();
        self::$responser = new Response();
        # Keep request time
        self::timerStart("Request");
        # if this is a JSONP request than use callback function
        self::$callback = self::$request->getQuery("callback") !==FALSE ? self::$request->getQuery("callback") : false;
        self::$callback = self::$request->getQuery("callbackName")!==FALSE ? self::$request->getQuery("callbackName") : self::$callback;
              
        # Request Type GET | PUT | POST | DELETE
        self::$requestType = self::$request->getMethod();
        # Define as seconds (ie: 0.5, 2, 0.02)
        self::$lazy = 0;
        # If lazy was send in the request overwrite the hard coded one
        if (self::$request->getQuery('lazy')!== FALSE) {
            self::$lazy = self::$request->getQuery('lazy');
        }
        # Define Error Handler
        set_error_handler(array(get_class(), "errorHandler"));
    }

    /**
     * Starts the timer for given title
     * @param object $title
     * @return
     */
    protected static function timerStart($title) {
        self::$timers[$title] = microtime(true);
    }

    /**
     * Brings back the result of time spending in seconds with floating point of milli seconds
     * Title must be exact same of the start functon
     * @param object $title
     * @return
     */
    protected static function timerEnd($title) {
        $end = microtime(true);
        return sprintf("%01.4f", ($end - self::$timers[$title]));
    }

    /**
     * Safely brings data from request. No need to use isset
     * It also converts "true" "false" strings to boolean
     * @param object $key
     * @return
     */
    public static function x_get($key) {
        if (self::$request->getQuery($key)==FALSE) {
            return NULL;
        }
        $val = self::$request->getQuery($key);
        if (!is_array($val)) {
            if (strtolower($val) == "true") {
                $val = true;
            }
            if (strtolower($val) == "false") {
                $val = false;
            }
        }
        return $val;
    }

    /**
     * Catches any error and responses with success:false
     * @param object $errno
     * @param object $message
     * @param object $filename
     * @param object $line
     */
    private static function errorHandler($errno, $message, $filename, $line) {
        if (error_reporting() == 0) {
            return;
        }
        if ($errno & (E_ALL ^ E_NOTICE)) {
            $types = array(1 => 'error', 2 => 'warning', 4 => 'parse error', 8 => 'notice', 16 => 'core error', 32 => 'core warning', 64 => 'compile error', 128 => 'compile warning', 256 => 'user error', 512 => 'user warning', 1024 => 'user notice', 2048 => 'strict warning');
            $entry = "<div style='text-align:left;'><span><b>" . @$types[$errno] . "</b></span>: $message <br><br>
                <span> <b>in</b> </span>: $filename <br>
                <span> <b>on line</b> </span>: $line </div>";

            error_log("Request Server Error:" . $message . "\nFile:" . $filename . "\nOn Line: " . $line);
            self::x_error($entry, 500);
        }
    }

    /**
     * Prompts a standard error response, all errors must prompt by this function
     * adds success:false automatically
     * @param object|string $message An error message, you can directly pass all parameters here
     * @param int $status Http status optional
     */
    public static function x_error($message, $status = 400) {
        $addHash["error"] = $message;
        $addHash["success"] = false;
        self::response($addHash, $status);
    }

    /**
     * Prompts the request response by given hash
     * adds standard success:true message automatically
     * @param object|string $message Success message you can also pass the all parameters as an array here
     * @param int $status Http status optional
     */
    public static function x_success($message, $status = 200) {
        $addHash["message"] = $message;
        $addHash["success"] = true;
        self::response($addHash, $status);
    }

    /**
     * Returns the type of the request
     * @return string GET | PUT | POST | DELETE
     */
    public static function x_getType() {
        return self::$requestType;
    }

    /**
     * Handles the response for both success and error methods
     * @param array $addHash
     * @param type $status Status
     */
    private static function response($addHash, $status) {
        $addHash["duration"] = self::timerEnd("Request");
        self::$responser->set_status($status);
        self::$responser->sendJSON();
        if (self::$callback) {
            $response = self::$callback . "(" . json_encode($addHash) . ");";
        } else {
            $response = json_encode($addHash);
        }
        echo $response;
        exit;
    }

}
