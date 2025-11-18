<?php

function add_activity($person_id, $date, $event_id, $hours_spent, $activity_description)
{
    require_once('dbinfo.php');

    $connection = connect();

    if (!$connection) {
        return false;
    }

    $hours = (float)$hours_spent;
    $photo_id = null;

     $query = "INSERT INTO dbvolunteeractivity (person_id, date, hours, event_id, interactions, photo_id) 
              VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        mysqli_close($connection);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "isdisi", $person_id, $date, $hours, $event_id, $activity_description, $photo_id);

    $result = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $result;
}

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
