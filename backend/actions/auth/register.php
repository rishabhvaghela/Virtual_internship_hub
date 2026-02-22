<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../mail/send_mail.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$role     = $_POST['role'] ?? 'student';

/*
|--------------------------------------------------------------------------
| NOTE:
| JS validation already done
| PHP will only:
| 1. check duplicate email
| 2. insert data
|--------------------------------------------------------------------------
*/

// ---------- INSERT USER ----------
// $hashed = password_hash($password, PASSWORD_DEFAULT);

// $insert = $pdo->prepare("
//     INSERT INTO users (name, email, password, role, is_verified)
//     VALUES (?, ?, ?, ?, 0)
// ");


// $insert->execute([$name, $email, $password, $role]);

$pdo->prepare("DELETE FROM pending_users WHERE email = ?")
    ->execute([$email]);

$pdo->prepare("
INSERT INTO pending_users (name, email, password, role)
VALUES (?, ?, ?, ?)
")->execute([$name, $email, $password, $role]);


// ---------- OTP GENERATE ----------
$otp = random_int(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// old OTP delete
$pdo->prepare("DELETE FROM email_otps WHERE email = ?")
    ->execute([$email]);

// insert OTP
$pdo->prepare("
INSERT INTO email_otps (email, otp, expires_at)
VALUES (?, ?, ?)
")->execute([$email, $otp, $expiry]);

$sent = sendOtp($email, $otp);

if (!$sent) {
    echo "MAIL_FAILED";
    exit;
}

echo "OTP_SENT";
exit;