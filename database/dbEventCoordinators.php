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

function get_events_for_coordinator($person_id) {
    $con = connect();
    $stmt = $con->prepare("
        SELECT e.* 
        FROM dbevents e
        INNER JOIN dbevent_coordinators ec ON e.id = ec.event_id
        WHERE ec.coordinator_id = ?
        ORDER BY e.date ASC
    ");
    $stmt->bind_param("i", $person_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $events = [];
    while ($result_row = $result->fetch_assoc()) {
        $events[] = new Event(
            $result_row['id'],
            $result_row['name'],
            date: $result_row['date'],
            startTime: $result_row['startTime'],
            endTime: $result_row['endTime'],
            description: $result_row['description'],
            capacity: $result_row['capacity'],
            completed: $result_row['completed'],
            restricted_signup: $result_row['restricted_signup'],
            type: $result_row['type']
        );
    }
    $stmt->close();
    $con->close();
    return $events;
}