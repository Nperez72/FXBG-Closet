<?php
ob_start();

session_start();
include_once "database/dbDiscussionReplies.php";
include_once "database/dbDiscussions.php";

// Ensure the user is authorized
if (!isset($_SESSION['_id']) || $_SESSION['access_level'] < 3) {
    die("Unauthorized access.");
}

// Check if the reply_id and title are passed in the URL
$replyID = $_GET['reply_id'] ?? null; // Fetch reply_id from the URL query string
$discussionTitle = $_GET['title'] ?? null; // Fetch title from the URL query string

if ($replyID && $discussionTitle) {
    // Call the function to remove the reply
    remove_reply($replyID);

    // Now fetch the discussion associated with the title to get the authorID
    $discussion = get_discussion($discussionTitle); // Fetch discussion based on the title
    
    if ($discussion) {
        $authorID = $discussion['author_id'];  // Get the author ID from the discussion
        $title = $discussion['title'];         // Get the title from the discussion
        
        // Redirect back to the discussion content page
        header("Location: discussionContent.php?author=" . urlencode($authorID) . "&title=" . urlencode($title));
        exit;
    } else {
        die("Error: Discussion not found.");
    }
} else {
    die("Invalid request. Missing reply ID or title.");
}
ob_end_flush();
?>
