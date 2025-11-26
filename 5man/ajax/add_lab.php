<?php
include("../includes/db.php");
header('Content-Type: application/json');

if (!empty($_POST['lab_name'])) {
    $lab_name = trim($_POST['lab_name']);
    $stmt = $conn->prepare("INSERT INTO labs (lab_name) VALUES (?)");
    $stmt->bind_param("s", $lab_name);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Lab added successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Lab name is required"]);
}