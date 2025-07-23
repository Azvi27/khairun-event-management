<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Production Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration khusus untuk environment production
    |
    */

    'security' => [
        'force_https' => env('FORCE_HTTPS', true),
        'hsts_max_age' => env('HSTS_MAX_AGE', 31536000), // 1 year
        'csrf_token_lifetime' => env('CSRF_TOKEN_LIFETIME', 3600), // 1 hour
        'session_secure' => env('SESSION_SECURE', true),
        'session_http_only' => env('SESSION_HTTP_ONLY', true),
        'session_same_site' => env('SESSION_SAME_SITE', 'strict'),
    ],

    'performance' => [
        'opcache_enabled' => env('OPCACHE_ENABLED', true),
        'query_log_enabled' => env('QUERY_LOG_ENABLED', false),
        'slow_query_threshold' => env('SLOW_QUERY_THRESHOLD', 2000), // ms
    ],

    'monitoring' => [
        'health_check_enabled' => env('HEALTH_CHECK_ENABLED', true),
        'error_reporting_enabled' => env('ERROR_REPORTING_ENABLED', true),
        'performance_monitoring' => env('PERFORMANCE_MONITORING', true),
    ],

    'backup' => [
        'enabled' => env('BACKUP_ENABLED', true),
        'schedule' => env('BACKUP_SCHEDULE', '0 2 * * *'), // Daily at 2 AM
        'retention_days' => env('BACKUP_RETENTION_DAYS', 30),
    ],
]; 