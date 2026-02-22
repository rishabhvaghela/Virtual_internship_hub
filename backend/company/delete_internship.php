<?php
session_start();
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    echo json_encode(['status' => 'UNAUTHORIZED']);
    exit;
}

$company_id = $_SESSION['user_id'];
$internship_id = $_POST['internship_id'] ?? null;

if (!$internship_id) {
    echo json_encode(['status' => 'INVALID_ID']);
    exit;
}

/* 🔐 Only delete if internship belongs to company */
$sql = "DELETE FROM internships 
        WHERE id = ? AND company_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$internship_id, $company_id]);

if ($stmt->rowCount()) {
    echo json_encode(['status' => 'DELETED']);
} else {
    echo json_encode(['status' => 'NOT_FOUND']);
}