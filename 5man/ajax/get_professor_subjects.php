<?php
header("Content-Type: application/json");
include("../includes/db.php");

try {
    $sql = "SELECT ps.id, p.name AS professor_name, s.subject_code, s.subject_name
            FROM professor_subjects ps
            JOIN professors p ON ps.professor_id = p.id
            JOIN subjects s ON ps.subject_id = s.id
            ORDER BY p.name, s.subject_code";

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
        "message" => "Error fetching professor-subjects: " . $e->getMessage()
    ]);
}