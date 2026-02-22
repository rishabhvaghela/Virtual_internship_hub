<?php
// backend/db_test.php
require_once __DIR__ . '/config/config.php';

echo "<h2>DB & Session Test — Virtual Internship Hub</h2>";

// 1) DB: try simple query
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_NUM);
    if (!$tables) {
        echo "<p style='color:orange;'>No tables found in DB '".DB_NAME."'. Did you import create_database.sql?</p>";
    } else {
        echo "<p style='color:green;'>Connected to DB: <strong>" . DB_NAME . "</strong></p>";
        echo "<h3>Tables:</h3><ul>";
        foreach ($tables as $t) {
            echo "<li>" . htmlspecialchars($t[0]) . "</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>DB error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 2) Session test
$_SESSION['test'] = 'ok_' . rand(100,999);
echo "<p>Session started? <strong>" . (session_status() === PHP_SESSION_ACTIVE ? 'YES' : 'NO') . "</strong></p>";
echo "<p>Session test value: <strong>" . htmlspecialchars($_SESSION['test']) . "</strong></p>";

// 3) Quick write permission check for uploads/
$uploadDir = realpath(__DIR__ . '/uploads');
if ($uploadDir && is_writable($uploadDir)) {
    echo "<p style='color:green;'>uploads/ directory exists and is writable.</p>";
} else {
    echo "<p style='color:orange;'>uploads/ folder missing or not writable. Create backend/uploads and set write permission.</p>";
}

echo "<hr>";
echo "<p>If everything above is green/orange (not red), Step 1 is OK.</p>";