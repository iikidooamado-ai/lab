<?php
session_start();
include("../includes/db.php");

// -------------------- LABS --------------------
$labs_result = $conn->query("SELECT id, lab_name FROM labs ORDER BY lab_name ASC");
$labs = $labs_result->fetch_all(MYSQLI_ASSOC);

// -------------------- FACULTIES --------------------
$faculties_result = $conn->query("SELECT id, username FROM users WHERE role='faculty' ORDER BY username ASC");
$faculties = $faculties_result->fetch_all(MYSQLI_ASSOC);

// -------------------- Faculty Usage Dataset --------------------
$datasets = [];
$colors = [
    'rgba(54, 162, 235, 0.7)',
    'rgba(255, 99, 132, 0.7)',
    'rgba(255, 206, 86, 0.7)',
    'rgba(75, 192, 192, 0.7)',
    'rgba(153, 102, 255, 0.7)',
    'rgba(255, 159, 64, 0.7)'
];

foreach ($faculties as $index => $faculty) {
    $data = [];
    foreach ($labs as $lab) {
        $stmt = $conn->prepare("
            SELECT SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as minutes_used
            FROM schedules
            WHERE faculty_id=? AND lab_id=?
        ");
        $stmt->bind_param("ii", $faculty['id'], $lab['id']);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $minutes = $res['minutes_used'] ?? 0;
        $data[] = round($minutes/60, 2); // convert to hours
        $stmt->close();
    }

    $datasets[] = [
        'label' => $faculty['username'],
        'data' => $data,
        'backgroundColor' => $colors[$index % count($colors)]
    ];
}

// -------------------- Lab Availability Tracker by Day --------------------
// Assuming labs open 8AM–6PM (10 hrs per day).
$total_capacity_per_day = 10;
$days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

$availability_data = []; // structured like: $availability_data[lab][day] = [used, free]

foreach ($labs as $lab) {
    foreach ($days as $day) {
        $stmt = $conn->prepare("
            SELECT SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as minutes_used
            FROM schedules
            WHERE lab_id=? AND DAYNAME(start_time)=?
        ");
        $stmt->bind_param("is", $lab['id'], $day);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $minutes = $res['minutes_used'] ?? 0;
        $used = round($minutes/60, 2);
        $free = max(0, $total_capacity_per_day - $used);

        $availability_data[$lab['lab_name']][$day] = [
            "used" => $used,
            "free" => $free
        ];
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lab Usage & Availability</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">
    <h2 class="mb-4">Lab Usage Reports</h2>

    <!-- Faculty vs Lab Usage -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Faculty vs Lab Usage</h5>
            <canvas id="facultyLabChart" height="120"></canvas>
        </div>
    </div>

    <!-- Lab Availability Tracker (Daily) -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Lab Availability Tracker (Mon–Sat)</h5>
            <canvas id="labAvailabilityChart" height="200"></canvas>
        </div>
    </div>
</div>

<script>
// Faculty vs Lab Usage
const facultyLabCtx = document.getElementById('facultyLabChart').getContext('2d');
new Chart(facultyLabCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($labs, 'lab_name')); ?>,
        datasets: <?php echo json_encode($datasets); ?>
    },
    options: {
        responsive: true,
        plugins: {
            title: { display: true, text: 'Faculty Hours per Lab' },
            tooltip: { mode: 'index', intersect: false },
            legend: { position: 'top' }
        },
        interaction: { mode: 'nearest', axis: 'x', intersect: false },
        scales: {
            x: { stacked: true },
            y: { stacked: true, beginAtZero: true, title: { display: true, text: 'Hours' } }
        }
    }
});

// Lab Availability Tracker by Day
const labAvailabilityCtx = document.getElementById('labAvailabilityChart').getContext('2d');
const availabilityData = <?php echo json_encode($availability_data); ?>;
const days = <?php echo json_encode($days); ?>;

const datasetsAvail = [];
days.forEach((day, idx) => {
    const usedData = [];
    const freeData = [];
    for (const lab in availabilityData) {
        usedData.push(availabilityData[lab][day]["used"]);
        freeData.push(availabilityData[lab][day]["free"]);
    }

    datasetsAvail.push({
        label: `${day} - Used`,
        data: usedData,
        backgroundColor: "rgba(255,99,132,0.7)",
        stack: day
    });
    datasetsAvail.push({
        label: `${day} - Free`,
        data: freeData,
        backgroundColor: "rgba(75,192,192,0.7)",
        stack: day
    });
});

new Chart(labAvailabilityCtx, {
    type: 'bar',
    data: {
        labels: Object.keys(availabilityData),
        datasets: datasetsAvail
    },
    options: {
        responsive: true,
        plugins: {
            title: { display: true, text: 'Daily Lab Availability (Used vs Free Hours)' },
            tooltip: { mode: 'index', intersect: false },
            legend: { position: 'top' }
        },
        interaction: { mode: 'nearest', axis: 'x', intersect: false },
        scales: {
            x: { stacked: true },
            y: { stacked: true, beginAtZero: true, max: 10, title: { display: true, text: 'Hours (per day)' } }
        }
    }
});
</script>
</body>
</html>
