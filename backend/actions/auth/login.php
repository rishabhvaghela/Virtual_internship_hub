<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$role     = $_POST['role'] ?? '';

// ---------- BASIC CHECK ----------
if ($email === '' && $password === '') {
    echo "All fields are required";
    exit;
}

if ($email === ''){
    echo "Email is required";
    exit;
}

if ($password === ''){
    echo "Password is required";
    exit;
}

// ---------- USER FETCH ----------
$stmt = $pdo->prepare("
    SELECT id, name, email, password, role, is_verified
    FROM users
    WHERE email = ?
    LIMIT 1
");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Email not registered";
    exit;
}

// ---------- PASSWORD VERIFY ----------
if ($password !== $user['password']) {
    echo "Incorrect password";
    exit;
}

// ---------- ROLE CHECK ----------
if ($user['role'] !== $role) {
    echo "Role mismatch";
    exit;
}

// ---------- SESSION SET ----------
$_SESSION['user_id'] = $user['id'];
$_SESSION['name']    = $user['name'];
$_SESSION['email']   = $user['email'];
$_SESSION['role']    = $user['role'];

// ---------- RESPONSE ----------
if ($user['role'] === 'student') {
    echo "STUDENT_LOGIN";
} elseif ($user['role'] === 'company') {
    echo "COMPANY_LOGIN";
} else {
    echo "Login failed";
}