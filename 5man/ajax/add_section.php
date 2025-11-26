<?php
include("../includes/db.php");
header('Content-Type: application/json');

if (!empty($_POST['section_name'])) {
    $section_name = trim($_POST['section_name']);
    $stmt = $conn->prepare("INSERT INTO sections (section_name) VALUES (?)");
    $stmt->bind_param("s", $section_name);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Section added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Section name is required"]);
}