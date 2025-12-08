<?php

require_once('dbinfo.php');

function create_coordinator_board($type, $first_name, $last_name, $phone1, $phone1type, $emergency_phone, $emergency_phone_type, $email, $emergency_first_name, $emergency_last_name, $emergency_relation)
{
    $con = connect();
    ini_set("display_errors", 1);
    error_reporting(E_ALL);

    // does the coordinator already exist?
    $check_stmt = $con->prepare("SELECT first_name, last_name FROM dbpersons WHERE first_name LIKE ? AND last_name LIKE ? LIMIT 1");
    if (!$check_stmt) {
        mysqli_close($con);
        return false;
    }

    $check_stmt->bind_param("ss", $first_name, $last_name);
    $check_stmt->execute();
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        // Username already exists
        mysqli_stmt_close($check_stmt);
        mysqli_close($con);
        return 'duplicate';
    }

    $id = 99;
    if ($res = mysqli_query($con, "SELECT * FROM dbpersons")) {

        $id = mysqli_num_rows( $res ) + 1;
    
    } else {
        mysqli_close($con);
        return false;
    }
    
    $anid = "";
    $role_name = "";
    if ($type === "coord") {
        $role_name = "Volunteer Coordinator";
        $anid = "coordinator" . (string)$id;
    } else {
        $role_name = "Board Member";
        $anid = "boardm" . (string)$id;
    }

    mysqli_stmt_close($check_stmt);

    $rid = 1;
    
    $stmt = $con->prepare("INSERT INTO dbpersons (person_id, id, role_type, role_name, first_name, last_name, phone1, phone1type, emergency_contact_phone, emergency_contact_phone_type, email, emergency_contact_first_name, emergency_contact_relation, emergency_contact_last_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isisssssssssss", $id, $anid, $rid, $role_name, $first_name, $last_name, $phone1, $phone1type, $emergency_phone, $emergency_phone_type, $email, $emergency_first_name, $emergency_relation, $emergency_last_name);

    $stmt->execute();
    $stmt->close();
    $con->close();
    return true;
}

