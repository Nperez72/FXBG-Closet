<?php
require_once('dbinfo.php');

/**
 * Inserts a new event report into the database.
 * Returns true on success, false on failure.
 */
function add_event_report($data) {
    $con = connect();

    // Check if a report exists for this event
    $stmt = $con->prepare("SELECT id FROM dbevent_reports WHERE event_id = ?");
    $stmt->bind_param("i", $data['event_id']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Report exists -> update it
        $stmt->close();

        $sql = "UPDATE dbevent_reports
                SET total_attendance=?, age_under_18=?, age_18_24=?, age_25_34=?, age_35_44=?,
                    age_45_54=?, age_55_64=?, age_65_plus=?,
                    ethnicity_american_indian=?, ethnicity_asian=?, ethnicity_black=?, ethnicity_hispanic=?,
                    ethnicity_pacific_islander=?, ethnicity_white=?,
                    event_cost=?, reimbursement_cost=?
                WHERE event_id=?";

        $stmt = $con->prepare($sql);
        $stmt->bind_param(
            "iiiiiiiiiiiiiiddi",
            $data['total_attendance'],
            $data['age_under_18'], $data['age_18_24'], $data['age_25_34'], $data['age_35_44'],
            $data['age_45_54'], $data['age_55_64'], $data['age_65_plus'],
            $data['ethnicity_american_indian'], $data['ethnicity_asian'], $data['ethnicity_black'], $data['ethnicity_hispanic'],
            $data['ethnicity_pacific_islander'], $data['ethnicity_white'],
            $data['event_cost'], $data['reimbursement_cost'],
            $data['event_id']
        );
    } else {
        // Insert new report
        $stmt->close();
        $sql = "INSERT INTO dbevent_reports 
            (event_id, total_attendance, age_under_18, age_18_24, age_25_34, age_35_44,
             age_45_54, age_55_64, age_65_plus,
             ethnicity_american_indian, ethnicity_asian, ethnicity_black, ethnicity_hispanic,
             ethnicity_pacific_islander, ethnicity_white, event_cost, reimbursement_cost)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param(
            "iiiiiiiiiiiiiiidd",
            $data['event_id'],
            $data['total_attendance'],
            $data['age_under_18'], $data['age_18_24'], $data['age_25_34'], $data['age_35_44'],
            $data['age_45_54'], $data['age_55_64'], $data['age_65_plus'],
            $data['ethnicity_american_indian'], $data['ethnicity_asian'], $data['ethnicity_black'], $data['ethnicity_hispanic'],
            $data['ethnicity_pacific_islander'], $data['ethnicity_white'],
            $data['event_cost'], $data['reimbursement_cost']
        );
    }

    $result = $stmt->execute();
    $stmt->close();
    $con->close();
    return $result;
}

function fetch_event_report($event_id) {
    $con = connect();
    $stmt = $con->prepare("
        SELECT *
        FROM dbevent_reports
        WHERE event_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $event_id);

    if (!$stmt->execute()) {
        error_log("Failed to execute statement: " . $stmt->error);
        $stmt->close();
        return null;
    }

    $result = $stmt->get_result();
    $report = $result->fetch_assoc();
    $stmt->close();
    return $report ?: null;
}

function get_attendance_per_event($start_date, $end_date) {
    $con = connect();

    $sql = "SELECT e.id, e.name, e.date,
                   r.total_attendance,
                   r.age_under_18, r.age_18_24, r.age_25_34, r.age_35_44,
                   r.age_45_54, r.age_55_64, r.age_65_plus,
                   r.ethnicity_american_indian, r.ethnicity_asian,
                   r.ethnicity_black, r.ethnicity_hispanic,
                   r.ethnicity_pacific_islander, r.ethnicity_white
            FROM dbevents e
            INNER JOIN dbevent_reports r ON e.id = r.event_id
            WHERE e.date BETWEEN ? AND ?
            ORDER BY e.date ASC";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
    $con->close();
    return $data;
}
