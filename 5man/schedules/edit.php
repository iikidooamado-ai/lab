<?php
session_start();
include("../includes/db.php");
require("../includes/functions.php");

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['faculty'])) {
    header("Location: ../auth/login.php");
    exit;
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: schedules.php?error=1");
    exit;
}
$schedule_id = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT s.*, 
           l.lab_name, 
           p.name AS professor_name, 
           sec.section_name AS section_name,
           sub.name AS subject_name
    FROM schedules s
    JOIN labs l ON s.lab_id = l.id
    LEFT JOIN professors p ON s.professor_id = p.id
    JOIN sections sec ON s.section_id = sec.id
    JOIN subjects sub ON s.subject_id = sub.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $schedule_id);
$stmt->execute();
$schedule = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$schedule) {
    header("Location: ../schedules/view.php?error=1");
    exit;
}

$labs = $conn->query("SELECT * FROM labs ORDER BY lab_name")->fetch_all(MYSQLI_ASSOC);
$professors = $conn->query("SELECT * FROM professors ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$sections = $conn->query("SELECT * FROM sections ORDER BY section_name")->fetch_all(MYSQLI_ASSOC);
$subjects = $conn->query("SELECT * FROM subjects ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Schedule | Lab Scheduler</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/style.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/toast.css">
<script src="../assets/toast.js"></script>
</head>
<body class="bg-light">
<?php include("../includes/navbar.php"); ?>

<div class="container mt-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
            <h4 class="mb-0">✏️ Edit Schedule</h4>
        </div>
        <div class="card-body p-4">

            <form method="POST" action="update_schedule.php" class="needs-validation" novalidate>
                <input type="hidden" name="id" value="<?= $schedule['id'] ?>">

                <!-- Lab -->
                <div class="mb-3">
                    <label class="form-label">Lab</label>
                    <select class="form-select" name="lab_id" required>
                        <?php foreach ($labs as $lab): ?>
                            <option value="<?= $lab['id'] ?>" <?= $lab['id'] == $schedule['lab_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($lab['lab_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Professor -->
                <div class="mb-3">
                    <label class="form-label">Professor</label>
                    <select class="form-select" name="professor_id" required>
                        <?php foreach ($professors as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= $p['id'] == $schedule['professor_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Subject -->
                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <select class="form-select" name="subject_id" required>
                        <?php foreach ($subjects as $sub): ?>
                            <option value="<?= $sub['id'] ?>" <?= $sub['id'] == $schedule['subject_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sub['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Section -->
                <div class="mb-3">
                    <label class="form-label">Section</label>
                    <select class="form-select" name="section_id" required>
                        <?php foreach ($sections as $sec): ?>
                            <option value="<?= $sec['id'] ?>" <?= $sec['id'] == $schedule['section_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sec['section_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Day -->
                <div class="mb-3">
                    <label class="form-label">Day</label>
                    <select name="day" class="form-select" required>
                        <?php
                        $days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
                        foreach ($days as $day): ?>
                            <option value="<?= $day ?>" <?= $schedule['day'] === $day ? 'selected' : '' ?>>
                                <?= $day ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Time -->
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Start Time</label>
                        <input type="time" name="start_time" class="form-control"
                               value="<?= $schedule['start_time'] ?>" required>
                    </div>
                    <div class="col">
                        <label class="form-label">End Time</label>
                        <input type="time" name="end_time" class="form-control"
                               value="<?= $schedule['end_time'] ?>" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="../schedules/view.php" class="btn btn-outline-secondary rounded-pill px-4">⬅ Back</a>
                    <button type="submit" class="btn btn-success rounded-pill px-4">💾 Update Schedule</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div id="toast-container" class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3"
     style="z-index: 1100; max-width: 400px;"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
