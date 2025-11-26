<?php
header("Content-Type: application/json");
include("../includes/db.php");

try {
    $sql = "SELECT id, section_name FROM sections ORDER BY section_name";
    $result = $conn->query($sql);

    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    echo json_encode([
        "success" => true,
        "data" => $data
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error fetching sections: " . $e->getMessage()
    ]);
}