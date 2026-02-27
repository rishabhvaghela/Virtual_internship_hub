<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../mail/send_mail.php';

// ----------------------------
// Force JSON output and clean any accidental output
// ----------------------------
header('Content-Type: application/json');
ob_start(); // Start output buffer
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

/* ======================
   AUTH CHECK
====================== */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access"
    ]);
    exit;
}

/* ======================
   GET POST DATA
====================== */
$student_id    = $_SESSION['user_id'];
$internship_id = $_POST['internship_id'] ?? null;
$cover         = trim($_POST['cover'] ?? '');

/* ======================
   BASIC VALIDATION
====================== */
if (!$internship_id || !$cover) {
    echo json_encode([
        "success" => false,
        "message" => "Required fields missing"
    ]);
    exit;
}

/* ======================
   CHECK INTERNSHIP EXISTS
====================== */
try {
    $stmt = $pdo->prepare("SELECT id FROM internships WHERE id = ?");
    $stmt->execute([$internship_id]);
    $internship = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$internship) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid internship"
        ]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
    exit;
}

/* ======================
   CHECK ALREADY APPLIED
====================== */
try {
    $stmt = $pdo->prepare("SELECT id FROM applications WHERE student_id=? AND internship_id=?");
    $stmt->execute([$student_id, $internship_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        echo json_encode([
            "success" => false,
            "message" => "You have already applied to this internship"
        ]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
    exit;
}

/* ======================
   CHECK IF INTERNSHIP IS STILL OPEN
====================== */
$stmt = $pdo->prepare("
    SELECT id, end_date 
    FROM internships 
    WHERE id = ? AND end_date >= CURDATE()
");
$stmt->execute([$internship_id]);
$activeInternship = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$activeInternship) {
    echo json_encode([
        "success" => false,
        "message" => "This internship is closed"
    ]);
    exit;
}

/* ======================
   GET STUDENT RESUME FROM student_profile
====================== */
try {
    $stmt = $pdo->prepare("SELECT resume FROM student_profile WHERE user_id=?");
    $stmt->execute([$student_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile || !$profile['resume']) {
        echo json_encode([
            "success" => false,
            "message" => "Please upload your resume in your profile first"
        ]);
        exit;
    }

    $resume = $profile['resume'];
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
    exit;
}

/* ======================
   INSERT APPLICATION
====================== */
try {

    $stmt = $pdo->prepare("
        INSERT INTO applications
        (student_id, internship_id, resume, cover_letter, status, applied_at)
        VALUES (?, ?, ?, ?, 'applied', NOW())
    ");

    $stmt->execute([$student_id, $internship_id, $resume, $cover]);


    /* ======================
        SEND EMAIL TO STUDENT
    ====================== */

    /* ======================
    SEND EMAIL TO STUDENT + COMPANY
====================== */

    try {

        $stmt = $pdo->prepare("
            SELECT 
                u.email as student_email,
                u.name as student_name,
                i.title,
                c.name as company_name,
                c.email as company_email
            FROM users u
            JOIN internships i ON i.id=?
            JOIN users c ON i.company_id=c.id
            WHERE u.id=?
            ");

        $stmt->execute([$internship_id, $student_id]);

        $info = $stmt->fetch();

        if ($info) {

            // ============================
            // EMAIL TO STUDENT
            // ============================

            sendApplicationConfirmation(

                $info['student_email'],
                $info['student_name'],
                $info['title'],
                $info['company_name']

            );


            // ============================
            // EMAIL TO COMPANY (NEW)
            // ============================

            sendCompanyNewApplicantEmail(

                $info['company_email'],
                $info['company_name'],
                $info['student_name'],
                $info['student_email'],
                $info['title']

            );
        }
    } catch (Exception $e) {
        // do nothing if email fails
    }


    ob_end_clean();

    echo json_encode([
        "success" => true,
        "message" => "Application submitted successfully",
        "resume" => $resume
    ]);
} catch (PDOException $e) {

    ob_end_clean();

    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
