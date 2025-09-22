<?php
session_cache_expire(30);
session_start();

$loggedIn = false;
$accessLevel = 0;
$userID = null;
if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}
// admin-only access
if ($accessLevel < 2) {
    header('Location: index.php');
    die();
}

require_once 'database/dbGroups.php';

if (isset($_GET['group_name'])) {
    $group_name = $_GET['group_name'];

    if (remove_group($group_name)) {
        // Redirect to showGroups.php with a success message
        remove_all_users_in_group($group_name);
        header("Location: showGroups.php?message=" . urlencode("Group '$group_name' deleted successfully."));
        exit();
    } else {
        // Redirect to showGroups.php with an error message
        header("Location: showGroups.php?error=" . urlencode("Failed to delete group '$group_name'."));
        exit();
    }
} else {
    // Redirect to showGroups.php if no group_name is provided
    header("Location: showGroups.php?error=" . urlencode("No group name provided."));
    exit();
}
?>