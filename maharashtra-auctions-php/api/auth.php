<?php
// api/auth.php

header('Content-Type: application/json');
require_once '../config/db.php';
require_once '../config/google.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';

// ─── Helper: build session array from DB row ──────────────────────────────────
function buildSession($user) {
    return [
        'id'                   => $user['id'],
        'name'                 => $user['name'],
        'email'                => $user['email'],
        'phone'                => $user['phone'] ?? '',
        'role'                 => $user['role'],
        'avatar'               => $user['avatar'],
        'subscription_ends_at' => $user['subscription_ends_at']
    ];
}

// ─── Helper: pick a default avatar by role ────────────────────────────────────
function pickAvatar($role) {
    if ($role === 'seller') return 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=100&q=80';
    if ($role === 'admin')  return 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=100&q=80';
    return 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&q=80';
}

// ─── Helper: get redirect URL based on role ─────────────────────────────────
function getRedirectUrl($role) {
    if ($role === 'seller') return 'seller_dashboard.php';
    if ($role === 'lawyer') return 'lawyer_dashboard.php';
    if ($role === 'admin') return 'admin_dashboard.php';
    return 'buyer_dashboard.php';
}

// ═══════════════════════════════════════════════════════════
// LOGIN
// ═══════════════════════════════════════════════════════════
if ($action === 'login') {
    $email    = trim(isset($_POST['email'])    ? $_POST['email']    : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please enter your email and password.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'No account found with this email. Please create an account first.']);
            exit;
        }

        if (!password_verify($password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Wrong password. Please try again.']);
            exit;
        }

        $_SESSION['user'] = buildSession($user);
        echo json_encode(['success' => true, 'redirect_url' => getRedirectUrl($user['role'])]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Server error. Please try again later.']);
    }

// ═══════════════════════════════════════════════════════════
// REGISTER (New Account)
// ═══════════════════════════════════════════════════════════
} elseif ($action === 'register') {
    $name     = trim(isset($_POST['name'])     ? $_POST['name']     : '');
    $email    = trim(isset($_POST['email'])    ? $_POST['email']    : '');
    $phone    = trim(isset($_POST['phone'])    ? $_POST['phone']    : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $role     = trim(isset($_POST['role'])     ? $_POST['role']     : 'buyer');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Please enter your name.']);
        exit;
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
        exit;
    }
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
        exit;
    }
    if (!in_array($role, ['buyer', 'seller'])) $role = 'buyer';

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'This email is already registered. Please login instead.']);
            exit;
        }

        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        $avatar      = pickAvatar($role);
        $sub_ends    = ($role === 'buyer') ? date('Y-m-d H:i:s', strtotime('+7 days')) : null;

        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, avatar, subscription_ends_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hashed_pass, $role, $avatar, $sub_ends]);
        $userId = $pdo->lastInsertId();

        $_SESSION['user'] = [
            'id'                   => $userId,
            'name'                 => $name,
            'email'                => $email,
            'phone'                => $phone,
            'role'                 => $role,
            'avatar'               => $avatar,
            'subscription_ends_at' => $sub_ends
        ];

        echo json_encode(['success' => true, 'redirect_url' => getRedirectUrl($role)]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Could not create account. Please try again.']);
    }

