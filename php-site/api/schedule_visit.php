<?php
// php-site/api/schedule_visit.php

header('Content-Type: application/json');
require_once '../config/db.php';

$property_id = trim(isset($_POST['property_id']) ? $_POST['property_id'] : '');
$visit_date = trim(isset($_POST['visit_date']) ? $_POST['visit_date'] : '');
$time_slot = trim(isset($_POST['time_slot']) ? $_POST['time_slot'] : '');
$phone = trim(isset($_POST['phone']) ? $_POST['phone'] : '');
$agent_id = trim(isset($_POST['agent_id']) ? $_POST['agent_id'] : '');

if (empty($property_id) || empty($visit_date) || empty($time_slot) || empty($phone) || empty($agent_id)) {
    echo json_encode(['success' => false, 'message' => 'Please provide all details.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO site_visits (property_id, visit_date, time_slot, phone, agent_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$property_id, $visit_date, $time_slot, $phone, $agent_id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
