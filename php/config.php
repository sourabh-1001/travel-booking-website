<?php
$envPath = __DIR__ . '/../config/.env';
if (!file_exists($envPath)) {
    $envPath = __DIR__ . '/../config/.env.example';
}
$env = parse_ini_file($envPath, false, INI_SCANNER_TYPED) ?: [];

define('DB_HOST', $env['DB_HOST'] ?? '127.0.0.1');
define('DB_NAME', $env['DB_NAME'] ?? 'travel_booking');
define('DB_USER', $env['DB_USER'] ?? 'root');
define('DB_PASS', $env['DB_PASS'] ?? '');
define('APP_URL', $env['APP_URL'] ?? 'http://localhost');
