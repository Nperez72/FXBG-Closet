<?php

require_once('dbinfo.php');

function add_event_coordinator($event_id, $coordinator_id)
{
    $con = connect();

    $stmt = $con->prepare("INSERT INTO dbevent_coordinators (event_id, coordinator_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $event_id, $coordinator_id);

    $stmt->execute();
    $stmt->close();
    $con->close();
}

function get_event_coordinators($event_id)
{
    $con = connect();
    $stmt = $con->prepare("SELECT coordinator_id FROM dbevent_coordinators WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $ids = [];
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['coordinator_id'];
    }

    $stmt->close();
    $con->close();

    return $ids;
}

function delete_event_coordinators($event_id)
{
    $con = connect();
    $stmt = $con->prepare("DELETE FROM dbevent_coordinators WHERE event_id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();
    $con->close();
}
