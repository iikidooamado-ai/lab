<?php
require_once("../config.php");
require_once("../includes/auth.php");

$view = $_GET['view'] ?? 'faculty';
$id   = $_GET['id'] ?? 1;
$term = $_GET['term'] ?? 1;

if ($view === 'faculty') {
    $sql = "SELECT s.day, s.time_start, s.time_end, l.name AS lab, sub.title
            FROM schedules s
            JOIN labs l ON s.lab_id=l.id
            JOIN offerings o ON o.id=s.offering_id
            JOIN subjects sub ON sub.id=o.subject_id
            WHERE s.faculty_id=? AND s.term_id=?";
} else {
    $sql = "SELECT s.day, s.time_start, s.time_end, f.code AS faculty, sub.title
            FROM schedules s
            JOIN faculty f ON s.faculty_id=f.id
            JOIN offerings o ON o.id=s.offering_id
            JOIN subjects sub ON sub.id=o.subject_id
            WHERE s.lab_id=? AND s.term_id=?";
}

$stmt = $pdo->prepare($sql);
$stmt->execute([$id,$term]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="../assets/css/print.css" media="print">
<h2>Printable Schedule (<?= ucfirst($view) ?>)</h2>
<table border="1" cellpadding="5">
<tr>
  <th>Day</th><th>Start</th><th>End</th><th>Info</th>
</tr>
<?php foreach($data as $row): ?>
<tr>
  <td><?= $row['day'] ?></td>
  <td><?= $row['time_start'] ?></td>
  <td><?= $row['time_end'] ?></td>
  <td><?= $view==='faculty' ? $row['lab']." - ".$row['title'] : $row['faculty']." - ".$row['title'] ?></td>
</tr>
<?php endforeach; ?>
</table>
<button onclick="window.print()">Print</button>
