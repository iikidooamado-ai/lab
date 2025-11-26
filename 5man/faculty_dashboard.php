<?php
session_start();
include("includes/db.php");

// Only allow faculty
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: auth/login.php");
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Faculty';
// Safe username for display
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Faculty Dashboard | Lab Scheduler</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("includes/navbar.php"); ?>

<div class="container mt-4">
    <h2>Welcome</h2>

    <div class="row mt-4">
        <!-- View My Schedule -->
        <div class="col-md-6 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">My Schedule</h5>
                    <p class="card-text">View your assigned lab schedules.</p>
                    <a href="/5man/schedules/view.php" class="btn btn-light mt-auto">View</a>
                </div>
            </div>
        </div>

        <!-- Profile / Settings -->
        <div class="col-md-6 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Profile</h5>
                    <p class="card-text">View or update your account info.</p>
                    <a href="/5man/auth/profile1.php" class="btn btn-light mt-auto">Go</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
