<?php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

require_post_csrf();

$allowedTours = [
    'Same-day Taj Mahal by Car',
    'Same-day Taj Mahal by Train',
    'Taj Mahal Sunrise/Sunset Tour',
    'Taj Mahal Overnight Luxury',
    'Delhi Half-day Heritage',
    'Delhi Full-day Explorer',
    'Delhi Spiritual & Cultural',
    'Delhi Night Market Tour',
    'Golden Triangle 3-Day',
    'Golden Triangle 5-Day',
    'Jaipur City Tour',
    'Agra City Tour',
    'Spiritual Tours',
    'Cultural Village Tours',
    'Neemrana Heritage Fort',
    'Vrindavan & Mathura',
    'Honeymoon Packages',
    'Rajasthan Grand 8-Day',
    'Jaisalmer Desert Safari',
    'Udaipur Luxury',
    'Jodhpur Blue City',
    'Ranthambore Tiger Safari',
    'Pushkar Camel Fair',
    'Uttarakhand 5-Day',
    'Himachal Pradesh 6-Day',
    'Jammu & Kashmir 7-Day',
];

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
$tour = trim($_POST['tour'] ?? '');
$travelDate = $_POST['travel_date'] ?? '';
$guests = (int) ($_POST['guests'] ?? 0);

if (
    $name === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $tour === '' ||
    $guests < 1 ||
    !in_array($tour, $allowedTours, true)
) {
    json_response(['error' => 'Invalid booking data'], 422);
}

$stmt = db()->prepare('INSERT INTO bookings (customer_name, customer_email, tour_name, guests, travel_date, status) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([$name, $email, $tour, $guests, $travelDate ?: null, 'pending']);

json_response(['message' => 'Booking request submitted']);
