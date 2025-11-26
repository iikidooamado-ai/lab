<?php
include("../includes/db.php");
header("Content-Type: application/json");

if (!empty($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM sections WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => " Section deleted"]);
    } else {
        echo json_encode(["success" => false, "message" => " Failed to delete section"]);
    }
    exit;
}

echo json_encode(["success" => false, "message" => " Missing section id"]);