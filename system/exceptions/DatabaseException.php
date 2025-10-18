<?php

namespace system\exceptions;

use system\core\LogLevel;
use system\core\LogException;

/**
 * Database Exception for handling database errors
 */
class DatabaseException extends LogException
{
    protected $logLevel = LogLevel::CRITICAL;

    public function __construct($message = "Database error", $code = 0)
    {
        parent::__construct($message, $code);
    }
}