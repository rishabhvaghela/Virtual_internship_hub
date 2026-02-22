<?php
session_start();
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access"
    ]);
    exit;
}

$student_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            a.id,
            i.title,
            i.end_date,
            u.name AS company_name,
            a.status,
            a.applied_at
        FROM applications a
        JOIN internships i ON a.internship_id = i.id
        JOIN users u ON i.company_id = u.id
        WHERE a.student_id = ?
        ORDER BY a.applied_at DESC
    ");

    $stmt->execute([$student_id]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $applications
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
