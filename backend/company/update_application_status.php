<?php
session_start();
require_once "../config/db.php";

header("Content-Type: application/json");

// Authorization check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);
    exit;
}

// Read raw input
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Fallback to POST
if (!is_array($data)) {
    $data = $_POST;
}

// Validate required fields
if (
    !isset($data['application_id'], $data['status']) ||
    trim($data['application_id']) === '' ||
    trim($data['status']) === ''
) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid data",
        "received" => $data
    ]);
    exit;
}

$application_id = (int)$data['application_id'];
$status = strtolower(trim($data['status']));

// Allowed values (ACCEPTED enabled)
$allowedStatuses = ['applied', 'accepted', 'rejected'];

if (!in_array($status, $allowedStatuses, true)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid status value"
    ]);
    exit;
}

try {

    $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->execute([$status, $application_id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode([
            "success" => false,
            "message" => "Application not found or already updated"
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Status updated successfully"
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => "Database error"
    ]);
}