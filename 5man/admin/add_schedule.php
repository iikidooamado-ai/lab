<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../includes/db.php");
require_once("../includes/functions.php");
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';

// Fetch dropdown data
$labs     = $conn->query("SELECT * FROM labs ORDER BY lab_name")->fetch_all(MYSQLI_ASSOC);
$sections = $conn->query("SELECT * FROM sections ORDER BY section_name")->fetch_all(MYSQLI_ASSOC);
$subjects = $conn->query("SELECT * FROM subjects ORDER BY subject_code")->fetch_all(MYSQLI_ASSOC);

// Fetch professor + subject pairs
$professorSubjects = $conn->query("
    SELECT ps.id, p.name AS professor_name, s.subject_code, s.subject_name
    FROM professor_subjects ps
    JOIN professors p ON ps.professor_id = p.id
    JOIN subjects s ON ps.subject_id = s.id
    ORDER BY p.name, s.subject_code
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Schedule | Lab Scheduler</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/style.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/toast.css">
  <script src="../assets/toast.js"></script>
</head>
<body class="bg-light">

<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
<?php include("../includes/navbar.php"); ?>

<div class="container mt-3">
  <div class="card shadow-lg border-0 rounded-4">
    <div class="card-header bg-primary text-white rounded-top-4">
      <h4 class="mb-0">Add New Schedule</h4>
    </div>
    <div class="card-body p-4">
      <!-- Form -->
      <form id="addScheduleForm" action="save_schedule.php" method="POST" class="row g-3 needs-validation" novalidate>
        
        <!-- Lab -->
        <div class="col-md-6">
          <label for="lab_id" class="form-label fw-semibold">Select Lab</label>
          <div class="input-group">
            <select id="lab_id" class="form-select" name="lab_id" required>
              <option value="">-- Select Lab --</option>
              <?php foreach ($labs as $lab): ?>
                <option value="<?= $lab['id'] ?>"><?= htmlspecialchars($lab['lab_name']) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addLabModal">Add</button>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#manageLabsModal">Delete</button>
          </div>
        </div>

        <!-- Section -->
        <div class="col-md-6">
          <label for="section_id" class="form-label fw-semibold">Select Section</label>
          <div class="input-group">
            <select id="section_id" class="form-select" name="section_id" required>
              <option value="">-- Select Section --</option>
              <?php foreach ($sections as $s): ?>
                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['section_name']) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">Add</button>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#manageSectionsModal">Delete</button>
          </div>
        </div>

        <!-- Professor + Subject -->
        <div class="col-md-6">
          <label for="professor_subject_id" class="form-label fw-semibold">Professor & Subject</label>
          <div class="input-group">
            <select id="professor_subject_id" name="professor_subject_id" class="form-select" required>
              <option value="">-- Select Professor & Subject --</option>
              <?php foreach ($professorSubjects as $ps): ?>
                <option value="<?= $ps['id'] ?>">
                  <?= htmlspecialchars($ps['professor_name'] . " - " . $ps['subject_code'] . " (" . $ps['subject_name'] . ")") ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addProfessorSubjectModal">Add</button>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#manageProfessorSubjectsModal">Delete</button>
          </div>
        </div>

        <!-- Subjects -->
        <div class="col-md-6">
          <label for="subject_id" class="form-label fw-semibold">Subjects</label>
          <div class="input-group">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">Add</button>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#manageSubjectsModal">Delete</button>
          </div>
        </div>

        <!-- Day -->
        <div class="col-md-6">
          <label for="day" class="form-label fw-semibold">Day</label>
          <select id="day" name="day" class="form-select" required>
            <option value="">-- Select Day --</option>
            <option>Monday</option>
            <option>Tuesday</option>
            <option>Wednesday</option>
            <option>Thursday</option>
            <option>Friday</option>
            <option>Saturday</option>
          </select>
        </div>

        <!-- Time -->
        <div class="col-md-6">
          <label for="start_time" class="form-label fw-semibold">Time</label>
          <div class="d-flex gap-2">
            <input type="time" id="start_time" name="start_time" class="form-control" required>
            <input type="time" id="end_time" name="end_time" class="form-control" required>
          </div>
        </div>

        <!-- Buttons -->
        <div class="col-12 d-flex justify-content-end gap-2 mt-4">
          <a href="../admin_dashboard.php" class="btn btn-secondary">⬅ Back</a>
          <button type="submit" class="btn btn-success">Save Schedule</button>
        </div>

      </form>
    </div>
  </div>
</div>

<!-- ================= LAB MODALS ================= -->
<div class="modal fade" id="addLabModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addLabForm">
        <div class="modal-header">
          <h5 class="modal-title">Add Lab</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label for="lab_name" class="form-label">Lab Name</label>
          <input type="text" id="lab_name" name="lab_name" class="form-control mb-3" required>
          <label for="lab_color" class="form-label">Color</label>
          <input type="color" id="lab_color" name="color" class="form-control" value="#2196f3">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="manageLabsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manage Labs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group" id="labsList"></ul>
      </div>
    </div>
  </div>
</div>

<!-- ================= SECTION MODALS ================= -->
<div class="modal fade" id="addSectionModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addSectionForm">
        <div class="modal-header">
          <h5 class="modal-title">Add Section</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label for="section_name" class="form-label">Section Name</label>
          <input type="text" id="section_name" name="section_name" class="form-control" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="manageSectionsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manage Sections</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group" id="sectionsList"></ul>
      </div>
    </div>
  </div>
</div>

<!-- ================= PROFESSOR + SUBJECT MODALS ================= -->
<div class="modal fade" id="addProfessorSubjectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addProfessorSubjectForm">
        <div class="modal-header">
          <h5 class="modal-title">Add Professor & Subject</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label for="professor_name" class="form-label">Professor Name</label>
          <input type="text" id="professor_name" name="professor_name" class="form-control mb-3" required>
          <label for="subject_id" class="form-label">Subject</label>
          <select id="subject_id" name="subject_id" class="form-select" required>
            <?php
            $subjects = $conn->query("SELECT id, subject_code, subject_name FROM subjects ORDER BY subject_code")->fetch_all(MYSQLI_ASSOC);
            foreach ($subjects as $sub):
            ?>
              <option value="<?= $sub['id'] ?>"><?= htmlspecialchars($sub['subject_code']." - ".$sub['subject_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="manageProfessorSubjectsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manage Professor & Subject</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group" id="professorSubjectsList"></ul>
      </div>
    </div>
  </div>
</div>

<!-- ================= SUBJECT MODALS ================= -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addSubjectForm">
        <div class="modal-header">
          <h5 class="modal-title">Add Subject</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Subject Code</label>
            <input type="text" name="subject_code" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Subject Name</label>
            <input type="text" name="subject_name" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="manageSubjectsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manage Subjects</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group" id="subjectsList">
          
        </ul>
      </div>
    </div>
  </div>
</div>


<!-- jQuery + Bootstrap + JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/toast.js"></script>
<script src="../assets/scheduler.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("addScheduleForm");
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        try {
            const response = await fetch("save_schedule.php", { method: "POST", body: formData });
            const result = await response.json();
            if (result.success) {
                showToast(result.message, "success");
                form.reset();
                setTimeout(() => location.reload(), 800);
            } else {
                showToast(result.message, "error");
            }
        } catch (err) {
            showToast("❌ Failed to save schedule (network error).", "error");
        }
    });
});
</script>
</body>
</html>