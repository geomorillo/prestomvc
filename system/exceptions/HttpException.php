<?php

namespace system\exceptions;

use system\core\LogException;

/**
 * HTTP Exception for handling HTTP errors
 */
class HttpException extends LogException
{
    public function __construct($message = "", $httpCode = 500, $code = 0)
    {
        $this->httpCode = $httpCode;
        parent::__construct($message, $code);
    }
}