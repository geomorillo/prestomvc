<?php

namespace system\exceptions;

use system\core\LogException;

/**
 * Validation Exception for handling form validation errors
 */
class ValidationException extends LogException
{
    protected $httpCode = 400;
    protected $errors = [];

    public function __construct(array $errors, $message = "Validation failed")
    {
        $this->errors = $errors;
        parent::__construct($message);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}