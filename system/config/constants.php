<?php

/*
 * System Constants - Centralized configuration values
 * These constants replace hardcoded values throughout the framework
 */

// Database Constants
define('DB_CHARSET', 'utf8');
define('DB_COLLATION', 'utf8_general_ci');

// Session Constants
define('SESSION_TIMEZONE', 'America/Bogota');
define('SESSION_DURATION_HOURS', 1);

// Cache Constants
define('CACHE_DEFAULT_DIR', 'cache');
define('CACHE_FILE_EXTENSION', '.cache');

// Security Constants
define('CSRF_TOKEN_LENGTH', 32);

// Logging Constants
define('LOG_MAX_FILE_SIZE', 10485760); // 10MB
define('LOG_ROTATION_COUNT', 5);