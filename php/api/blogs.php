<?php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = db()->query('SELECT * FROM blogs ORDER BY published_at DESC');
    json_response(['data' => $stmt->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $authorId = require_admin_user();

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '' || $content === '') {
        json_response(['error' => 'Missing fields'], 422);
    }

    $stmt = db()->prepare('INSERT INTO blogs (title, content, category, featured_image, author_id) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$title, $content, $_POST['category'] ?? 'Travel Tips', $_POST['featured_image'] ?? '', $authorId]);

    json_response(['message' => 'Blog created']);
}

json_response(['error' => 'Method not allowed'], 405);
