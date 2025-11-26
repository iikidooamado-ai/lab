<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
session_start();
include("../includes/db.php");

// Get filter values
$filter_lab = isset($_GET['lab_id']) ? intval($_GET['lab_id']) : 0;
$filter_section = isset($_GET['section_id']) ? intval($_GET['section_id']) : 0;
$filter_period = isset($_GET['period']) ? $_GET['period'] : 'all'; // all, week, month

// --- Labs & Sections lists ---
$labs_result = $conn->query("SELECT id, lab_name FROM labs ORDER BY lab_name ASC");
$labs = $labs_result->fetch_all(MYSQLI_ASSOC);

$sections_result = $conn->query("SELECT id, section_name FROM sections ORDER BY section_name ASC");
$sections = $sections_result->fetch_all(MYSQLI_ASSOC);

// --- Date filter ---
$date_condition = "";
if ($filter_period === 'week') {
    $date_condition = " AND s.day >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ";
} elseif ($filter_period === 'month') {
    $date_condition = " AND s.day >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) ";
}

// --- Lab Utilization Data ---
$lab_data_query = "
    SELECT l.lab_name, SUM(TIMESTAMPDIFF(MINUTE, s.start_time, s.end_time)) AS minutes_used
    FROM schedules s
    JOIN labs l ON s.lab_id = l.id
    WHERE 1
";

if ($filter_lab) $lab_data_query .= " AND l.id = $filter_lab ";
$lab_data_query .= $date_condition . " GROUP BY s.lab_id ORDER BY minutes_used DESC ";

$lab_result = $conn->query($lab_data_query);
$lab_labels = [];
$lab_data = [];
while($row = $lab_result->fetch_assoc()) {
    $lab_labels[] = $row['lab_name'];
    $lab_data[] = round($row['minutes_used']/60, 2);
}
$lab_result->free();

// --- Section vs Lab Usage Data ---
$datasets = [];
$colors = ['rgba(54,162,235,0.7)','rgba(255,99,132,0.7)','rgba(255,206,86,0.7)',
           'rgba(75,192,192,0.7)','rgba(153,102,255,0.7)','rgba(255,159,64,0.7)'];

foreach ($sections as $index => $section) {
    if ($filter_section && $section['id'] != $filter_section) continue;

    $data = [];
    foreach ($labs as $lab) {
        if ($filter_lab && $lab['id'] != $filter_lab) continue;

        $stmt = $conn->prepare("
            SELECT SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time)) AS minutes_used
            FROM schedules s
            WHERE section_id=? AND lab_id=? $date_condition
        ");
        $stmt->bind_param("ii", $section['id'], $lab['id']);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $minutes = $res['minutes_used'] ?? 0;
        $data[] = round($minutes/60, 2);
        $stmt->close();
    }

    $datasets[] = [
        'label' => $section['section_name'],
        'data' => $data,
        'backgroundColor' => $colors[$index % count($colors)]
    ];
}

// Helper for filter labels
function getFilterLabel($filter, $list, $default, $field) {
    foreach($list as $item) {
        if($item['id'] == $filter) return htmlspecialchars($item[$field]);
    }
    return $default;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports Dashboard | Lab Scheduler</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* Default: charts resize nicely */
canvas {
    max-width: 100%;
    height: auto !important;
}

/* Print only charts */
@media print {
    body * {
        visibility: hidden;
    }
    #print-area, #print-area * {
        visibility: visible;
    }
    #print-area {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
    }

    /* Force A4 scaling */
    @page {
        size: A4 portrait; /* switch to landscape if you prefer */
        margin: 10mm;
    }

    canvas {
        max-width: 100% !important;
        max-height: 95% !important;
    }
}
</style>
</head>
<body>
<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">
    <h2>Reports Dashboard</h2>

    <!-- Filters Form -->
    <form class="row g-3 mb-3" method="get" id="filters-area">
        <div class="col-md-3">
            <label for="lab_id" class="form-label">Lab</label>
            <select name="lab_id" id="lab_id" class="form-select">
                <option value="0">All Labs</option>
                <?php foreach($labs as $lab): ?>
                    <option value="<?= $lab['id']; ?>" <?= $filter_lab==$lab['id'] ? 'selected':'' ?>>
                        <?= htmlspecialchars($lab['lab_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="section_id" class="form-label">Section</label>
            <select name="section_id" id="section_id" class="form-select">
                <option value="0">All Sections</option>
                <?php foreach($sections as $sec): ?>
                    <option value="<?= $sec['id']; ?>" <?= $filter_section==$sec['id'] ? 'selected':'' ?>>
                        <?= htmlspecialchars($sec['section_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="period" class="form-label">Period</label>
            <select name="period" id="period" class="form-select">
                <option value="all" <?= $filter_period=='all' ? 'selected':'' ?>>All Time</option>
                <option value="week" <?= $filter_period=='week' ? 'selected':'' ?>>Last 7 Days</option>
                <option value="month" <?= $filter_period=='month' ? 'selected':'' ?>>Last 30 Days</option>
            </select>
        </div>
        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
        </div>
    </form>

    <!-- Filters Summary -->
    <div class="mb-3 p-2 border rounded" id="filter-summary">
        <strong>Filters Applied:</strong>
        Lab: <?= getFilterLabel($filter_lab, $labs, "All Labs", "lab_name"); ?> |
        Section: <?= getFilterLabel($filter_section, $sections, "All Sections", "section_name"); ?> |
        Period: <?= ucfirst($filter_period); ?>
    </div>

    <!-- Print Button -->
    <div class="mb-3 text-end">
        <button class="btn btn-primary ms-2" onclick="window.print();">Print - Save</button>
    </div>

    <!-- Charts (only this prints) -->
    <div id="print-area">
        <div class="chart-wrapper mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">Lab Utilization (Hours)</div>
                <div class="card-body">
                    <canvas id="labUsageChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-wrapper mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">Section vs Lab Usage (Hours)</div>
                <div class="card-body">
                    <canvas id="sectionLabChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Lab Utilization Chart
const labCtx = document.getElementById('labUsageChart').getContext('2d');
new Chart(labCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($lab_labels); ?>,
        datasets: [{
            label: 'Hours Used',
            data: <?= json_encode($lab_data); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
});

// Section vs Lab Usage Chart
const sectionCtx = document.getElementById('sectionLabChart').getContext('2d');
new Chart(sectionCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($labs, 'lab_name')); ?>,
        datasets: <?= json_encode($datasets); ?>
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { tooltip: { mode: 'index', intersect: false }, legend: { position: 'top' } },
        interaction: { mode: 'nearest', axis: 'x', intersect: false },
        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, title: { display: true, text: 'Hours' } } }
    }
});
</script>
</body>
</html>
