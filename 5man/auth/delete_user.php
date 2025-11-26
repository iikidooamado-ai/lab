<?php
session_start();
include("../includes/db.php");

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get user ID from URL
if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$id = $_GET['id'];

// Prevent admin from deleting themselves
if ($id == $_SESSION['user_id']) {
    echo "You cannot delete your own account.";
    exit();
}

// Delete user
$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Redirect back to manage users
header("Location: manage_users.php");
exit();
