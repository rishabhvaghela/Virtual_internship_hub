<?php
require_once __DIR__ . '/../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';

if ($token === '' || $password === '') {
    echo "INVALID_REQUEST";
    exit;
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Token verify
$stmt = $pdo->prepare("
    SELECT email 
    FROM password_resets 
    WHERE token = ? AND expires_at > NOW()
    LIMIT 1
");
$stmt->execute([$token]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "TOKEN_INVALID";
    exit;
}

//  Password update
// $hashed = password_hash($password, PASSWORD_DEFAULT);

$update = $pdo->prepare("
    UPDATE users 
    SET password = ?
    WHERE email = ?
");
$update->execute([$password, $data['email']]);

//  Token delete
$pdo->prepare("DELETE FROM password_resets WHERE email = ?")
    ->execute([$data['email']]);

echo "PASSWORD_UPDATED";