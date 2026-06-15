<?php
// php-site/api/submit_lead.php

header('Content-Type: application/json');
require_once '../config/db.php';

$campaign = trim(isset($_POST['campaign']) ? $_POST['campaign'] : 'General Portal');
$name = trim(isset($_POST['name']) ? $_POST['name'] : '');
$email = trim(isset($_POST['email']) ? $_POST['email'] : '');

if (empty($name) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Please provide both name and email.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO leads (campaign, name, email) VALUES (?, ?, ?)");
    $stmt->execute([$campaign, $name, $email]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
