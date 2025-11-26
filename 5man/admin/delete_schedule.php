<?php
session_start();
include("../includes/db.php");

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get schedule ID from URL
if (!isset($_GET['id'])) {
    header("Location: schedules.php");
    exit();
}
$id = $_GET['id'];

// Delete schedule
$stmt = $conn->prepare("DELETE FROM schedules WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Redirect back to schedules page
header("Location: schedules.php");
exit();
