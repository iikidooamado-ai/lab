<?php
session_start();
include("../includes/db.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user info
$stmt = $conn->prepare("SELECT username, role FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, password=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $password_hash, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=? WHERE id=?");
        $stmt->bind_param("si", $username, $user_id);
    }

    $stmt->execute();
    $_SESSION['username'] = $username; // update session

    // Redirect with success
    header("Location: profile1.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile | Lab Scheduler</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">
    <h2>Edit Profile</h2>
    <form method="POST" style="max-width: 500px;">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label>New Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Update Profile</button>
        <a href="../schedules/view.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
