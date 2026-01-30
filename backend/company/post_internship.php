<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

/* ===============================
   AUTH CHECK (COMPANY ONLY)
================================ */
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'company'
) {
    echo json_encode([
        "status" => "ERROR",
        "message" => "Unauthorized"
    ]);
    exit;
}

$company_id = $_SESSION['user_id'];

/* ===============================
   ONLY POST ALLOWED
================================ */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => "ERROR",
        "message" => "Method Not Allowed"
    ]);
    exit;   
}

/* ===============================
   GET & SANITIZE INPUT
================================ */
$title            = trim($_POST['title'] ?? '');
$department       = trim($_POST['department'] ?? '');
$location         = trim($_POST['location'] ?? '');
$type             = trim($_POST['type'] ?? 'Remote');
$start_date       = $_POST['startDate'] ?? '';
$end_date         = $_POST['endDate'] ?? '';
$duration_weeks   = (int)($_POST['duration'] ?? 0);
$stipend          = trim($_POST['stipend'] ?? '');
$skills           = trim($_POST['skills'] ?? '');
$description      = trim($_POST['description'] ?? '');
$application_link = trim($_POST['applicationLink'] ?? '');

/* ===============================
   BASIC VALIDATION
================================ */
if (
    $title === '' ||
    $description === '' ||
    $start_date === '' ||
    $end_date === '' ||
    $duration_weeks <= 0
) {
    echo json_encode([
        "status" => "ERROR",
        "message" => "Required fields missing"
    ]);
    exit;
}

/* ===============================
   INSERT INTERNSHIP
================================ */
try {

    $stmt = $pdo->prepare("
        INSERT INTO internships
        (company_id, title, department, location, type,
         start_date, end_date, duration_weeks,
         stipend, skills, description, application_link)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $company_id,
        $title,
        $department,
        $location,
        $type,
        $start_date,
        $end_date,
        $duration_weeks,
        $stipend,
        $skills,
        $description,
        $application_link
    ]);

    echo json_encode([
        "status" => "SUCCESS",
        "message" => "Internship posted successfully"
    ]);
    exit;

} catch (PDOException $e) {

    http_response_code(500);
    echo json_encode([
        "status" => "ERROR",
        "message" => "Database error",
        "debug" => $e->getMessage()
    ]);
    exit;
}
