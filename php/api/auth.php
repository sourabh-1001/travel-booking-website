<?php
require_once __DIR__ . '/../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'POST' && $action === 'register') {
    require_post_csrf();

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        json_response(['error' => 'Invalid registration data'], 422);
    }

    $checkStmt = db()->prepare('SELECT id FROM users WHERE email = ?');
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        json_response(['error' => 'Email already registered'], 409);
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare('INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $email, $passwordHash, 'user']);

    json_response(['message' => 'Registration successful']);
}

if ($method === 'POST' && $action === 'login') {
    require_post_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        json_response(['error' => 'Invalid login data'], 422);
    }

    $stmt = db()->prepare('SELECT id, full_name, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        json_response(['error' => 'Invalid credentials'], 401);
    }

    ensure_session();
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_role'] = $user['role'];

    json_response([
        'message' => 'Login successful',
        'data' => [
            'id' => (int) $user['id'],
            'name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ],
    ]);
}

if ($method === 'POST' && $action === 'logout') {
    ensure_session();
    $_SESSION = [];
    session_destroy();
    json_response(['message' => 'Logout successful']);
}

if ($method === 'GET' && $action === 'profile') {
    ensure_session();
    if (empty($_SESSION['user_id'])) {
        json_response(['error' => 'Unauthorized'], 401);
    }

    $stmt = db()->prepare('SELECT id, full_name, email, role FROM users WHERE id = ?');
    $stmt->execute([(int) $_SESSION['user_id']]);

    json_response(['data' => $stmt->fetch()]);
}

json_response(['error' => 'Unsupported auth action'], 400);
