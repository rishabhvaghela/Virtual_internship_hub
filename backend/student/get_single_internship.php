<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "ERROR",
        "message" => "Invalid internship ID"
    ]);
    exit;
}

$id = (int)$_GET['id'];

try {

    $stmt = $pdo->prepare("
        SELECT 
            i.*,
            u.name AS company_name,

            CASE 
                WHEN i.end_date < CURDATE() THEN 'closed'
                ELSE 'open'
            END AS status

        FROM internships i
        JOIN users u ON u.id = i.company_id
        WHERE i.id = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    $internship = $stmt->fetch();

    if (!$internship) {

        http_response_code(404);

        echo json_encode([
            "status" => "ERROR",
            "message" => "Internship not found"
        ]);

        exit;
    }

    echo json_encode([
        "status" => "SUCCESS",
        "data" => $internship
    ]);

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode([
        "status" => "ERROR",
        "message" => $e->getMessage()
    ]);
}