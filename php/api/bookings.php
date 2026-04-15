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

$stmt = db()->prepare('
    INSERT INTO bookings (customer_name, customer_email, phone, tour_name, guests, travel_date, status, price)
    VALUES (:name, :email, :phone, :tour, :guests, :travel_date, :status, :price)
');
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
$stmt->bindValue(':tour', $tour, PDO::PARAM_STR);
$stmt->bindValue(':guests', $guests, PDO::PARAM_INT);
$stmt->bindValue(':travel_date', $travelDate ?: null, $travelDate ? PDO::PARAM_STR : PDO::PARAM_NULL);
$stmt->bindValue(':status', 'pending', PDO::PARAM_STR);
$stmt->bindValue(':price', $price > 0 ? $price : null, $price > 0 ? PDO::PARAM_STR : PDO::PARAM_NULL);
$stmt->execute();

json_response(['message' => 'Booking request submitted']);
