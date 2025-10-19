<?php

function add_activity($person_id, $date, $hours_spent, $activity_description) {
    require_once('dbinfo.php');
    
    $connection = connect();
    
    if (!$connection) {
        return false;
    }
    
    $start_time = "00:00:00";
    $end_time = "00:00:00";
    $photo_id = NULL;
    
    $query = "INSERT INTO dbvolunteeractivity (person_id, date, start_time, end_time, event_id, interactions, photo_id) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($connection, $query);
    
    if (!$stmt) {
        mysqli_close($connection);
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "issdsi", $person_id, $date, $start_time, $end_time, $hours_spent, $activity_description, $photo_id);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    
    return $result;
}

?>