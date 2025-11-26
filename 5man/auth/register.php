<?php
include("../includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);

    if ($stmt->execute()) {
    echo "<p style='color: green;'>User registered successfully. Redirecting...</p>";
    header("Refresh:2; url=login.php"); // wait 2 seconds then redirect
    exit();
        } else {
    echo "<p style='color: red;'>Error inserting user: " . $stmt->error . "</p>";
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | Lab Scheduler</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="../assets/style1.css" rel="stylesheet">
</head>
<body>
    <img src="../assets/nisu.png" alt="Logo" class="logo">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
               
                <div class="card shadow border-0 p-4">
                    <h2 class="text-center mb-4 neon-text">Register</h2>

                    <?php if (!empty($error)) { ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php } ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="faculty">Faculty</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Register</button>
                    </form>

                    <div class="mt-3 text-center">
                        <small>Already have an account? <a href="login.php">Login here</a></small>
                    </div>
                </div>
              
            </div>
        </div>
    </div>
</body>
</html>
