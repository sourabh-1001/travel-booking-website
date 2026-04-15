<?php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = db()->query('SELECT * FROM reviews WHERE approved = 1 ORDER BY created_at DESC');
    json_response(['data' => $stmt->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    enforce_rate_limit('reviews_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), 300, 5);

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $rating = (int) ($_POST['rating'] ?? 0);
    $review = trim($_POST['review'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $rating < 1 || $rating > 5 || $review === '') {
        json_response(['error' => 'Invalid input'], 422);
    }

    $photoPath = null;
    if (isset($_FILES['photo']) && is_array($_FILES['photo']) && ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            json_response(['error' => 'Photo upload failed'], 422);
        }

        $tmpPath = $_FILES['photo']['tmp_name'];
        $mime = mime_content_type($tmpPath) ?: '';
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($allowed[$mime])) {
            json_response(['error' => 'Unsupported photo format'], 422);
        }

        $uploadDir = __DIR__ . '/../../uploads/reviews';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            json_response(['error' => 'Unable to store uploaded photo'], 500);
        }
        $htaccessFile = $uploadDir . '/.htaccess';
        if (!file_exists($htaccessFile)) {
            file_put_contents($htaccessFile, "Options -ExecCGI\nAddType text/plain .php .phtml .php3 .php4 .php5 .phar\n");
        }

        $filename = 'review_' . bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
        $destination = $uploadDir . '/' . $filename;
        if (!move_uploaded_file($tmpPath, $destination)) {
            json_response(['error' => 'Unable to save uploaded photo'], 500);
        }
        $photoPath = 'uploads/reviews/' . $filename;
    }

    $duplicateStmt = db()->prepare('SELECT COUNT(*) AS total FROM reviews WHERE guest_email = ? AND review_text = ?');
    $duplicateStmt->execute([$email, $review]);
    if ((int) ($duplicateStmt->fetch()['total'] ?? 0) > 0) {
        json_response(['error' => 'Duplicate review detected'], 409);
    }

    $stmt = db()->prepare('INSERT INTO reviews (guest_name, guest_email, rating, review_text, photo_path, approved) VALUES (?, ?, ?, ?, ?, 0)');
    $stmt->execute([$name, $email, $rating, $review, $photoPath]);

    json_response(['message' => 'Review submitted for moderation']);
}

json_response(['error' => 'Method not allowed'], 405);
