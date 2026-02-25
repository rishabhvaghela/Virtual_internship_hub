<?php
header("Content-Type: application/json");
require_once "config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
    exit();
}

// Collect & sanitize input
$name    = trim($_POST["name"] ?? '');
$email   = trim($_POST["email"] ?? '');
$subject = trim($_POST["subject"] ?? '');
$message = trim($_POST["message"] ?? '');

// Validation
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode([
        "success" => false,
        "message" => "All fields are required"
    ]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email format"
    ]);
    exit();
}

try {
    $stmt = $conn->prepare("
        INSERT INTO contact_messages (name, email, subject, message)
        VALUES (:name, :email, :subject, :message)
    ");

    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":subject", $subject);
    $stmt->bindParam(":message", $message);

    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Message sent successfully. We will respond within 24 hours."
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error. Please try again later."
    ]);
}