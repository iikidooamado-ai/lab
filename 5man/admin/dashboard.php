<?php
require_once("../config.php");
require_once("../includes/auth.php");

if ($_SESSION['role_id'] != 1) { // 1 = admin
    die("Access denied");
}

include("../includes/header.php");
?>
<h2>Admin Dashboard</h2>
<ul>
  <li><a href="offerings.php">Manage Offerings</a></li>
  <li><a href="schedules.php">View/Edit Schedules</a></li>
  <li><a href="auto_assign.php">Run Auto Assignment</a></li>
  <li><a href="print.php">Print Schedules</a></li>
</ul>
<?php include("../includes/footer.php"); ?>
