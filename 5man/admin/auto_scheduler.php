<?php
session_start();
include("../includes/db.php");
require_once("../includes/functions.php");

if (isset($_POST['auto_set']) && !empty($_POST['week'])) {
    $week = intval($_POST['week']);

    // 🔹 Updated timeslots (1 hour 30 minutes each, with lunch break 12:00-1:00 PM)
    $timeslots = [
        ["07:30:00", "09:00:00"],
        ["09:00:00", "10:30:00"],
        ["10:30:00", "12:00:00"],
        ["13:00:00", "14:30:00"],
        ["14:30:00", "16:00:00"],
        ["16:00:00", "17:30:00"]
    ];

    $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];

    // Fetch all entities
    $sections = $conn->query("SELECT id FROM sections")->fetch_all(MYSQLI_ASSOC);
    $labs     = $conn->query("SELECT id FROM labs")->fetch_all(MYSQLI_ASSOC);
    // Use professor_subjects table which links professor <-> subject
    $prof_subs = $conn->query("SELECT id FROM professor_subjects")->fetch_all(MYSQLI_ASSOC);
    // Fetch subjects (if needed later)
    $subjects = $conn->query("SELECT id, subject_name AS name FROM subjects")->fetch_all(MYSQLI_ASSOC);

    if (empty($sections) || empty($labs) || empty($prof_subs) || empty($subjects)) {
        header("Location: schedules.php?error=⚠️ Missing required data (labs, sections, professor-subject pairs, or subjects)");
        exit;
    }

    $count = 0;
    foreach ($days as $day) {
        foreach ($timeslots as $slot) {
            $section = $sections[$count % count($sections)]['id'];
            $lab     = $labs[$count % count($labs)]['id'];
            $profsub = $prof_subs[$count % count($prof_subs)]['id'];

            // ✅ Conflict check (no overlapping lab, professor, or section)
            $check = $conn->query("
                SELECT 1 FROM schedules
                WHERE week = $week
                AND day = '$day'
                AND (
                    (lab_id = $lab OR professor_subject_id = $profsub OR section_id = $section)
                )
                AND (
                    (start_time < '{$slot[1]}' AND end_time > '{$slot[0]}')
                )
            ");

            if ($check->num_rows == 0) {
                // Insert schedule using professor_subject_id to match schema
                $stmt = $conn->prepare("
                    INSERT INTO schedules 
                    (day, start_time, end_time, professor_subject_id, lab_id, section_id, auto_shift, week)
                    VALUES (?,?,?,?,?,?,1,?)
                ");
                $stmt->bind_param(
                    "sssiiii",
                    $day,
                    $slot[0],
                    $slot[1],
                    $profsub,
                    $lab,
                    $section,
                    $week
                );
                $stmt->execute();
            }

            $count++;
        }
    }

    header("Location: schedules.php?msg=✅ Auto schedule created successfully with 1.5-hour intervals for Week $week");
    exit;
} else {
    header("Location: schedules.php?error=1");
    exit;
}
?>