<?php
session_start();
require_once __DIR__ . '/../config/config.php';
header("Content-Type: application/json");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$application_id = $data['application_id'] ?? null;
$status = $data['status'] ?? null;

$allowed = ['accepted','rejected'];

if (!$application_id || !in_array($status, $allowed)) {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

try {

    if ($status === 'rejected') {

        $stmt = $pdo->prepare("
            UPDATE applications 
            SET 
                status = 'rejected',
                interview_date = NULL,
                interview_note = NULL
            WHERE id = ?
        ");
        $stmt->execute([$application_id]);

    } else {

        $stmt = $pdo->prepare("
            UPDATE applications 
            SET status = 'accepted'
            WHERE id = ?
        ");
        $stmt->execute([$application_id]);
    }

    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    echo json_encode(["success" => false]);
}