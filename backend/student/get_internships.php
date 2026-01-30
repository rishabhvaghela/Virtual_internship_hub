<?php
require_once __DIR__ . '/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ---------- AUTH CHECK ---------- */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    http_response_code(401);
    echo json_encode([
        "status" => "ERROR",
        "message" => "Unauthorized"
    ]);
    exit;
}

/* ---------- FETCH INTERNSHIPS ---------- */
$stmt = $pdo->prepare("
    SELECT 
        i.id,
        i.title,
        i.department,
        i.location,
        i.type,
        i.start_date,
        i.duration_weeks,
        i.stipend,
        i.skills,
        i.description,
        i.created_at,
        u.name AS company_name
    FROM internships i
    JOIN users u ON u.id = i.company_id
    ORDER BY i.created_at DESC
");

$stmt->execute();
$internships = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- RESPONSE ---------- */
echo json_encode([
    "status" => "SUCCESS",
    "count" => count($internships),
    "data" => $internships
]);
