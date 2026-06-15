<?php
// php-site/api/connect_agent.php

header('Content-Type: application/json');
require_once '../config/db.php';

$agent_id = trim(isset($_POST['agent_id']) ? $_POST['agent_id'] : '');
$name = trim(isset($_POST['name']) ? $_POST['name'] : '');
$phone = trim(isset($_POST['phone']) ? $_POST['phone'] : '');
$message = trim(isset($_POST['message']) ? $_POST['message'] : '');

if (empty($agent_id) || empty($name) || empty($phone) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all details.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO agent_connections (agent_id, name, phone, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$agent_id, $name, $phone, $message]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
