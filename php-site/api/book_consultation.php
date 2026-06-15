<?php
// php-site/api/book_consultation.php

header('Content-Type: application/json');
require_once '../config/db.php';

$name = trim(isset($_POST['name']) ? $_POST['name'] : '');
$email = trim(isset($_POST['email']) ? $_POST['email'] : '');
$booking_date = trim(isset($_POST['booking_date']) ? $_POST['booking_date'] : '');
$topic = trim(isset($_POST['topic']) ? $_POST['topic'] : '');
$advocate_id = trim(isset($_POST['advocate_id']) ? $_POST['advocate_id'] : '');

if (empty($name) || empty($email) || empty($booking_date) || empty($topic) || empty($advocate_id)) {
    echo json_encode(['success' => false, 'message' => 'Please complete all required fields.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO consultations (name, email, booking_date, topic, advocate_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $booking_date, $topic, $advocate_id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
