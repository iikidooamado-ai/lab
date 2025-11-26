<?php
include("../includes/db.php");
header("Content-Type: application/json");

// Expecting professor_name + subject_id
if (!empty($_POST['professor_name']) && !empty($_POST['subject_id'])) {
    $professor_name = trim($_POST['professor_name']);
    $subject_id = intval($_POST['subject_id']);

    // 1. Insert professor
    $stmt = $conn->prepare("INSERT INTO professors (name) VALUES (?)");
    $stmt->bind_param("s", $professor_name);

    if ($stmt->execute()) {
        $professor_id = $stmt->insert_id;

        // 2. Link professor with subject
        $stmt2 = $conn->prepare("INSERT INTO professor_subjects (professor_id, subject_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $professor_id, $subject_id);

        if ($stmt2->execute()) {
            echo json_encode(["success" => true, "message" => " Professor & Subject added"]);
            exit;
        }
    }
    echo json_encode(["success" => false, "message" => " Failed to add professor/subject"]);
    exit;
}

echo json_encode(["success" => false, "message" => " Missing professor or subject"]);