<?php
// php-site/api/auth.php

header('Content-Type: application/json');
require_once '../config/db.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'login') {
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $role = trim(isset($_POST['role']) ? $_POST['role'] : 'buyer');

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
        exit;
    }

    try {
        // Look up user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Check password
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'avatar' => $user['avatar']
                ];
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
            }
        } else {
            // Dynamic auto-registration on first login
            $name = explode('@', $email)[0];
            $name = ucwords(str_replace(['.', '_'], ' ', $name));
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            
            // Random avatar
            $avatar = 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&q=80';
            if ($role === 'seller') {
                $avatar = 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=100&q=80';
            } elseif ($role === 'admin') {
                $avatar = 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=100&q=80';
            }

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, avatar) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_pass, $role, $avatar]);
            
            $userId = $pdo->lastInsertId();

            $_SESSION['user'] = [
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'avatar' => $avatar
            ];

            echo json_encode(['success' => true]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }

} elseif ($action === 'oauth') {
    // Simulated Google SSO / QR Code Login
    $name = trim(isset($_POST['name']) ? $_POST['name'] : 'Sayali Patil');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : 'sayali.patil@outlook.com');
    $role = trim(isset($_POST['role']) ? $_POST['role'] : 'buyer');

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'avatar' => $user['avatar']
            ];
            echo json_encode(['success' => true]);
        } else {
            // Register new OAuth user
            $hashed_pass = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
            $avatar = 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80';
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, avatar) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_pass, $role, $avatar]);
            
            $userId = $pdo->lastInsertId();

            $_SESSION['user'] = [
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'avatar' => $avatar
            ];

            echo json_encode(['success' => true]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
