<?php
session_start();
include("../includes/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["username"] = $user["username"];

        // Redirect based on role
        if ($user["role"] === "admin") {
            header("Location: ../home.php");   // Admin home
        } elseif ($user["role"] === "faculty") {
            header("Location: ../home1.php");  // Faculty home
        } else {
            header("Location: ../home.php");   // Default
        }
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Lab Scheduler</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/style1.css" rel="stylesheet">
</head>
<body>
  <img src="../assets/nisu.png" alt="Logo" class="logo">

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow border-0">
          <div class="card-body p-4">
            <h2 class="text-center mb-4 neon-text">Login</h2>

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
              <button type="submit" class="btn btn-gradient w-100">Login</button>
            </form>

            <div class="mt-3 text-center">
              <small>Don't have an account? <a href="register.php">Register here</a></small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
