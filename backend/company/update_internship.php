<?php
session_start();
require_once __DIR__ . '/../config/config.php';

$data = json_decode(file_get_contents("php://input"), true);

$id          = $data['id'] ?? null;
$title       = $data['title'] ?? null;
$department  = $data['department'] ?? null;
$location    = $data['location'] ?? null;
$startDate   = $data['startDate'] ?? null;
$endDate     = $data['endDate'] ?? null;         
$duration    = $data['duration'] ?? null;
$stipend     = $data['stipend'] ?? null;
$skills      = $data['skills'] ?? null;
$description = $data['description'] ?? null;

/* ===== BASIC VALIDATION ===== */
if (
    !$id ||
    !$title ||
    !$location ||
    !$startDate ||
    !$endDate ||         
    !$duration ||
    !$description
) {
    echo json_encode([
        "status" => "ERROR",
        "message" => "Required fields missing"
    ]);
    exit;
}

$stmt = $pdo->prepare("
    UPDATE internships SET
        title = ?,
        department = ?,
        location = ?,
        start_date = ?,
        end_date = ?,          
        duration_weeks = ?,
        stipend = ?,
        skills = ?,
        description = ?
    WHERE id = ? AND company_id = ?
");

$stmt->execute([
    $title,
    $department,
    $location,
    $startDate,
    $endDate,               
    $duration,
    $stipend,
    $skills,
    $description,
    $id,
    $_SESSION['user_id']
]);

echo json_encode(["status" => "UPDATED"]);
