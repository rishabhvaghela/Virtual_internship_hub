<?php
session_start();
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    die("Unauthorized");
}

if (!isset($_GET['file'])) {
    die("File missing");
}

// DB se jo value aa rahi hai
$relativePath = $_GET['file'];

// Security: ../ block karo
if (strpos($relativePath, '..') !== false) {
    die("Invalid path");
}

// Project root se absolute path banao
$filepath = __DIR__ . "/../../" . $relativePath;

if (!file_exists($filepath)) {
    die("File not found: " . $filepath);
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($filepath) . '"');
header('Content-Length: ' . filesize($filepath));

readfile($filepath);
exit;