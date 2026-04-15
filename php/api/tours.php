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

json_response(['data' => db()->query('SELECT * FROM tours ORDER BY id DESC')->fetchAll()]);
