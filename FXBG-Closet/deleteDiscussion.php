<?php
ob_start();
session_cache_expire(30);
session_start();

$loggedIn = false;
$accessLevel = 0;
$userID = null;

if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}

// Only allow admins (access level > 2)
if ($accessLevel < 3) {
    header('Location: index.php');
    exit;
}

include_once "database/dbDiscussions.php";
include_once "database/dbDiscussionReplies.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author_id = $_POST['author_id'] ?? '';
    $title = $_POST['title'] ?? '';

    if (!empty($author_id) && !empty($title)) {
        delete_all_replies_in($title);
        $result = remove_discussion($author_id, $title);
        if ($result) {
            header('Location: viewDiscussions.php?deleted=1');
        } else {
            header('Location: viewDiscussions.php?error=1');
        }
        exit;
    }
}

header('Location: viewDiscussions.php?error=1');
exit;
ob_end_flush();
?>
