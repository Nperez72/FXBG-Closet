<?php

include_once('dbinfo.php');

function add_financial_log($log)
{
    $connection = connect();
    if (!$connection) {
        echo "<script>console.log('Failed to connect to the database');</script>";
        return false;
    }


    $stmt = mysqli_prepare($connection, "INSERT INTO `dbfinanciallogs` (`reporter_id`, `event_id not null`, `date`, `time`, `amount`, `description`) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        mysqli_close($connection);
        return false;
    }
    $reporter_id = $log['reporter_id'];
    $event_id = $log['event_id'];
    $amount = $log['amount'];
    $description = $log['description'];
    $date = date("Y-m-d");
    $time = date("H:i:s");
    mysqli_stmt_bind_param($stmt, "iissds", $reporter_id, $event_id, $date, $time, $amount, $description);
    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $success;
}
