<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
include("../includes/db.php");

$code = trim($_POST['subject_code'] ?? '');
$name = trim($_POST['subject_name'] ?? '');

if ($code === '' || $name === '') {
    echo json_encode([
        "success" => false,
        "message" => "Both Subject Code and Subject Name are required."
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)");
    $stmt->bind_param("ss", $code, $name);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Subject added successfully"
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error adding subject: " . $e->getMessage()
    ]);
}