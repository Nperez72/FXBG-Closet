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
        header('Location: viewAllEventSignUps.php?pendingSignupSuccess');
        die();
    }
    header('Location: index.php');
?>