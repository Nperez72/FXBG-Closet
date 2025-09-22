<?php
    session_cache_expire(30);
    session_start();
    
    /*if ($_SESSION['access_level'] < 2 || $_SERVER['REQUEST_METHOD'] != 'POST') {
        header('Location: index.php');
        die();
    }*/

    require_once('database/dbEvents.php');
    require_once('include/input-validation.php');
    $args = sanitize($_POST);
    $id = $args['id'];
    $user_id = $args['user_id'];
    $position = $args['position'];
    $notes = $args['notes'];

    if (!$id) {
        header('Location: index.php');
        die();
    }
    if (approve_signup($id, $user_id, $position, $notes)) {
        require_once('database/dbMessages.php');
        $event = fetch_event_by_id($id);
        $event_name = htmlspecialchars_decode($event['name']);
        send_system_message($user_id, "Your restricted event signup has been approved", "You are now signed up for $event_name. Congratulations!");
        header('Location: viewEventSignUps.php?pendingSignupSuccess&id=' . $id);
        die();
    }
    header('Location: index.php');
?>