<?php

namespace system\exceptions;

use system\core\Logger;
use system\core\LogException;

/**
 * Global Exception Handler for PrestoMVC
 */
class ExceptionHandler
{
    /**
     * Handle any uncaught exception
     */
    public static function handle(\Throwable $exception)
    {
        // Si es LogException, ya se logueó automáticamente
        if (!$exception instanceof LogException) {
            Logger::error('Uncaught exception: ' . $exception->getMessage(), [
                'exception' => $exception,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]);
        }

        // Handle based on exception type
        if ($exception instanceof HttpException) {
            self::handleHttpException($exception);
        } elseif ($exception instanceof ValidationException) {
            self::handleValidationException($exception);
        } elseif ($exception instanceof SecurityException) {
            self::handleSecurityException($exception);
        } elseif ($exception instanceof DatabaseException) {
            self::handleDatabaseException($exception);
        } else {
            self::handleGenericException($exception);
        }
    }

    /**
     * Handle HTTP exceptions
     */
    private static function handleHttpException(HttpException $exception)
    {
        http_response_code($exception->getHttpCode());
        echo $exception->getMessage();
    }

    /**
     * Handle validation exceptions
     */
    private static function handleValidationException(ValidationException $exception)
    {
        http_response_code($exception->getHttpCode());

        if (self::isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'errors' => $exception->getErrors(),
                'message' => $exception->getMessage()
            ]);
        } else {
            // Show validation errors in template
            echo "Validation errors: " . implode(', ', $exception->getErrors());
        }
    }

    /**
     * Handle security exceptions
     */
    private static function handleSecurityException(SecurityException $exception)
    {
        http_response_code($exception->getHttpCode());
        echo "Access denied";
    }

    /**
     * Handle database exceptions
     */
    private static function handleDatabaseException(DatabaseException $exception)
    {
        http_response_code($exception->getHttpCode());

        if (ENABLE_DEBUG) {
            echo "Database error: " . $exception->getMessage();
        } else {
            echo "A database error occurred. Please try again later.";
        }
    }

    /**
     * Handle generic exceptions
     */
    private static function handleGenericException(\Throwable $exception)
    {
        http_response_code(500);

        if (ENABLE_DEBUG) {
            echo "Error: " . $exception->getMessage();
        } else {
            echo "An error occurred. Please try again later.";
        }
    }

    /**
     * Check if request is AJAX
     */
    private static function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}