<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$loggedIn = isset($_SESSION['user_id']);
$role = $loggedIn && isset($_SESSION['role']) ? $_SESSION['role'] : '';
$username = $loggedIn && isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>

<!-- Bootstrap CSS (include in <head>) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom Navbar Styles -->
<style>
  html, body {
    margin: 0;
    padding: 0;
  }

  body {
    padding-top: 70px; /* Prevent content from hiding behind fixed navbar */
  }

  .navbar-custom {
    background: linear-gradient(90deg, #4dafff, #1e90ff); /* light → medium blue gradient */
    margin-top: 0 !important;
  }

  .navbar-custom .navbar-brand,
  .navbar-custom .nav-link {
    color: #fff !important;
    transition: color 0.2s ease-in-out;
  }

  .navbar-custom .nav-link:hover,
  .navbar-custom .nav-link.active {
    color: #ffeb3b !important; /* yellow highlight on hover/active */
    font-weight: 600;
  }

  .navbar-custom .btn-outline-light:hover {
    background-color: #ffeb3b;
    border-color: #ffeb3b;
    color: #000;
  }

  .navbar-custom .btn-primary {
    background-color: #ffeb3b;
    border: none;
    color: #000;
  }

  .navbar-custom .btn-primary:hover {
    background-color: #fdd835;
    color: #000;
  }
</style>

<nav class="navbar navbar-expand-lg navbar-custom shadow-sm fixed-top">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center fw-bold" href="/5man/home.php">
      <img src="/5man/assets/nisu.png" alt="Logo" width="32" height="32" class="me-2 rounded">
      Lab Scheduler
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <?php if($loggedIn): ?>
            

            <?php if($role === 'admin'): ?>
                <li class="nav-item">
                <li class="nav-item">
                <a class="nav-link<?= basename($_SERVER['PHP_SELF']) === 'home.php' ? ' active' : '' ?>" href="/5man/home.php">Home</a>
            </li>
                    <a class="nav-link<?= basename($_SERVER['PHP_SELF']) === 'admin_dashboard.php' ? ' active' : '' ?>" href="/5man/admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= strpos($_SERVER['PHP_SELF'], 'reports') !== false ? ' active' : '' ?>" href="/5man/reports/dashboard.php">Lab Utilazation Report</a>
                </li>
            <?php elseif($role === 'faculty'): ?>
            <li class="nav-item">
                <a class="nav-link<?= basename($_SERVER['PHP_SELF']) === 'home1.php' ? ' active' : '' ?>" href="/5man/home1.php">Home</a>
            </li>
                <li class="nav-item">
                    <a class="nav-link<?= basename($_SERVER['PHP_SELF']) === 'faculty_dashboard.php' ? ' active' : '' ?>" href="/5man/faculty_dashboard.php">Dashboard</a>
                </li>
            <?php endif; ?>

            <!-- Username + Logout -->
            <li class="nav-item ms-lg-3">
                <a class="btn btn-sm btn-outline-light px-3" href="/5man/auth/logout.php">
                    Logout <?= $username ? "(".htmlspecialchars($username).")" : ""; ?>
                </a>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a class="btn btn-sm btn-primary px-3" href="/5man/auth/login.php">Login</a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Bootstrap JS (before </body>) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
