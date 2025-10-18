<?php

namespace system\exceptions;

use system\core\LogLevel;
use system\exceptions\HttpException;

/**
 * Security Exception for handling security violations
 */
class SecurityException extends HttpException
{
    protected $logLevel = LogLevel::WARNING;

    public function __construct($message = "Security violation", $httpCode = 403)
    {
        parent::__construct($message, $httpCode);
    }
}