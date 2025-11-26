<?php
session_start();
include("../includes/db.php");


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Profile | Lab Scheduler</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/navbar.php"); ?>
<div class="container mt-4">
    <h2>My Profile</h2>
    <div class="card" style="max-width: 500px;">
        <div class="card-body">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Role:</strong> <?php echo ucfirst($user['role']); ?></p>
            <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
            <a href="../admin_dashboard.php" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Profile updated successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
</body>
</html>
