<?php
// db/migrate_subscription.php
require_once __DIR__ . '/../config/db.php';

try {
    // Check if column already exists
    $check = $pdo->query("SHOW COLUMNS FROM users LIKE 'subscription_ends_at'");
    $exists = $check->fetch();

    if (!$exists) {
        $pdo->exec("ALTER TABLE users ADD COLUMN subscription_ends_at TIMESTAMP NULL DEFAULT NULL");
        echo "Successfully added subscription_ends_at column to users table.\n";
    } else {
        echo "Column subscription_ends_at already exists in users table.\n";
    }
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
