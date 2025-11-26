<?php
include '../includes/db.php';

// Handle filters
$dayFilter       = $_GET['day'] ?? "All";
$professorFilter = $_GET['professor_id'] ?? "All";
$sectionFilter   = $_GET['section_id'] ?? "All";

// Fetch dropdown data
$professors = $conn->query("SELECT id, name FROM professors ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$sections   = $conn->query("SELECT id, section_name FROM sections ORDER BY section_name")->fetch_all(MYSQLI_ASSOC);

// Build SQL
$sql = "
    SELECT s.id, s.day, s.start_time, s.end_time,
           sec.section_name, 
           l.lab_name, l.color AS lab_color,
           p.name AS professor_name,
           subj.subject_code, subj.subject_name
    FROM schedules s
    JOIN sections sec ON s.section_id = sec.id
    JOIN labs l ON s.lab_id = l.id
    JOIN professor_subjects ps ON s.professor_subject_id = ps.id
    JOIN professors p ON ps.professor_id = p.id
    JOIN subjects subj ON ps.subject_id = subj.id
    WHERE 1=1
";

$params = [];
$types  = "";

if ($dayFilter !== "All") {
    $sql .= " AND s.day = ?";
    $params[] = $dayFilter;
    $types .= "s";
}
if ($professorFilter !== "All") {
    $sql .= " AND p.id = ?";
    $params[] = $professorFilter;
    $types .= "i";
}
if ($sectionFilter !== "All") {
    $sql .= " AND sec.id = ?";
    $params[] = $sectionFilter;
    $types .= "i";
}

$sql .= " ORDER BY 
            FIELD(s.day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'),
            s.start_time ASC";

$stmt = $conn->prepare($sql);
if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedules</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { background-color: #007bff; }
        .navbar-brand { font-weight: bold; color: white !important; }
        .table th { background-color: #007bff; color: white; }
        .table td, .table th { vertical-align: middle; }
        .badge-lab {
            padding: 0.5em 0.9em;
            border-radius: 50px;
            font-weight: 600;
        }
        .btn-primary { background-color: #007bff; border: none; }
        .btn-primary:hover { background-color: #0056b3; }

        /* Print styles */
        @media print {
            .no-print, .navbar, form, button, select, label { display: none !important; }
            .print-header {
                display: block;
                text-align: center;
                margin-bottom: 20px;
                font-size: 20px;
                font-weight: bold;
                color: #000;
            }
            .table th {
                background-color: #007bff !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
            }
            body { background: white; }
        }

        
        .print-header { display: none; }
    </style>
</head>
<body>
<?php include("../includes/navbar.php"); ?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title mb-3 text-primary fw-bold">Schedules</h4>

            
            <form method="GET" class="row g-3 align-items-end mb-4 no-print">
                <div class="col-md-3">
                    <label for="day" class="form-label fw-semibold">Day</label>
                    <select name="day" id="day" class="form-select">
                        <option value="All" <?= $dayFilter === "All" ? "selected" : "" ?>>All</option>
                        <?php
                        $days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
                        foreach ($days as $day) {
                            echo "<option value='$day' " . ($dayFilter === $day ? "selected" : "") . ">$day</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="professor_id" class="form-label fw-semibold">Professor</label>
                    <select name="professor_id" id="professor_id" class="form-select">
                        <option value="All" <?= $professorFilter === "All" ? "selected" : "" ?>>All</option>
                        <?php foreach ($professors as $prof): ?>
                            <option value="<?= $prof['id'] ?>" <?= $professorFilter == $prof['id'] ? "selected" : "" ?>>
                                <?= htmlspecialchars($prof['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="section_id" class="form-label fw-semibold">Section</label>
                    <select name="section_id" id="section_id" class="form-select">
                        <option value="All" <?= $sectionFilter === "All" ? "selected" : "" ?>>All</option>
                        <?php foreach ($sections as $sec): ?>
                            <option value="<?= $sec['id'] ?>" <?= $sectionFilter == $sec['id'] ? "selected" : "" ?>>
                                <?= htmlspecialchars($sec['section_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </form>

            <div class="print-header">
                LAB SCHEDULE REPORT
            </div>
            <div class="table-responsive">
                <table class="table table-striped text-center align-middle shadow-sm">
                    <thead>
                        <tr>
                            <th>Subject Name</th>
                            <th>Section</th>
                            <th>Professor Name</th>
                            <th>Room / Lab</th>
                            <th>Day</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-start">
                                        <?= htmlspecialchars($row['subject_code'] . " - " . $row['subject_name']) ?>

                                        <td class="text-start">
                                        <?= htmlspecialchars($row['section_name']) ?>
                                    </td>

                                    </td>
                                    <td class="text-start">
                                        <?= htmlspecialchars($row['professor_name']) ?>
                                    </td>
                                    <td>
                                        <span class="badge-lab text-white" style="background-color: <?= htmlspecialchars($row['lab_color']) ?>;">
                                            <?= htmlspecialchars($row['lab_name']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['day']) ?></td>
                                    <td><?= date("g:i A", strtotime($row['start_time'])) . " – " . date("g:i A", strtotime($row['end_time'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No schedules found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3 no-print">
                <button onclick="window.print();" class="btn btn-primary">🖨️ Print / Save</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>