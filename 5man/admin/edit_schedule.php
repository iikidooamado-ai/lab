<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin'])) {
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
           sec.section_name,
           ps.id AS professor_subject_id,
           p.name AS professor_name, 
           sub.subject_name
    FROM schedules s
    JOIN labs l ON s.lab_id = l.id
    JOIN sections sec ON s.section_id = sec.id
    JOIN professor_subjects ps ON s.professor_subject_id = ps.id
    JOIN professors p ON ps.professor_id = p.id
    JOIN subjects sub ON ps.subject_id = sub.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $schedule_id);
$stmt->execute();
$schedule = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$schedule) {
    header("Location: schedules.php?error=1");
    exit;
}

$labs = $conn->query("SELECT * FROM labs ORDER BY lab_name")->fetch_all(MYSQLI_ASSOC);
$sections = $conn->query("SELECT * FROM sections ORDER BY section_name")->fetch_all(MYSQLI_ASSOC);

// Fetch professor-subject pairs
$professor_subjects = $conn->query("
    SELECT ps.id, p.name AS professor_name, sub.subject_name
    FROM professor_subjects ps
    JOIN professors p ON ps.professor_id = p.id
    JOIN subjects sub ON ps.subject_id = sub.id
    ORDER BY p.name, sub.subject_name
")->fetch_all(MYSQLI_ASSOC);
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

                <!-- Professor + Subject -->
                <div class="mb-3">
                    <label class="form-label">Professor & Subject</label>
                    <select class="form-select" name="professor_subject_id" required>
                        <?php foreach ($professor_subjects as $ps): ?>
                            <option value="<?= $ps['id'] ?>" <?= $ps['id'] == $schedule['professor_subject_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ps['professor_name'] . " - " . $ps['subject_name']) ?>
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
                    <a href="schedules.php" class="btn btn-outline-secondary rounded-pill px-4">⬅ Back</a>
                    <button type="submit" class="btn btn-success rounded-pill px-4">💾 Update Schedule</button>
                </div>
                <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
            </form>

        </div>
    </div>
</div>

<div id="toast-container" class="toast-container position-fixed bottom-0 start-50 translate-middle-x p-3"
     style="z-index: 1100; max-width: 400px;"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            // Smooth fade out
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 600); // Remove from DOM after fade
        }, 4000); // Auto-hide after 4 seconds
    });
});
</script>
</body>
</html>