<?php
require_once __DIR__ . '/../config/config.php';

if (!isset($_GET['id'])) {
  echo json_encode([
    "status" => "ERROR",
    "message" => "ID missing"
  ]);
  exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("
  SELECT id, title, department, location, start_date, end_date,
       duration_weeks, stipend, skills, description
  FROM internships
  WHERE id = ?
");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
  echo json_encode([
    "status" => "ERROR",
    "message" => "Internship not found"
  ]);
  exit;
}

echo json_encode([
  "status" => "SUCCESS",
  "data" => $data
]);