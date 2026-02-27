<?php

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../mail/send_mail.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {

    echo json_encode(["success" => false]);
    exit;
}

$application_id = $_POST['application_id'];
$date = $_POST['date'];
$time = $_POST['time'];
$note = $_POST['note'];

$scheduled_at = date("Y-m-d H:i:s", strtotime("$date $time"));

try {

    // get student info
    $stmt = $pdo->prepare("
        SELECT 
            u.email,
            u.name as student_name,
            i.title,
            c.name as company_name
        FROM applications a
        JOIN users u ON a.student_id=u.id
        JOIN internships i ON a.internship_id=i.id
        JOIN users c ON i.company_id=c.id
        WHERE a.id=?
    ");

    $stmt->execute([$application_id]);
    $info = $stmt->fetch();


    // update interview
    $update = $pdo->prepare("
        UPDATE applications
        SET status='interview_scheduled',
            interview_date=?,
            interview_note=?
        WHERE id=?
    ");

    $update->execute([
        $scheduled_at,
        $note,
        $application_id
    ]);


    /* ============================================
       USE EXISTING FUNCTION (QUEUE EMAIL)
    ============================================ */

    sendInterviewEmail(
        $info['email'],
        $info['student_name'],
        $info['title'],
        $info['company_name'],
        $scheduled_at,
        $note
    );


    /* ============================================
       TRIGGER BACKGROUND WORKER
    ============================================ */

    @file_get_contents(
        "http://localhost/virtual_internship_hub/backend/email/trigger_email_worker.php"
    );


    echo json_encode(["success" => true]);

} catch (Exception $e) {

    echo json_encode(["success" => false]);
}