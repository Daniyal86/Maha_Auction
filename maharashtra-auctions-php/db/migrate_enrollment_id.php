<?php
// db/migrate_enrollment_id.php
require_once __DIR__ . '/../config/db.php';

try {
    // Check if column already exists
    $check = $pdo->query("SHOW COLUMNS FROM users LIKE 'enrollment_id'");
    $exists = $check->fetch();

    if (!$exists) {
        $pdo->exec("ALTER TABLE users ADD COLUMN enrollment_id VARCHAR(100) DEFAULT NULL");
        echo "Successfully added enrollment_id column to users table.\n";
    } else {
        echo "Column enrollment_id already exists in users table.\n";
    }
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>
