<?php

// backend/config/config.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');

define('DB_HOST', 'localhost');
define('DB_NAME', 'virtual_internship_hub');
define('DB_USER', 'root');   // XAMPP default
define('DB_PASS', '');       // XAMPP default empty
define('BASE_URL', 'http://localhost/virtual_internship_hub/'); // change to your project folder URL

define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    error_log("DB Connection error: " . $e->getMessage());
    die("Database connection failed.");
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
