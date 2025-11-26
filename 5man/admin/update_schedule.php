<?php
session_start();
include("../includes/db.php");
require_once("../includes/functions.php");

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id                   = intval($_POST['id'] ?? 0);
    $lab_id               = intval($_POST['lab_id'] ?? 0);
    $section_id           = intval($_POST['section_id'] ?? 0);
    $professor_subject_id = intval($_POST['professor_subject_id'] ?? 0);
    $day                  = trim($_POST['day'] ?? '');
    $start_time           = trim($_POST['start_time'] ?? '');
    $end_time             = trim($_POST['end_time'] ?? '');

    // ---- Basic validation ----
    if (!$id || !$lab_id || !$section_id || !$professor_subject_id || !$day || !$start_time || !$end_time) {
        header("Location: edit_schedule.php?id=$id&error=" . urlencode("Missing required fields"));
        exit;
    }

    if (strtotime($end_time) <= strtotime($start_time)) {
        header("Location: edit_schedule.php?id=$id&error=" . urlencode("End time must be after start time"));
        exit;
    }

    $prof_stmt = $conn->prepare("SELECT professor_id FROM professor_subjects WHERE id = ?");
    $prof_stmt->bind_param("i", $professor_subject_id);
    $prof_stmt->execute();
    $prof_result = $prof_stmt->get_result()->fetch_assoc();
    $prof_stmt->close();

    if (!$prof_result) {
        header("Location: edit_schedule.php?id=$id&error=" . urlencode("Invalid professor/subject pair"));
        exit;
    }

    $professor_id = intval($prof_result['professor_id']);

    // -------------------------------------------------------
    //  LAB CONFLICT CHECK — prevent overlap or duplicate
    // -------------------------------------------------------
    $lab_conflict_sql = "
        SELECT id 
        FROM schedules 
        WHERE lab_id = ? 
          AND day = ?
          AND id != ?
          AND (
                -- partial overlap (any intersection)
                (start_time < ? AND end_time > ?) OR
                (start_time >= ? AND start_time < ?) OR
                -- exact duplicate time (same start & end)
                (start_time = ? AND end_time = ?)
              )
    ";
    $lab_stmt = $conn->prepare($lab_conflict_sql);
    $lab_stmt->bind_param(
        "isiisssss",
        $lab_id,
        $day,
        $id,
        $end_time,
        $start_time,
        $start_time,
        $end_time,
        $start_time,
        $end_time
    );
    $lab_stmt->execute();
    $lab_conflict = $lab_stmt->get_result()->num_rows > 0;
    $lab_stmt->close();

    if ($lab_conflict) {
        header("Location: edit_schedule.php?id=$id&error=" . urlencode("❌ Conflict detected! Another schedule already uses this lab on $day between $start_time and $end_time."));
        exit;
    }

    // -------------------------------------------------------
    //  PROFESSOR CONFLICT CHECK — prevent double booking
    // -------------------------------------------------------
    $prof_conflict_sql = "
        SELECT s.id 
        FROM schedules s
        JOIN professor_subjects ps ON s.professor_subject_id = ps.id
        WHERE ps.professor_id = ?
          AND s.day = ?
          AND s.id != ?
          AND (
                -- partial overlap
                (s.start_time < ? AND s.end_time > ?) OR
                (s.start_time >= ? AND s.start_time < ?) OR
                -- exact same time
                (s.start_time = ? AND s.end_time = ?)
              )
    ";
    $prof_stmt = $conn->prepare($prof_conflict_sql);
    $prof_stmt->bind_param(
        "isiisssss",
        $professor_id,
        $day,
        $id,
        $end_time,
        $start_time,
        $start_time,
        $end_time,
        $start_time,
        $end_time
    );
    $prof_stmt->execute();
    $prof_conflict = $prof_stmt->get_result()->num_rows > 0;
    $prof_stmt->close();

    if ($prof_conflict) {
        header("Location: edit_schedule.php?id=$id&error=" . urlencode("❌ This professor already has another schedule on $day between $start_time and $end_time."));
        exit;
    }

    // -------------------------------------------------------
    //  NO CONFLICT — Update schedule
    // -------------------------------------------------------
    $sql = "
        UPDATE schedules 
        SET lab_id = ?, section_id = ?, professor_subject_id = ?, day = ?, start_time = ?, end_time = ? 
        WHERE id = ?
    ";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header("Location: edit_schedule.php?id=$id&error=" . urlencode("Prepare failed: " . $conn->error));
        exit;
    }

    $stmt->bind_param("iiisssi", $lab_id, $section_id, $professor_subject_id, $day, $start_time, $end_time, $id);

    if ($stmt->execute()) {
        header("Location: schedules.php?msg=" . urlencode(" Schedule updated successfully"));
    } else {
        header("Location: edit_schedule.php?id=$id&error=" . urlencode("Database error: " . $stmt->error));
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: schedules.php?error=" . urlencode("Invalid request"));
    exit;
}
?>