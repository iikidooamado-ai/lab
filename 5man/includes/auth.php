<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: /5man/auth/login.php");
    exit;
}
