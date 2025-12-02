<?php

function add_media($media_id, $activity_id, $file_name, $type, $file_format, $description, $alternate_name, $time_created)
{
    require_once('dbinfo.php');

    $connection = connect();

    if (!$connection) {
        return false;
    }

     $query = "INSERT INTO dbeventmedia (media_id, activity_id,	file_name, type, file_format, description, alternate_name, time_created) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        mysqli_close($connection);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "iissssss", $media_id, $activity_id, $file_name, $type, $file_format, $description, $alternate_name, $time_created);

    $result = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $result;
}


/**
 * Get event name by event ID
 *
 * @param int $event_id The event ID
 * @return string|null The event name or null if not found
 */
function get_event_name_by_id($event_id)
{
    require_once('dbinfo.php');

    $connection = connect();

    if (!$connection) {
        return null;
    }

    $query = "SELECT name FROM dbevents WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        mysqli_close($connection);
        return null;
    }

    mysqli_stmt_bind_param($stmt, "i", $event_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $row ? $row['name'] : null;
}

function get_weekly_volunteer_hours($start_date, $end_date)
{
    require_once('dbinfo.php');

    $connection = connect();

    if (!$connection) {
        return array();
    }

    $query = "SELECT 
            MIN(date) as week_start,
            WEEK(date) as week_number,
            YEAR(date) as year,
            SUM(hours) as total_hours
          FROM dbvolunteeractivity 
          WHERE date BETWEEN ? AND ?
          GROUP BY YEAR(date), WEEK(date)
          ORDER BY week_start ASC";

    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        mysqli_close($connection);
        return array();
    }

    mysqli_stmt_bind_param($stmt, "ss", $start_date, $end_date);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $weekly_data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $weekly_data[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $weekly_data;
}

function get_monthly_volunteer_hours($start_date, $end_date)
{
    require_once('dbinfo.php');

    $connection = connect();

    if (!$connection) {
        return array();
    }

    $query = "SELECT 
                MIN(date) as month_start,
                YEAR(date) as year,
                MONTH(date) as month,
                SUM(hours) as total_hours
              FROM dbvolunteeractivity 
              WHERE date BETWEEN ? AND ?
              GROUP BY YEAR(date), MONTH(date)
              ORDER BY month_start ASC";

    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        mysqli_close($connection);
        return array();
    }

    mysqli_stmt_bind_param($stmt, "ss", $start_date, $end_date);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $monthly_data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $monthly_data[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $monthly_data;
}

/**
 * Add a new activity record
 *
 * @param string $name Person's name
 * @param string $role Person's role ('volunteer', 'board member', 'volunteer coordinator')
 * @param string $date Activity date (YYYY-MM-DD format)
 * @param float $hours_spent Hours spent on activity
 * @param int $event_id Event ID
 * @param string|null $email Optional email address
 * @return int|false The new activity_id on success, false on failure
 */
function add_activity($name, $role, $date, $hours_spent, $event_id, $email = null)
{
    require_once('dbinfo.php');

    $connection = connect();
    if (!$connection) {
        return false;
    }

    $query = "INSERT INTO dbvolunteeractivity (name, role, date, hours, event_id, email) 
              VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($connection, $query);
    if (!$stmt) {
        mysqli_close($connection);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "sssdis", $name, $role, $date, $hours_spent, $event_id, $email);

    $activity_id = mysqli_stmt_execute($stmt) ? mysqli_insert_id($connection) : false;

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $activity_id;
}

function get_monthly_volunteer_hours_by_email($start_date, $end_date, $email)
{
    require_once('dbinfo.php');

    $connection = connect();

    if (!$connection) {
        return array();
    }

    $query = "SELECT 
                MIN(date) as month_start,
                YEAR(date) as year,
                MONTH(date) as month,
                SUM(hours) as total_hours
              FROM dbvolunteeractivity 
              WHERE date BETWEEN ? AND ? AND email = ?
              GROUP BY YEAR(date), MONTH(date)
              ORDER BY year ASC, month ASC";

    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        mysqli_close($connection);
        return array();
    }

    mysqli_stmt_bind_param($stmt, "sss", $start_date, $end_date, $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $monthly_data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $monthly_data[] = $row;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $monthly_data;
}

function get_all_activity_emails()
{
    require_once('dbinfo.php');

    $connection = connect();

    if (!$connection) {
        return array();
    }

    $query = "SELECT DISTINCT email FROM dbvolunteeractivity WHERE email IS NOT NULL AND email != '' ORDER BY email ASC";

    $result = mysqli_query($connection, $query);

    if (!$result) {
        mysqli_close($connection);
        return array();
    }

    $emails = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $emails[] = $row['email'];
    }

    mysqli_close($connection);

    return $emails;
}

function save_interaction_demographics(int $activity_id, array $age_data, array $ethnicity_data): bool
{
    require_once('dbinfo.php');

    $connection = connect();

    if ($activity_id <= 0) {
        error_log("Invalid activity_id provided: $activity_id");
        mysqli_close($connection);
        return false;
    }

    $age_0_12 = (int)($age_data['0_12'] ?? 0);
    $age_13_17 = (int)($age_data['13_17'] ?? 0);
    $age_18_24 = (int)($age_data['18_24'] ?? 0);
    $age_25_54 = (int)($age_data['25_54'] ?? 0);
    $age_55_plus = (int)($age_data['55_plus'] ?? 0);

    $ethnicity_white = (int)($ethnicity_data['white'] ?? 0);
    $ethnicity_black = (int)($ethnicity_data['black'] ?? 0);
    $ethnicity_hispanic = (int)($ethnicity_data['hispanic'] ?? 0);
    $ethnicity_asian = (int)($ethnicity_data['asian'] ?? 0);
    $ethnicity_native = (int)($ethnicity_data['native'] ?? 0);
    $ethnicity_other = (int)($ethnicity_data['other'] ?? 0);

    // Prepare the SQL statement
    $query = "INSERT INTO dbinteractiondemographics (
                activity_id, 
                age_0_12, 
                age_13_17, 
                age_18_24, 
                age_25_54, 
                age_55_plus,
                ethnicity_white,
                ethnicity_black,
                ethnicity_hispanic,
                ethnicity_asian,
                ethnicity_native,
                ethnicity_other
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $connection->prepare($query);

    if (!$stmt) {
        error_log("Failed to prepare statement: " . $connection->error);
        mysqli_close($connection);
        return false;
    }

    $stmt->bind_param(
        "iiiiiiiiiiii",
        $activity_id,
        $age_0_12,
        $age_13_17,
        $age_18_24,
        $age_25_54,
        $age_55_plus,
        $ethnicity_white,
        $ethnicity_black,
        $ethnicity_hispanic,
        $ethnicity_asian,
        $ethnicity_native,
        $ethnicity_other
    );

    $result = $stmt->execute();

    if (!$result) {
        error_log("Failed to save interaction demographics for activity_id $activity_id: " . $stmt->error);
        $stmt->close();
        mysqli_close($connection);
        return false;
    }

    $stmt->close();
    mysqli_close($connection);
    return true;
}
