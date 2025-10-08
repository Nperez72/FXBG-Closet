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
    $notes = $args['notes'];
    $position = $args['position'];

    if (!$id) {
        header('Location: index.php');
        die();
    }
    if (reject_signup($id, $user_id, $notes, $position)) {
        require_once('database/dbMessages.php');
        $event = fetch_event_by_id($id);
        $event_name = htmlspecialchars_decode($event['name']);
        send_system_message($user_id, "Your sign-up for $event_name has been denied.", "Your sign up for $event_name has been denied.");
        header('Location: viewEventSignUps.php?pendingSignupSuccess&id=' . $id);
        die();
    }
    header('Location: index.php');
?>