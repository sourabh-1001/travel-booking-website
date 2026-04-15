<?php
$settings = require __DIR__ . '/../../config/settings.php';
$publicSettings = [
    'site_name' => $settings['site_name'] ?? '',
    'whatsapp_url' => $settings['whatsapp_url'] ?? '',
    'google_reviews_url' => $settings['google_reviews_url'] ?? '',
    'tripadvisor_url' => $settings['tripadvisor_url'] ?? '',
];

header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['data' => $publicSettings]);
