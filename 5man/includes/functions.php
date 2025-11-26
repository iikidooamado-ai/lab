<?php
// includes/functions.php
// Clean, guarded utility functions for the scheduler app.
// - No output or headers here (safe to include).
// - Expects a mysqli $conn passed to functions that need DB access.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('formatTimeForDisplay')) {
    function formatTimeForDisplay($time) {
        if (empty($time)) return '';
        $ts = strtotime($time);
        if ($ts === false) return '';
        return date("g:iA", $ts);
    }
}

/**
 * @param mysqli $conn
 * @param array $data  keys: lab_id, professor_subject_id, section_id, day, start_time, end_time
 * @return array result ['success'=>bool, 'message'=>string, 'insert_id'=>int|null]
 */
if (!function_exists('addSchedule')) {
    function addSchedule($conn, array $data) {
        // Validate and normalize input
        $lab_id     = isset($data['lab_id']) ? (int)$data['lab_id'] : 0;
        $prof_sub   = isset($data['professor_subject_id']) ? (int)$data['professor_subject_id'] : 0;
        $section_id = isset($data['section_id']) ? (int)$data['section_id'] : 0;
        $day        = isset($data['day']) ? trim($data['day']) : '';
        $start_time = isset($data['start_time']) ? trim($data['start_time']) : '';
        $end_time   = isset($data['end_time']) ? trim($data['end_time']) : '';

        if (!$lab_id || !$prof_sub || !$section_id || !$day || !$start_time || !$end_time) {
            return ['success' => false, 'message' => 'Missing required fields.'];
        }

        // Normalize time strings to H:i:s if possible
        $st = strtotime($start_time);
        $et = strtotime($end_time);
        if ($st === false || $et === false) {
            return ['success' => false, 'message' => 'Invalid time format.'];
        }
        $start_time = date("H:i:s", $st);
        $end_time   = date("H:i:s", $et);

        // Basic sanity: start must be before end
        if (strtotime($start_time) >= strtotime($end_time)) {
            return ['success' => false, 'message' => 'Start time must be before end time.'];
        }

        $cutoff = "18:00:00";
        $maxAttempts = 20;
        $attempts = 0;

        // -- First, enforce section non-overlap rule
        $checkSecSql = "SELECT id FROM schedules WHERE section_id = ? AND day = ? AND (start_time < ? AND end_time > ?) LIMIT 1";
        $stmt = $conn->prepare($checkSecSql);
        if (!$stmt) {
            return ['success'=>false, 'message' => 'Database prepare error (section-check): ' . $conn->error];
        }
        $stmt->bind_param("isss", $section_id, $day, $end_time, $start_time);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $stmt->close();
            return ['success'=>false, 'message' => "⚠️ Section already has a subject at this time on {$day}."];
        }
        $stmt->close();

        // -- Conflict resolution loop
        $conflictFound = true;
        while ($conflictFound && $attempts < $maxAttempts) {
            $attempts++;

            $confSql = "SELECT * FROM schedules WHERE day = ? AND (start_time < ? AND end_time > ?) LIMIT 1";
            $stmt = $conn->prepare($confSql);
            if (!$stmt) {
                return ['success'=>false, 'message' => 'Database prepare error (conflict-select): ' . $conn->error];
            }
            $stmt->bind_param("sss", $day, $end_time, $start_time);
            $stmt->execute();
            $conflict = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($conflict) {
                $conf_lab = (int)$conflict['lab_id'];
                $conf_sec = (int)$conflict['section_id'];

                // If same section & same lab => shift times
                if ($conf_sec === $section_id && $conf_lab === $lab_id) {
                    $start_time = date("H:i:s", strtotime($start_time) + 3600);
                    $end_time   = date("H:i:s", strtotime($end_time) + 3600);
                    if (strtotime($end_time) > strtotime($cutoff)) {
                        return ['success' => false, 'message' => '⚠️ No available time slot before cutoff.'];
                    }
                    continue;
                }

                // If same lab but different section => try to reassign lab
                if ($conf_lab === $lab_id && $conf_sec !== $section_id) {
                    $labCheckSql = "
                        SELECT id FROM labs
                        WHERE id NOT IN (
                            SELECT lab_id FROM schedules
                            WHERE day = ? AND (start_time < ? AND end_time > ?)
                        )
                        LIMIT 1
                    ";
                    $labStmt = $conn->prepare($labCheckSql);
                    if (!$labStmt) {
                        return ['success' => false, 'message' => 'Database prepare error (lab-check): ' . $conn->error];
                    }
                    $labStmt->bind_param("sss", $day, $end_time, $start_time);
                    $labStmt->execute();
                    $freeLab = $labStmt->get_result()->fetch_assoc();
                    $labStmt->close();

                    if ($freeLab) {
                        $lab_id = (int)$freeLab['id'];
                        $conflictFound = false;
                        break;
                    } else {
                        $start_time = date("H:i:s", strtotime($start_time) + 3600);
                        $end_time   = date("H:i:s", strtotime($end_time) + 3600);
                        if (strtotime($end_time) > strtotime($cutoff)) {
                            return ['success' => false, 'message' => ' No available lab/time before cutoff.'];
                        }
                        continue;
                    }
                }

                // Otherwise just shift
                $start_time = date("H:i:s", strtotime($start_time) + 3600);
                $end_time   = date("H:i:s", strtotime($end_time) + 3600);
                if (strtotime($end_time) > strtotime($cutoff)) {
                    return ['success' => false, 'message' => ' No available time slot before cutoff.'];
                }
            } else {
                $conflictFound = false;
            }
        }

        if ($conflictFound) {
            return ['success' => false, 'message' => ' Conflict resolution exceeded attempts, please adjust manually.'];
        }

        //  Insert final schedule (new schema: professor_subject_id)
        $insertSql = "
            INSERT INTO schedules (lab_id, section_id, professor_subject_id, day, start_time, end_time)
            VALUES (?, ?, ?, ?, ?, ?)
        ";
        $insStmt = $conn->prepare($insertSql);
        if (!$insStmt) {
            return ['success'=>false, 'message' => 'Database prepare error (insert): ' . $conn->error];
        }
        // types: 3 ints, then 3 strings => i i i s s s
        $insStmt->bind_param("iiisss", $lab_id, $section_id, $prof_sub, $day, $start_time, $end_time);
        if ($insStmt->execute()) {
            $id = $insStmt->insert_id;
            $insStmt->close();
            return ['success' => true, 'message' => ' Schedule saved successfully.', 'insert_id' => $id];
        } else {
            $err = $insStmt->error;
            $insStmt->close();
            return ['success' => false, 'message' => ' Database error: ' . $err];
        }
    }
}