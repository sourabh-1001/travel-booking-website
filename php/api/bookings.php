<?php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

require_post_csrf();
enforce_rate_limit('bookings_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), 300, 8);

if (isset($_POST['contact'])) {
    $name = trim($_POST['contact_name'] ?? '');
    $email = trim($_POST['contact_email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
        json_response(['error' => 'Invalid contact form'], 422);
    }

    $stmt = db()->prepare('INSERT INTO inquiries (contact_name, contact_email, message) VALUES (?, ?, ?)');
    $stmt->execute([$name, $email, $message]);

    json_response(['message' => 'Inquiry received. We will contact you soon.']);
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$tour = trim($_POST['tour'] ?? '');
$travelDate = $_POST['travel_date'] ?? '';
$guests = (int) ($_POST['guests'] ?? 0);
$price = (float) ($_POST['price'] ?? 0);

if (
    $name === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $phone === '' ||
    $tour === '' ||
    $guests < 1
) {
    json_response(['error' => 'Invalid booking data'], 422);
}

$tourExists = db()->prepare('SELECT COUNT(*) AS total FROM tours WHERE title = ?');
$tourExists->execute([$tour]);
$tourCount = (int) ($tourExists->fetch()['total'] ?? 0);
if ($tourCount < 1) {
    json_response(['error' => 'Selected tour is not available'], 422);
}

$stmt = db()->prepare('INSERT INTO bookings (customer_name, customer_email, phone, tour_name, guests, travel_date, status, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([$name, $email, $phone, $tour, $guests, $travelDate ?: null, 'pending', $price > 0 ? $price : null]);

json_response(['message' => 'Booking request submitted']);
