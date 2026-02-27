<?php

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';


/* ---------- CHECK LOGIN STATUS ---------- */

$is_logged_in = false;
$is_student = false;
$user_id = null;

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    $is_logged_in = true;
    $user_id = $_SESSION['user_id'];

    if ($_SESSION['role'] === 'student') {
        $is_student = true;
    }
}


/* ---------- FETCH INTERNSHIPS ---------- */

try {

    $stmt = $pdo->prepare("
        SELECT 
            i.id,
            i.title,
            i.department,
            i.location,
            i.type,
            i.start_date,
            i.end_date,
            i.duration_weeks,
            i.stipend,
            i.skills,
            i.description,
            i.created_at,

            CASE 
                WHEN i.end_date < CURDATE() THEN 'closed'
                ELSE 'open'
            END AS status,

            u.name AS company_name

        FROM internships i
        JOIN users u ON u.id = i.company_id
        ORDER BY i.created_at DESC
    ");

    $stmt->execute();

    $internships = $stmt->fetchAll(PDO::FETCH_ASSOC);


    /* ---------- ADD APPLY PERMISSION FLAG ---------- */

    foreach ($internships as &$internship) {

        $internship['can_apply'] = $is_student;
        $internship['login_required'] = !$is_logged_in;
    }


    echo json_encode([
        "status" => "SUCCESS",
        "is_logged_in" => $is_logged_in,
        "is_student" => $is_student,
        "count" => count($internships),
        "data" => $internships
    ]);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "status" => "ERROR",
        "message" => $e->getMessage()
    ]);
}