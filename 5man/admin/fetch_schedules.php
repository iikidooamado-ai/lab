<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("../includes/db.php");

$query = "
 SELECT s.*, 
       l.lab_name AS lab_name, 
       l.color AS lab_color,
       sec.section_name AS section_name,
       p.name AS professor_name,
       subj.name AS subject_name,
       s.auto_shift
FROM schedules s
JOIN labs l ON s.lab_id = l.id
JOIN sections sec ON s.section_id = sec.id
JOIN subjects subj ON s.subject_id = subj.id
LEFT JOIN professors p ON s.professor_id = p.id
ORDER BY FIELD(s.day,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), s.start_time";

$result = $conn->query($query);

while ($row = $result->fetch_assoc()):
?>
<tr class="<?= $row['auto_shift'] ? 'table-warning' : '' ?>">
    <td><?= htmlspecialchars($row['day']) ?></td>
    <td><?= date("h:i A", strtotime($row['start_time'])) ?> - <?= date("h:i A", strtotime($row['end_time'])) ?></td>
    <td><?= htmlspecialchars($row['subject_name']) ?></td>
    <td>
        <?= $row['professor_name'] 
            ? '<span class="badge bg-success">'.htmlspecialchars($row['professor_name']).'</span>' 
            : '<span class="badge bg-secondary">Unassigned</span>' ?>
    </td>
    <td>
        <span class="lab-badge" style="background:<?= $row['lab_color'] ?: '#6c757d' ?>">
            <?= htmlspecialchars($row['lab_name']) ?>
        </span>
    </td>
    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['section_name']) ?></span></td>
    <td class="no-print">
        <div class="btn-group">
            <a href="edit_schedule.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">✏️</a>
            <a href="delete_schedule.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Are you sure?')">🗑️</a>
        </div>
    </td>
</tr>
<?php endwhile; ?>