<?php
require_once __DIR__ . '/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];

/* ======================
   GET → LOAD PROFILE
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // 1️⃣ USERS TABLE (SOURCE)
    $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2️⃣ COMPANY PROFILE
    $stmt = $pdo->prepare("SELECT * FROM company_profile WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3️⃣ FIRST TIME OPEN → AUTO CREATE + AUTO SYNC (STUDENT STYLE)
    if (!$profile) {
        $pdo->prepare("
            INSERT INTO company_profile (user_id, company_name, industry, description)
            VALUES (?, ?, '', '')
        ")->execute([$user_id, $user['name']]);

        $profile = [
            "company_name" => $user['name'],
            "industry" => "",
            "description" => ""
        ];
    }

    // 4️⃣ IF NAME EMPTY → SYNC FROM USERS (IMPORTANT)
    if (empty($profile['company_name'])) {
        $pdo->prepare("
            UPDATE company_profile 
            SET company_name = ?
            WHERE user_id = ?
        ")->execute([$user['name'], $user_id]);

        $profile['company_name'] = $user['name'];
    }

    echo json_encode([
        "company_name" => $profile['company_name'],
        "email"        => $user['email'],
        "industry"     => $profile['industry'],
        "description"  => $profile['description']
    ]);
    exit;
}

/* ======================
   POST → SAVE PROFILE
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $company_name = trim($_POST['company_name'] ?? '');

    if ($company_name === '') {
        echo json_encode([
            'success' => false,
            'error' => 'Company name required'
        ]);
        exit;
    }
    $industry     = trim($_POST['industry'] ?? '');
    $description  = trim($_POST['description'] ?? '');


    // 1️⃣ USERS TABLE UPDATE (STUDENT STYLE)
    $pdo->prepare("
        UPDATE users SET name = ?
        WHERE id = ?
    ")->execute([$company_name, $user_id]);

    // 2️⃣ COMPANY PROFILE UPDATE
    $pdo->prepare("
        UPDATE company_profile
        SET company_name = ?, industry = ?, description = ?
        WHERE user_id = ?
    ")->execute([$company_name, $industry, $description, $user_id]);

    echo json_encode(["success" => true]);
    exit;
}

echo json_encode(["error" => "Method Not Allowed"]);
