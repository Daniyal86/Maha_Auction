<?php
// config/db.php

// Database configuration constants (modify for production server as needed)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_NAME', getenv('DB_NAME') ?: 'maharashtra_auctions');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // If the database does not exist or cannot connect, fail gracefully or explain setup
    die("Database connection failed: " . $e->getMessage() . ". Make sure to import db/schema.sql into MySQL and create database 'maharashtra_auctions'.");
}

// Initialize session globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
