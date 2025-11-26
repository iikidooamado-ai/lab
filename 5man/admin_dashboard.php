<?php
session_start();
include("includes/db.php");

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth/login.php");
    exit();
}

// Safe username for display
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | Lab Scheduler</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        min-height: 100vh;
        margin: 0;
    }
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        transition: transform 0.2s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .dashboard-container {
        max-width: 1000px;
        margin: 60px auto;
    }
</style>
</head>
<body>
<?php include("includes/navbar.php"); ?>

<div class="container dashboard-container">
    <div class="row g-4">

        <!-- Add Schedules -->
        <div class="col-md-6">
            <div class="card text-white bg-success h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Add Schedules</h5>
                    <p class="card-text">Setup Schedule Plan</p>
                    <a href="/5man/admin/add_schedule.php" class="btn btn-light mt-auto">Go</a>
                </div>
            </div>
        </div>

        <!-- Manage Schedules -->
        <div class="col-md-6">
            <div class="card text-white bg-success h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Schedule Report</h5>
                    <p class="card-text">Edit/View Schedules assign</p>
                    <a href="/5man/admin/schedules.php" class="btn btn-light mt-auto">Go</a>
                </div>
            </div>
        </div>

        <!-- Admin Profile -->
        <div class="col-md-6">
            <div class="card text-white bg-primary h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Admin Profile</h5>
                    <p class="card-text">View or edit your admin profile information.</p>
                    <a href="/5man/auth/profile.php" class="btn btn-light mt-auto">Go</a>
                </div>
            </div>
        </div>

        <!-- Reports -->
        <div class="col-md-6">
            <div class="card text-white bg-warning h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Reports</h5>
                    <p class="card-text">View lab usage and availability</p>
                    <a href="/5man/reports/lab_usage.php" class="btn btn-light mt-auto">Go</a>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>