// ═══════════════════════════════════════════════════════════
// GOOGLE SIGN-IN (Real OAuth via Google Identity Services)
// ═══════════════════════════════════════════════════════════
} elseif ($action === 'google_signin') {
    $credential = isset($_POST['credential']) ? trim($_POST['credential']) : '';
    $role       = isset($_POST['role'])       ? trim($_POST['role'])       : 'buyer';

    if (empty($credential)) {
        echo json_encode(['success' => false, 'message' => 'Google sign-in failed. No token received.']);
        exit;
    }
    if (!in_array($role, ['buyer', 'seller'])) $role = 'buyer';

    // Verify the Google ID token using Google's public tokeninfo endpoint
    // This works without any PHP library — just a simple HTTP call
    $verify_url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($credential);

    $context  = stream_context_create(['http' => ['timeout' => 5]]);
    $response = @file_get_contents($verify_url, false, $context);

    if ($response === false) {
        echo json_encode(['success' => false, 'message' => 'Could not verify with Google. Check your internet and try again.']);
        exit;
    }

    $payload = json_decode($response, true);

    // Validate token fields
    if (empty($payload['email'])) {
        echo json_encode(['success' => false, 'message' => 'Google did not return your email. Please try again.']);
        exit;
    }
    if (empty($payload['email_verified']) || $payload['email_verified'] !== 'true') {
        echo json_encode(['success' => false, 'message' => 'Your Google account email is not verified.']);
        exit;
    }
    // Make sure the token is meant for our app
    if (GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID_HERE.apps.googleusercontent.com' &&
        $payload['aud'] !== GOOGLE_CLIENT_ID) {
        echo json_encode(['success' => false, 'message' => 'Google token is invalid for this app. Please try again.']);
        exit;
    }

    $google_email  = $payload['email'];
    $google_name   = isset($payload['name'])    ? $payload['name']    : explode('@', $google_email)[0];
    $google_avatar = isset($payload['picture']) ? $payload['picture'] : pickAvatar($role);

    try {
        // Check if user already has an account
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$google_email]);
        $user = $stmt->fetch();

        if ($user) {
            // Returning user — update their Google profile picture
            $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?")->execute([$google_avatar, $user['id']]);
            $user['avatar'] = $google_avatar;
            $_SESSION['user'] = buildSession($user);
            echo json_encode(['success' => true, 'is_new' => false, 'redirect_url' => getRedirectUrl($user['role'])]);
        } else {
            // New user — register them automatically using Google details
            $hashed_pass = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            $sub_ends    = ($role === 'buyer') ? date('Y-m-d H:i:s', strtotime('+7 days')) : null;

            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, avatar, subscription_ends_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$google_name, $google_email, '', $hashed_pass, $role, $google_avatar, $sub_ends]);
            $userId = $pdo->lastInsertId();

            $_SESSION['user'] = [
                'id'                   => $userId,
                'name'                 => $google_name,
                'email'                => $google_email,
                'phone'                => '',
                'role'                 => $role,
                'avatar'               => $google_avatar,
                'subscription_ends_at' => $sub_ends
            ];

            echo json_encode(['success' => true, 'is_new' => true, 'redirect_url' => getRedirectUrl($role)]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Could not save your account. Please try again.']);
    }

// ═══════════════════════════════════════════════════════════
// OAUTH / Legacy simulation (kept for backward compat)
// ═══════════════════════════════════════════════════════════
} elseif ($action === 'oauth') {
    $name  = trim(isset($_POST['name'])  ? $_POST['name']  : 'Guest User');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $role  = trim(isset($_POST['role'])  ? $_POST['role']  : 'buyer');

    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email is required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user'] = buildSession($user);
            echo json_encode(['success' => true, 'redirect_url' => getRedirectUrl($user['role'])]);
        } else {
            $hashed_pass = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
            $avatar      = pickAvatar($role);
            $sub_ends    = ($role === 'buyer') ? date('Y-m-d H:i:s', strtotime('+7 days')) : null;

            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, avatar, subscription_ends_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, '', $hashed_pass, $role, $avatar, $sub_ends]);
            $userId = $pdo->lastInsertId();

            $_SESSION['user'] = [
                'id'                   => $userId,
                'name'                 => $name,
                'email'                => $email,
                'phone'                => '',
                'role'                 => $role,
                'avatar'               => $avatar,
                'subscription_ends_at' => $sub_ends
            ];

            echo json_encode(['success' => true, 'redirect_url' => getRedirectUrl($role)]);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Server error. Please try again.']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
