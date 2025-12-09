<?php

function update_closet($first, $last, $item_type, $quantity)
{
    require_once('dbinfo.php');

    $connection = connect();

    if (!$connection) {
        return false;
    }
    $quantity = (int)$quantity;
    $use_date = date('Y-m-d');
    $query = "INSERT INTO dbclosetuse (user_first_name, user_last_name, use_date, item_type, quantity_taken) 
              VALUES (?, ?, '$use_date', ?, ?)";

    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        mysqli_close($connection);
        return false;
    }

    $binded = mysqli_stmt_bind_param($stmt, "sssi", $first, $last, $item_type, $quantity);

    if (!$binded) {
        return false;
    }

    $result = mysqli_stmt_execute($stmt);

    if (!$result) {
        echo 'there is a problem';
        return false;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $result;
}

function get_all_closet_usage()
{
    require_once('dbinfo.php');

    $connection = connect();

    if (!$connection) {
        return array();
    }

    $query = "SELECT * FROM dbclosetuse ORDER BY use_date DESC";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        mysqli_close($connection);
        return array();
    }

    $usage = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $supplies[] = $row;
    }

    mysqli_close($connection);

    return $usage;
}

function update_closet_quantity($item_name, $quantity, $user)
{
    require_once('dbinfo.php');
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $connection = connect();

    if (!$connection) {
        return false;
    }
    if ($user) {
        $query = "UPDATE dbclosetinventory SET quantity = quantity - ? WHERE item_name = ?";
    } else {
        $query = "UPDATE dbclosetinventory SET quantity = quantity + ? WHERE item_name = ?";
    }
    $stmt = mysqli_prepare($connection, $query);

    if (!$stmt) {
        mysqli_close($connection);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "is", $quantity, $item_name);

    $result = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $result;
}
