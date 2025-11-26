<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
include("../includes/db.php");

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid subject ID"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Subject deleted successfully"
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error deleting subject: " . $e->getMessage()
    ]);
}