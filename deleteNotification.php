<?php
session_cache_expire(30);
session_start();
require_once('database/dbMessages.php');

$loggedIn = false;
$userID = null;

if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $userID = $_SESSION['_id'];
} else {
    header('Location: inbox.php');
    exit;
}

// DELETE ALL
if (isset($_POST['delete_all'])) {
    delete_all_messages_for_user($userID);
    header('Location: inbox.php');
    exit;
}

// DELETE SELECTED
if (isset($_POST['bulk_delete']) && isset($_POST['selected_messages']) && is_array($_POST['selected_messages'])) {
    $ids = array_map('intval', $_POST['selected_messages']);
    delete_messages_by_ids($ids, $userID);
    header('Location: inbox.php');
    exit;
}

// SINGLE DELETE (via GET)
if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $id = intval($_GET['id']);
    $message = get_message_by_id($id);

    if ($message && $message['recipientID'] == $userID) {
        delete_message($id);
    }
}

header('Location: inbox.php');
exit;
