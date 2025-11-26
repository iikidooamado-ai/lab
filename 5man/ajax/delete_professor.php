<?php
include("../includes/db.php");
header("Content-Type: application/json");

if (!empty($_GET['id'])) {
    $id = intval($_GET['id']);

    // delete professor_subject link first
    $stmt = $conn->prepare("DELETE FROM professor_subjects WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => " Professor & Subject link deleted"]);
    } else {
        echo json_encode(["success" => false, "message" => " Failed to delete professor/subject"]);
    }
    exit;
}

echo json_encode(["success" => false, "message" => " Missing professor/subject id"]);