<?php
// Database connection settings
$host = "localhost";   
$user = "root";       
$pass = "";            
$db   = "project";     

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

//delete all schedule 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_all'])) {
    $conn->query("TRUNCATE TABLE schedules"); 
    // or use: $conn->query("DELETE FROM schedules");
    header("Location: schedules.php?msg=All schedules deleted");
    exit;
}

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
