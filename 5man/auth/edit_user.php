<?php
session_start();
include("../includes/db.php");

// Only allow admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get user ID from URL
if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$id = $_GET['id'];

// Fetch user data
$stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id=?");
$stmt->bind_param("i", $id);
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
    $role = $_POST['role'];

    // Optional: update password if provided
    if (!empty($_POST['password'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, role=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $role, $password_hash, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $role, $id);
    }
    $stmt->execute();
    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit User | Lab Scheduler</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">
    <h2>Edit User</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>

        <div class="mb-3">
            <label>Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-select" required>
                <option value="admin" <?php if($user['role']=='admin') echo 'selected'; ?>>Admin</option>
                <option value="faculty" <?php if($user['role']=='faculty') echo 'selected'; ?>>Faculty</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update User</button>
        <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
