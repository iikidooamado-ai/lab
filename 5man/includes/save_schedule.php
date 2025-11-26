<?php
// admin/save_schedule.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require_once("../includes/db.php");
require_once("../includes/functions.php");

//  Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. POST required."
    ]);
    exit;
}

//  Collect form data into array
$data = [
    'lab_id'       => $_POST['lab_id']       ?? null,
    'professor_id' => $_POST['professor_id'] ?? null,
    'subject_id'   => $_POST['subject_id']   ?? null,
    'section_id'   => $_POST['section_id']   ?? null,
    'day'          => $_POST['day']          ?? null,
    'start_time'   => $_POST['start_time']   ?? null,
    'end_time'     => $_POST['end_time']     ?? null,
];

//  Validate required fields
foreach ($data as $key => $value) {
    if (empty($value)) {
        echo json_encode([
            "success" => false,
            "message" => "Missing required field: $key"
        ]);
        exit;
    }
}

//  Call function from includes/functions.php
$result = addSchedule($conn, $data);

echo json_encode($result);
exit;
