<?php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['error' => 'Method not allowed'], 405);
}

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = db()->prepare('SELECT * FROM tours WHERE id = ?');
    $stmt->execute([$id]);
    json_response(['data' => $stmt->fetch()]);
}

$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = (int) ($_GET['per_page'] ?? 20);
if ($perPage < 1) {
    $perPage = 20;
}
if ($perPage > 100) {
    $perPage = 100;
}
$offset = ($page - 1) * $perPage;

$conditions = [];
$params = [];

$category = trim((string) ($_GET['category'] ?? ''));
if ($category !== '') {
    $conditions[] = 'category = :category';
    $params[':category'] = $category;
}

$destination = trim((string) ($_GET['destination'] ?? ''));
if ($destination !== '') {
    $conditions[] = 'destination = :destination';
    $params[':destination'] = $destination;
}

$where = $conditions ? ('WHERE ' . implode(' AND ', $conditions)) : '';
$stmt = db()->prepare("SELECT * FROM tours $where ORDER BY id DESC LIMIT :limit OFFSET :offset");
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

json_response([
    'data' => $stmt->fetchAll(),
    'pagination' => [
        'page' => $page,
        'per_page' => $perPage,
    ],
]);
