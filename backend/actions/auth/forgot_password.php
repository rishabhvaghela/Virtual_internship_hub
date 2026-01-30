<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../mail/send_mail.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$email = trim($_POST['email'] ?? '');

if ($email === '') {
    echo "EMAIL_REQUIRED";
    exit;
}

// Enable PDO error mode (VERY IMPORTANT)
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check user
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if (!$stmt->fetch()) {
    echo "EMAIL_NOT_FOUND";
    exit;
}

// Generate token
$token = bin2hex(random_bytes(32));

// Clear old tokens
$pdo->prepare("DELETE FROM password_resets WHERE email = ?")
    ->execute([$email]);

// Insert token (with expiry – recommended)
$pdo->prepare("
    INSERT INTO password_resets (email, token, expires_at)
    VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))
")->execute([$email, $token]);

$resetLink = "http://localhost/virtual_internship_hub/reset_password.html?token=$token";

if (sendResetLink($email, $resetLink)) {
    echo "RESET_EMAIL_SENT";
} else {
    echo "MAIL_FAILED";
}
exit;
