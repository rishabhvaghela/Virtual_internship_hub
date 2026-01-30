<?php
require_once __DIR__ . '/../../config/config.php';

$email = trim($_POST['email'] ?? '');
$otp   = trim($_POST['otp'] ?? '');

if ($email === '' || $otp === '') {
    echo "OTP_REQUIRED";
    exit;
}

/* ---------------- OTP CHECK ---------------- */

$stmt = $pdo->prepare("
    SELECT otp
    FROM email_otps
    WHERE email = ?
      AND expires_at >= NOW()
    LIMIT 1
");
$stmt->execute([$email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "OTP_EXPIRED";
    exit;
}

if ((string)$row['otp'] !== (string)$otp) {
    echo "OTP_INVALID";
    exit;
}

/* ---------------- FETCH PENDING USER ---------------- */

$pending = $pdo->prepare("
    SELECT name, email, password, role
    FROM pending_users
    WHERE email = ?
    LIMIT 1
");

$pending->execute([$email]);
$user = $pending->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "USER_NOT_FOUND";
    exit;
}



/* ---------------- FINAL USER CREATE ---------------- */

$insert = $pdo->prepare("
    INSERT INTO users (name, email, password, role, is_verified)
    VALUES (?, ?, ?, ?, 1)
");
$insert->execute([
    $user['name'],
    $user['email'],
    $user['password'],
    $user['role']
]);

/* ---------------- CLEANUP ---------------- */

$pdo->prepare("DELETE FROM pending_users WHERE email = ?")
    ->execute([$email]);

$pdo->prepare("DELETE FROM email_otps WHERE email = ?")
    ->execute([$email]);

echo "OTP_VERIFIED";
exit;
