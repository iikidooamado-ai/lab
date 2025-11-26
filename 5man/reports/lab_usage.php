<?php
session_start();
include("../includes/db.php");

// Assumed daily open hours (e.g., 8AM–6PM = 10 hours per day)
$total_capacity_per_day = 10; 

// Fetch total usage per lab
$query = "
    SELECT l.id, l.lab_name, COALESCE(SUM(TIMESTAMPDIFF(MINUTE, s.start_time, s.end_time)), 0) AS minutes_used
    FROM labs l
    LEFT JOIN schedules s ON s.lab_id = l.id
    GROUP BY l.id, l.lab_name
    ORDER BY l.lab_name ASC
";
$result = $conn->query($query);

$labs = [];
$used_hours = [];
$free_hours = [];

while ($row = $result->fetch_assoc()) {
    $labs[] = $row['lab_name'];
    $hours_used = round($row['minutes_used'] / 60, 2);
    $hours_free = max(0, $total_capacity_per_day - $hours_used);

    $used_hours[] = $hours_used;
    $free_hours[] = $hours_free;
}
$result->free();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Lab Utilization & Availability</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">
    <h2>Lab Utilization & Availability Report</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Lab Hours (Used vs Free)</h5>
            <canvas id="labUsageChart" height="120"></canvas>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Currently Free Labs</h5>
            <ul class="list-group">
                <?php foreach ($labs as $index => $lab): ?>
                    <?php if ($used_hours[$index] == 0): ?>
                        <li class="list-group-item text-success fw-bold">
                            ✅ <?php echo $lab; ?> (Completely Free)
                        </li>
                    <?php elseif ($free_hours[$index] > 0): ?>
                        <li class="list-group-item">
                            🟢 <?php echo $lab; ?> (<?php echo $free_hours[$index]; ?> hrs free left)
                        </li>
                    <?php else: ?>
                        <li class="list-group-item text-danger">
                            ❌ <?php echo $lab; ?> (Fully Used)
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('labUsageChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labs); ?>,
        datasets: [
            {
                label: 'Hours Used',
                data: <?php echo json_encode($used_hours); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                stack: 'usage'
            },
            {
                label: 'Hours Free',
                data: <?php echo json_encode($free_hours); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                stack: 'usage'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Lab Usage vs Availability (Weekly)' }
        },
        scales: {
            x: { stacked: true },
            y: {
                stacked: true,
                beginAtZero: true,
                max: <?php echo $total_capacity_per_day; ?>,
                title: { display: true, text: 'Hours (per week)' }
            }
        }
    }
});
</script>
</body>
</html>
