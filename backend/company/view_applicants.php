<?php
session_start();
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access"
    ]);
    exit;
}

$company_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
SELECT 
    a.id AS application_id,
    s.full_name AS student_name,
    u.email AS student_email,
    i.title AS internship_title,
    a.status,
    a.resume,
    a.cover_letter
FROM applications a
JOIN student_profile s ON a.student_id = s.user_id
JOIN internships i ON a.internship_id = i.id
JOIN users u ON a.student_id = u.id
WHERE i.company_id = ?
ORDER BY a.id DESC
");

$stmt->execute([$company_id]);
$applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "data" => $applicants
]);