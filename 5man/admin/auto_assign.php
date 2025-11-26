<?php
require_once("../config.php");
require_once("../includes/auth.php");
require_once("../includes/functions.php");

if ($_SESSION['role_id'] != 1) die("Access denied");

$term_id = $_GET['term'] ?? 1; // default test term

auto_assign($pdo, $term_id);

echo "<p>Auto-assign complete for Term $term_id</p>";
echo "<a href='schedules.php?term=$term_id'>View Schedules</a>";
