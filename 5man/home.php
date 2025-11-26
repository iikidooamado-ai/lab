<?php
session_start();
include("includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}

// Get total schedules today
$today = date("Y-m-d");
$stmt = $conn->prepare("SELECT COUNT(*) AS total_today FROM schedules WHERE DATE(start_time) = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$total_today = $result['total_today'] ?? 0;

// Get available labs
$labs_result = $conn->query("SELECT COUNT(*) AS total_labs FROM labs");
$labs = $labs_result->fetch_assoc();
$total_labs = $labs['total_labs'] ?? 0;

// Get ongoing classes (current time between start and end)
$current_time = date("H:i:s");
$ongoing_result = $conn->query("SELECT COUNT(*) AS ongoing FROM schedules WHERE CURTIME() BETWEEN start_time AND end_time");
$ongoing = $ongoing_result->fetch_assoc();
$total_ongoing = $ongoing['ongoing'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Home | Lab Scheduler</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="assets/home.css" rel="stylesheet">
  <style>
    .dashboard-card {
      border-radius: 1rem;
      padding: 1.5rem;
      color: #fff;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
      transition: transform 0.2s ease-in-out;
    }
    .dashboard-card:hover {
      transform: translateY(-5px);
    }
    .card-icon {
      font-size: 2.5rem;
      opacity: 0.8;
    }
  </style>
</head>
<body>
  <?php include("includes/navbar.php"); ?>

  <div class="container mt-4">
    <h2 class="mb-4 text-center">Welcome, <?= htmlspecialchars($_SESSION["username"]); ?> 🎉</h2>

    <div class="row g-4">
     <!-- Total Schedules Today -->
<div class="col-md-4">
  <div class="dashboard-card bg-primary text-center">
    <i class="bi bi-calendar-day card-icon"></i>
    <h3><?= $total_today ?></h3>
    <p>Schedules Today</p>
  </div>
</div>

<!-- Available Labs -->
<div class="col-md-4">
  <div class="dashboard-card bg-success text-center">
    <i class="bi bi-laptop card-icon"></i>
    <h3><?= $total_labs ?></h3>
    <p>Available Labs</p>
  </div>
</div>

<!-- Ongoing Classes -->
<div class="col-md-4">
  <div class="dashboard-card bg-warning text-dark text-center">
    <i class="bi bi-easel2 card-icon"></i>
    <h3><?= $total_ongoing ?></h3>
    <p>Ongoing Classes</p>
  </div>
</div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
