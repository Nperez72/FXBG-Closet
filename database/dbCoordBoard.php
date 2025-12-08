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

    $role_name = "";
    if ($type === "coord") {
        $role_name = "Volunteer Coordinator";
    } else {
        $role_name = "Board Member";
    }
    $rid = 1;

    $placeholder = uniqid("temp_", true); // temp value for $id, to be updated to ensure uniqueness

    mysqli_stmt_close($check_stmt);

    $stmt = $con->prepare("INSERT INTO dbpersons (id, role_type, role_name, first_name, last_name, phone1, phone1type, emergency_contact_phone, emergency_contact_phone_type, email, emergency_contact_first_name, emergency_contact_relation, emergency_contact_last_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssssssssss", $placeholder, $rid, $role_name, $first_name, $last_name, $phone1, $phone1type, $emergency_phone, $emergency_phone_type, $email, $emergency_first_name, $emergency_relation, $emergency_last_name);

    if (!$stmt->execute()) {
        $stmt->close();
        $con->close();
        return false;
    }
    
    $person_id = $con->insert_id;
    $anid = "";
    if ($type === "coord") {
        $anid = "coord" . $person_id;
    } else {
        $anid = "boardm" . $person_id;
    }

    $update = $con->prepare(
        "UPDATE dbpersons SET id = ? WHERE person_id = ?"
    );

    if (!$update) {
        $stmt->close();
        $con->close();
        return false;
    }

    $update->bind_param("si", $anid, $person_id);
    $update->execute();

    $stmt->close();
    $update->close();
    $con->close();
    return true;
}
