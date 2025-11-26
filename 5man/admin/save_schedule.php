<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
ob_clean(); // clear accidental output

require_once("../includes/db.php");
require_once("../includes/functions.php");

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
    exit;
}

// Build the data array for addSchedule()
$data = [
    "lab_id"               => $_POST['lab_id'] ?? null,
    "section_id"           => $_POST['section_id'] ?? null,
    "professor_subject_id" => $_POST['professor_subject_id'] ?? null,
    "day"                  => $_POST['day'] ?? null,
    "start_time"           => $_POST['start_time'] ?? null,
    "end_time"             => $_POST['end_time'] ?? null
];

// Validate required fields
foreach ($data as $key => $value) {
    if (empty($value)) {
        echo json_encode([
            "success" => false,
            "message" => "Missing required field: $key"
        ]);
        exit;
    }
}

// Call your addSchedule() function (must be updated to handle professor_subject_id)
$result = addSchedule($conn, $data);

// Always return JSON
echo json_encode($result);
exit;