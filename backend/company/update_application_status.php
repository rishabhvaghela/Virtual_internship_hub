<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../mail/send_mail.php';

header("Content-Type: application/json");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {

    echo json_encode(["success" => false]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$application_id = $data['application_id'] ?? null;
$status = $data['status'] ?? null;

if (!$application_id || !in_array($status, ['accepted', 'rejected'])) {

    echo json_encode(["success" => false]);
    exit;
}

try {

    // Get student info
    $stmt = $pdo->prepare("
        SELECT 
            u.email,
            u.name as student_name,
            i.title,
            c.name as company_name
        FROM applications a
        JOIN users u ON a.student_id = u.id
        JOIN internships i ON a.internship_id = i.id
        JOIN users c ON i.company_id = c.id
        WHERE a.id = ?
    ");

    $stmt->execute([$application_id]);

    $info = $stmt->fetch();

    if (!$info) {

        echo json_encode(["success" => false]);
        exit;
    }


    if ($status === 'rejected') {

        $update = $pdo->prepare("
            UPDATE applications
            SET status='rejected',
                interview_date=NULL,
                interview_note=NULL
            WHERE id=?
        ");
    } else {

        $update = $pdo->prepare("
            UPDATE applications
            SET status='accepted'
            WHERE id=?
        ");
    }

    $update->execute([$application_id]);


    // send email
    sendApplicationStatusEmail(
        $info['email'],
        $info['student_name'],
        $info['title'],
        $info['company_name'],
        $status
    );


    echo json_encode(["success" => true]);
} catch (Exception $e) {

    echo json_encode(["success" => false]);
}
