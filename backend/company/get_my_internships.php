<?php
session_start();
require_once __DIR__ . "/../config/config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    echo json_encode(["status" => "ERROR", "message" => "Unauthorized"]);
    exit;
}

$companyId = $_SESSION['user_id'];

$sql = "
SELECT 
  internships.id,
  internships.title,
  internships.end_date,
  COUNT(applications.id) AS applicants,
  CASE 
    WHEN internships.end_date < CURDATE() THEN 'closed'
    ELSE 'open'
  END AS status
FROM internships
LEFT JOIN applications 
  ON applications.internship_id = internships.id
WHERE internships.company_id = ?
GROUP BY internships.id, internships.title, internships.end_date
ORDER BY internships.id DESC

";

$stmt = $pdo->prepare($sql);
$stmt->execute([$companyId]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "status" => "SUCCESS",
    "data" => $data
]);