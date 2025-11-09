<?php

function add_supply_request($item_type, $quantity, $description, $date_submitted) {
    require_once('dbinfo.php');
    
    $connection = connect();
    
    if (!$connection) {
        return false;
    }
    
    $query = "INSERT INTO dbsupplies (item_type, quantity, description, date_submitted, status) 
              VALUES (?, ?, ?, ?, 'pending')";
    
    $stmt = mysqli_prepare($connection, $query);
    
    if (!$stmt) {
        mysqli_close($connection);
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "siss", $item_type, $quantity, $description, $date_submitted);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    
    return $result;
}

function get_all_supply_requests() {
    require_once('dbinfo.php');
    
    $connection = connect();
    
    if (!$connection) {
        return array();
    }
    
    $query = "SELECT * FROM dbsupplies ORDER BY date_submitted DESC";
    $result = mysqli_query($connection, $query);
    
    if (!$result) {
        mysqli_close($connection);
        return array();
    }
    
    $supplies = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $supplies[] = $row;
    }
    
    mysqli_close($connection);
    
    return $supplies;
}

function toggle_supply_status($supply_id, $new_status) {
    require_once('dbinfo.php');
    
    $connection = connect();
    
    if (!$connection) {
        return false;
    }
    
    $query = "UPDATE dbsupplies SET status = ? WHERE supply_id = ?";
    
    $stmt = mysqli_prepare($connection, $query);
    
    if (!$stmt) {
        mysqli_close($connection);
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "si", $new_status, $supply_id);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    
    return $result;
}

function update_supply_quantity($supply_id, $quantity) {
    require_once('dbinfo.php');
    
    $connection = connect();
    
    if (!$connection) {
        return false;
    }
    
    $query = "UPDATE dbsupplies SET quantity = ? WHERE supply_id = ?";
    
    $stmt = mysqli_prepare($connection, $query);
    
    if (!$stmt) {
        mysqli_close($connection);
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $quantity, $supply_id);
    
    $result = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    
    return $result;
}

?>