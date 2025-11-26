<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
include("../includes/db.php");

try {
    $res = $conn->query("SELECT id, subject_code, subject_name FROM subjects ORDER BY subject_code");
    $subjects = [];
    while ($row = $res->fetch_assoc()) {
        $subjects[] = $row;
    }

    echo json_encode([
        "success" => true,
        "data" => $subjects
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error fetching subjects: " . $e->getMessage()
    ]);
}