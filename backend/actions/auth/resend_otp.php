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

// check user exists
$stmt = $pdo->prepare("SELECT id FROM pending_users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo "EMAIL_NOT_FOUND";
    exit;
}

// generate OTP
$otp = random_int(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// delete old OTP
$pdo->prepare("DELETE FROM email_otps WHERE email = ?")
    ->execute([$email]);

// insert new OTP
$insert = $pdo->prepare("
    INSERT INTO email_otps (email, otp, expires_at)
    VALUES (?, ?, ?)
");

$insert->execute([$email, $otp, $expiry]);

// ✅ SEND EMAIL
if (!sendOTP($email, $otp)) {
    echo "MAIL_FAILED";
    exit;
}

// TODO: mail($email, ...)

echo "OTP_RESENT";
exit;