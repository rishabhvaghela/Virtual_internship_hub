<?php
session_start();
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    echo json_encode(["success" => false]);
    exit;
}

$application_id = $_POST['application_id'] ?? null;
$date = $_POST['date'] ?? null;
$time = $_POST['time'] ?? null;
$note = $_POST['note'] ?? null;

if (!$application_id || !$date || !$time) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$scheduled_at = date("Y-m-d H:i:s", strtotime("$date $time"));
$company_id = $_SESSION['user_id'];

try {

    // verify ownership
    $check = $pdo->prepare("
        SELECT a.id
        FROM applications a
        JOIN internships i ON a.internship_id = i.id
        WHERE a.id = ? AND i.company_id = ?
    ");
    $check->execute([$application_id, $company_id]);

    if ($check->rowCount() === 0) {
        echo json_encode(["success" => false]);
        exit;
    }

    // update applications table directly
    $update = $pdo->prepare("
        UPDATE applications 
        SET 
            status = 'interview_scheduled',
            interview_date = ?,
            interview_note = ?
        WHERE id = ?
    ");

    $update->execute([$scheduled_at, $note, $application_id]);

    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    echo json_encode(["success" => false]);
}