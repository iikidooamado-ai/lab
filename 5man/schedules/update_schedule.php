<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['faculty'])) {
    header("Location: ../auth/login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = intval($_POST['id'] ?? 0);
    $lab_id       = $_POST['lab_id'] ?? null;
    $section_id   = $_POST['section_id'] ?? null;
    $professor_id = $_POST['professor_id'] ?? null;
    $subject      = trim($_POST['subject'] ?? "");
    $day          = $_POST['day'] ?? null;
    $start_time   = $_POST['start_time'] ?? null;
    $end_time     = $_POST['end_time'] ?? null;

    if (!$id || !$lab_id || !$section_id || !$professor_id || !$subject || !$day || !$start_time || !$end_time) {
        header("Location: view.php?error=Missing required fields");
        exit;
    }

    $sql = "UPDATE schedules 
            SET lab_id = ?, section_id = ?, professor_id = ?, subject = ?, day = ?, start_time = ?, end_time = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        header("Location: view.php?error=Prepare failed: " . urlencode($conn->error));
        exit;
    }

    $stmt->bind_param(
        "iiissssi",
        $lab_id,
        $section_id,
        $professor_id,
        $subject,
        $day,
        $start_time,
        $end_time,
        $id
    );

    if ($stmt->execute()) {
        header("Location: view.php?msg=" . urlencode(" Schedule updated successfully"));
    } else {
        header("Location: view.php?error=" . urlencode("Database error: " . $stmt->error));
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: view.php?error=Invalid request");
    exit;
}
