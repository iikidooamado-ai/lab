<?php
include("../includes/db.php");
header("Content-Type: application/json");

if (!empty($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM labs WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => " Lab deleted"]);
    } else {
        echo json_encode(["success" => false, "message" => " Failed to delete lab"]);
    }
    exit;
}

echo json_encode(["success" => false, "message" => " Missing lab id"]);