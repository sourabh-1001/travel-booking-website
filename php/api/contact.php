<?php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

require_post_csrf();
enforce_rate_limit('contact_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), 300, 5);

$name = trim($_POST['name'] ?? ($_POST['contact_name'] ?? ''));
$email = trim($_POST['email'] ?? ($_POST['contact_email'] ?? ''));
$message = trim($_POST['message'] ?? '');

if ($name === '') {
    json_response(['error' => 'Name is required'], 422);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['error' => 'Valid email is required'], 422);
}
if ($message === '') {
    json_response(['error' => 'Message is required'], 422);
}

$stmt = db()->prepare('INSERT INTO inquiries (contact_name, contact_email, message) VALUES (?, ?, ?)');
$stmt->execute([$name, $email, $message]);

json_response(['message' => 'Inquiry received. We will contact you soon.']);
