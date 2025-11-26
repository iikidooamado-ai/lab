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

if (isset($_POST['delete_all'])) {
    $conn->query("DELETE FROM schedules");
    echo "<script>window.location.href='schedules.php?msg=All schedules deleted successfully.';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Schedules | Lab Scheduler</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/style.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/toast.css">

  <style>
    /* General layout */
    #schedule-table thead th { position: sticky; top: 0; z-index: 1; }
    .lab-badge {
      display:inline-block; padding:0.35rem 0.65rem;
      border-radius:0.5rem; color:#fff; font-size:0.85rem; font-weight:600;
    }
    .action-toolbar {
      display:flex; flex-wrap:wrap; gap:.5rem; margin-top:1rem; margin-bottom:1rem;
    }
    .action-toolbar .btn {
      border-radius:50rem!important; box-shadow:0 2px 5px rgba(0,0,0,.1);
      display:flex; align-items:center; gap:.35rem; font-weight:500;
      transition:all .2s ease;
    }
    .action-toolbar .btn:hover {
      transform:translateY(-1px); box-shadow:0 4px 10px rgba(0,0,0,.15);
    }
    .week-select {
      border-radius:50rem; padding:.25rem .75rem;
      border:1px solid #ddd; font-size:.9rem; outline:none;
    }

    @media (max-width:768px){
      .action-toolbar { overflow-x:auto; white-space:nowrap; -webkit-overflow-scrolling:touch; padding-bottom:.5rem; }
      .action-toolbar .btn,.action-toolbar .week-select { flex:0 0 auto; }
      #schedule-table { display:none; }
      .schedule-card { border-left:5px solid #0d6efd; margin-bottom:1rem; }
    }

    /* ---------- PRINT STYLES ---------- */
    @media print {
      body {
        background: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      nav, .action-toolbar, .no-print, #toast-container {
        display: none !important;
      }
      .container {
        width: 100%; max-width: 100%; margin: 0; padding: 0;
      }
      table {
        border-collapse: collapse !important;
        width: 100%;
        font-size: 13px;
        color: #000;
      }
      th, td {
        border: 1px solid #000 !important;
        padding: 6px 8px !important;
      }
      thead th {
        background-color: #f0f0f0 !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact;
      }
      thead { display: table-header-group; }
      tfoot { display: table-row-group; }
      tr { page-break-inside: avoid; }

      .shadow-sm, .rounded { box-shadow: none !important; }

      .print-header {
        display: block !important;
        text-align: center;
        margin-bottom: 10px;
      }
      .print-footer {
        display: block !important;
        text-align: center;
        margin-top: 10px;
        font-size: 11px;
        color: #555;
      }
    }
  </style>
</head>
<body>
<?php include("../includes/navbar.php"); ?>

<!-- Toast Container -->
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1100;"></div>

<div class="container mt-4">
  <h3 class="mb-3">Schedules</h3>

  <!-- Action Toolbar -->
  <div class="action-toolbar no-print">
    <form id="auto-set-form" method="post" action="auto_scheduler.php" class="d-flex">
      <select id="week" name="week" class="week-select me-2" required>
        <option value="">Auto Asign Schedule</option>
        <option value="1">Set Schedule</option>
      </select>
      <button id="auto-set-btn" type="submit" name="auto_set" class="btn btn-warning">⚡ Auto Set</button>
    </form>

    <a id="add-btn" href="add_schedule.php" class="btn btn-success">➕ Add</a>

    <form id="delete-all-form" method="post"
          onsubmit="return confirm('⚠️ Delete ALL schedules? This action cannot be undone.');">
      <button id="delete-all-btn" type="submit" name="delete_all" class="btn btn-danger">🗑️ Delete All</button>
    </form>

    <button id="print-btn" onclick="window.print();" class="btn btn-primary">🖨️ Print / Save</button>
  </div>

  <!-- Print Header -->
  <div class="print-header d-none d-print-block">
    <h4 class="fw-bold mb-0">Lab Schedule Overview</h4>
    <p class="mb-1">Week: <span id="print-week"></span></p>
    <hr style="border: 1px solid #000;">
  </div>

  <!-- Table -->
  <div class="table-responsive shadow-sm rounded mt-3">
    <table class="table table-striped table-hover align-middle" id="schedule-table">
      <thead class="table-primary">
        <tr>
                        <th>Subject Name</th>
                        <th>Section</th>
                        <th>Professor Name</th>
                        <th>Room / Lab</th>
                        <th>Day</th>
                        <th>Time</th>
          <th class="no-print">Actions</th>
        </tr>
      </thead>
      <tbody id="scheduleBody">
        <!-- Filled dynamically -->
      </tbody>
    </table>
  </div>

  <!-- Mobile Card View -->
  <div id="scheduleCards" class="d-md-none mt-3">
    <!-- Filled dynamically -->
  </div>

  <!-- Print Footer -->
  <div class="print-footer d-none d-print-block">
    Printed on: <script>document.write(new Date().toLocaleString());</script>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/toast.js"></script>
<script>
function formatTime(t) {
  if (!t) return "";
  const [h, m] = t.split(":");
  let hour = parseInt(h, 10);
  const minutes = m;
  const ampm = hour >= 12 ? "PM" : "AM";
  hour = hour % 12;
  if (hour === 0) hour = 12;
  return `${hour}:${minutes}${ampm}`;
}

document.addEventListener("DOMContentLoaded", () => {
  <?php if (isset($_GET['msg'])): ?>
    showToast("<?= htmlspecialchars($_GET['msg']) ?>", "success");
  <?php endif; ?>

  function loadSchedules() {
    $.ajax({
      url: "../includes/get_schedules.php",
      method: "GET",
      dataType: "json",
      success: function(res) {
        if (res.status === "success") {
          let rows = "", cards = "";
          res.data.forEach(r => {
            rows += `
              <tr class="${r.auto_shift ? 'table-warning' : ''}">
                <td>${r.subject}</td>
                <td><span class="badge bg-secondary">${r.section_name}</span></td>
                <td>${r.professor ? `<span class='badge bg-success'>${r.professor}</span>` : `<span class='badge bg-secondary'>Unassigned</span>`}</td>
                <td><span class="lab-badge" style="background:${r.lab_color || '#6c757d'}">${r.lab_name}</span></td>
                <td>${r.day}</td>
                <td>${formatTime(r.start_time)} - ${formatTime(r.end_time)}</td>
                <td class="no-print">
                  <div class="btn-group">
                    <a href="edit_schedule.php?id=${r.id}" class="btn btn-sm btn-outline-primary">✏️</a>
                    <a href="delete_schedule.php?id=${r.id}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">🗑️</a>
                  </div>
                </td>
              </tr>`;

            cards += `
              <div class="card schedule-card p-3 d-md-none">
                <h6 class="fw-bold text-primary mb-1">${r.day} | ${formatTime(r.start_time)} - ${formatTime(r.end_time)}</h6>
                <p class="mb-1"><strong>Subject:</strong> ${r.subject}</p>
                <p class="mb-1"><strong>Professor:</strong> ${r.professor || 'Unassigned'}</p>
                <p class="mb-1"><strong>Lab:</strong> <span class="lab-badge" style="background:${r.lab_color || '#6c757d'}">${r.lab_name}</span></p>
                <p class="mb-2"><strong>Section:</strong> ${r.section_name}</p>
                <div>
                  <a href="edit_schedule.php?id=${r.id}" class="btn btn-sm btn-outline-primary">✏️ Edit</a>
                  <a href="delete_schedule.php?id=${r.id}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
                </div>
              </div>`;
          });
          $("#scheduleBody").html(rows);
          $("#scheduleCards").html(cards);
        } else {
          $("#scheduleBody").html(`<tr><td colspan="7">${res.message}</td></tr>`);
          $("#scheduleCards").html(`<p class="text-danger">${res.message}</p>`);
        }
      },
      error: function() {
        $("#scheduleBody").html(`<tr><td colspan="7">❌ Failed to load schedules.</td></tr>`);
      }
    });
  }

  // Set week label for print header
  document.getElementById("week").addEventListener("change", e => {
    document.getElementById("print-week").textContent = e.target.value || "";
  });

  loadSchedules();
  window.refreshSchedules = loadSchedules;
});
</script>
</body>
</html>