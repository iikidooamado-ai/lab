<?php
include("db.php");

header('Content-Type: application/json');

try {
    $result = $conn->query("
        SELECT 
            sch.id,
            sch.day,
            sch.start_time,
            sch.end_time,
            sec.section_name,
            l.lab_name,
            l.color AS lab_color,
            p.name AS professor,
            CONCAT(s.subject_code, ' - ', s.subject_name) AS subject,
            sch.auto_shift
        FROM schedules sch
        JOIN sections sec ON sch.section_id = sec.id
        JOIN labs l ON sch.lab_id = l.id
        JOIN professor_subjects ps ON sch.professor_subject_id = ps.id
        JOIN professors p ON ps.professor_id = p.id
        JOIN subjects s ON ps.subject_id = s.id
        ORDER BY 
            FIELD(sch.day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'),
            sch.start_time
    ");

    $schedules = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["status" => "success", "data" => $schedules]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}