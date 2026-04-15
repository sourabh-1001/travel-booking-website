<?php
require_once __DIR__ . '/../config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        return $pdo;
    } catch (Throwable $e) {
        error_log('Database connection failed.');
        json_response(['error' => 'Database unavailable'], 500);
    }
}

function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data);
    exit;
}

function ensure_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function is_same_origin_request(): bool
{
    $appHost = parse_url(APP_URL, PHP_URL_HOST);
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';

    if ($origin) {
        return parse_url($origin, PHP_URL_HOST) === $appHost;
    }

    if ($referer) {
        return parse_url($referer, PHP_URL_HOST) === $appHost;
    }

    return false;
}

function require_post_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    ensure_session();
    $requestToken = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

    if (!empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $requestToken)) {
        return;
    }

    if (!is_same_origin_request()) {
        json_response(['error' => 'Invalid CSRF token'], 403);
    }
}

function enforce_rate_limit(string $key, int $windowSeconds, int $maxAttempts): void
{
    ensure_session();
    $now = time();
    $bucketKey = 'rl_' . $key;

    if (!isset($_SESSION[$bucketKey]) || !is_array($_SESSION[$bucketKey])) {
        $_SESSION[$bucketKey] = [];
    }

    $_SESSION[$bucketKey] = array_values(array_filter(
        $_SESSION[$bucketKey],
        static fn ($ts) => is_int($ts) && ($now - $ts) < $windowSeconds
    ));

    if (count($_SESSION[$bucketKey]) >= $maxAttempts) {
        json_response(['error' => 'Too many requests. Please try again later.'], 429);
    }

    $_SESSION[$bucketKey][] = $now;
}

function require_admin_user(): int
{
    ensure_session();
    $role = $_SESSION['user_role'] ?? '';
    $userId = (int) ($_SESSION['user_id'] ?? 0);

    if ($role !== 'admin' || $userId < 1) {
        json_response(['error' => 'Unauthorized'], 401);
    }

    return $userId;
}
