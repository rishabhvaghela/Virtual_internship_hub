<?php
session_start();

require_once __DIR__ . '/../config/config.php';

/* =========================
   BASIC SECURITY CHECKS
========================= */

// Only logged-in users
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'UNAUTHORIZED']);
    exit;
}

// Only students allowed
if ($_SESSION['role'] !== 'student') {
    http_response_code(403);
    echo json_encode(['status' => 'FORBIDDEN']);
    exit;
}

$user_id = $_SESSION['user_id'];

// -------- AUTO CREATE PROFILE IF NOT EXISTS --------
$checkProfile = $pdo->prepare(
    "SELECT id FROM student_profile WHERE user_id = :user_id"
);
$checkProfile->execute(['user_id' => $user_id]);
$profileExists = $checkProfile->fetchColumn();

if (!$profileExists) {

    $userStmt = $pdo->prepare(
        "SELECT name FROM users WHERE id = :user_id"
    );
    $userStmt->execute(['user_id' => $user_id]);
    $userName = $userStmt->fetchColumn();

    $insertProfile = $pdo->prepare(
        "INSERT INTO student_profile (user_id, full_name)
         VALUES (:user_id, :full_name)"
    );
    $insertProfile->execute([
        'user_id'   => $user_id,
        'full_name' => $userName
    ]);
}


/* =========================
   HANDLE GET REQUEST
   Fetch profile data
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "
        SELECT 
            u.name,
            u.email,
            sp.full_name,
            sp.phone,
            sp.gender,
            sp.skills,
            sp.bio,
            sp.address,
            sp.resume
        FROM users u
        LEFT JOIN student_profile sp ON sp.user_id = u.id
        WHERE u.id = :user_id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'SUCCESS',
        'data'   => $profile
    ]);
    exit;
}

/* =========================
   HANDLE POST REQUEST
   Save / Update profile

   ========================= */


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        empty($_POST['full_name']) &&
        empty($_POST['phone']) &&
        empty($_POST['skills']) &&
        empty($_POST['bio']) &&
        empty($_POST['address'])
    ) {
        http_response_code(400);
        echo json_encode(['status' => 'EMPTY_SUBMIT']);
        exit;
    }


    // Sanitize inputs
    $full_name = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $gender    = trim($_POST['gender'] ?? '');
    $skills    = trim($_POST['skills'] ?? '');
    $bio       = trim($_POST['bio'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $profilePhoto = $_FILES['profile_photo'] ?? null;
    $resumeFile   = $_FILES['resume'] ?? null;

    /* ======================
        PROFILE PHOTO
    ====================== */

    $profilePhotoPath = null;

    if ($profilePhoto && $profilePhoto['error'] === 0) {

        if ($profilePhoto['size'] > 2 * 1024 * 1024) {
            exit(json_encode(['status' => 'IMAGE_TOO_LARGE']));
        }

        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($profilePhoto['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            exit(json_encode(['status' => 'INVALID_IMAGE_TYPE']));
        }

        $uploadDir = __DIR__ . '/../../uploads/profile_photos/student/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = "student_{$user_id}." . $ext;
        move_uploaded_file($profilePhoto['tmp_name'], $uploadDir . $fileName);
    }


    /* =========================
   RESUME UPLOAD
========================= */
    $resumePath = null;

    if (!empty($_FILES['resume']['name'])) {

        $allowedExt = ['pdf', 'doc', 'docx'];
        $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            http_response_code(400);
            exit(json_encode(['status' => 'INVALID_RESUME_TYPE']));
        }

        $uploadDir = __DIR__ . '/../../uploads/resumes/student/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = 'resume_' . $user_id . '.' . $ext;
        $targetPath = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['resume']['tmp_name'], $targetPath)) {
            http_response_code(500);
            exit(json_encode(['status' => 'RESUME_UPLOAD_FAILED']));
        }

        $resumePath = 'uploads/resumes/student/' . $fileName;
    }




    // Check if profile already exists
    $check = $pdo->prepare("SELECT id FROM student_profile WHERE user_id = :user_id");
    $check->execute(['user_id' => $user_id]);
    $exists = $check->fetchColumn();

    if ($exists) {
        // UPDATE
        $sql = "
        UPDATE student_profile SET
        full_name = :full_name,
        phone = :phone,
        gender = :gender,
        skills = :skills,
        bio = :bio,
        address = :address,
        resume = COALESCE(:resume, resume)
        WHERE user_id = :user_id
        ";
    } else {
        // INSERT
        $sql = "
            INSERT INTO student_profile
                (user_id, full_name, phone, gender, skills, bio, address)
            VALUES
                (:user_id, :full_name, :phone, :gender, :skills, :bio, :address)
        ";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'user_id'       => $user_id,
        'full_name'     => $full_name,
        'phone'         => $phone,
        'gender'        => $gender,
        'skills'        => $skills,
        'bio'           => $bio,
        'address'       => $address,
        'resume' => $resumePath,

    ]);

    echo json_encode([
        'status' => 'PROFILE_SAVED'
    ]);
    exit;
}

/* =========================
   INVALID METHOD
========================= */

http_response_code(405);
echo json_encode(['status' => 'METHOD_NOT_ALLOWED']);
exit;
