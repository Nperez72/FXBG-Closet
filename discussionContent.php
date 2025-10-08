<?php /* Implemented and Improved by Aidan Meyer */
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

// Include necessary files
include_once "database/dbDiscussions.php";
include_once "domain/Discussion.php";
include_once "database/dbPersons.php";
include_once "domain/DiscussionReply.php";
include_once "database/dbDiscussionReplies.php";
include_once "database/dbMessages.php";

// Check for required GET parameters
if (!isset($_GET['author']) || !isset($_GET['title'])) {
    die("Error: Missing author or title.");
}

$authorID = htmlspecialchars(trim($_GET['author']));
$title = htmlspecialchars(trim($_GET['title']));

// Fetch discussion and author info
$discussion = get_discussion($title);
if (!$discussion) {
    die("Error: Discussion not found.");
}

$author = get_user_from_author($authorID);
$author_name = $author->get_first_name() . ' ' . $author->get_last_name();
$discussionDate = $discussion['time'];

// Fetch all replies
$replies = get_replies_from($discussion);

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$loggedIn) {
        die("Error: You must be logged in to reply.");
    }

    if (isset($_POST['reply_body'])) {
        $reply_body = trim($_POST['reply_body']);
        $parent_reply_id = isset($_POST['parent_reply_id']) ? trim($_POST['parent_reply_id']) : null;

        if (!empty($reply_body)) {
            if ($parent_reply_id) {
                // Replying to a reply
                add_reply_to_reply($discussion, $userID, $reply_body, $userID, $parent_reply_id);
            } else {
                // Replying to discussion
                add_reply_to_discussion($discussion, $userID, $reply_body);
            }

            $systemMessageTitle = $userID . " has replied to " . $title . ". View under discussions page.";
            $body = "A user has replied to a discussion.";
            system_message_all_admins($systemMessageTitle, $body);

            header("Location: discussionContent.php?author=" . urlencode($authorID) . "&title=" . urlencode($title));
            exit;
        }
    }
}

// Organize replies by parent
$repliesByParent = [];
foreach ($replies as $reply) {
    $parent = $reply['parent_reply_id'] ?? 'root';
    if (!isset($repliesByParent[$parent])) {
        $repliesByParent[$parent] = [];
    }
    $repliesByParent[$parent][] = $reply;
}

// Recursive function to display replies
function displayReplies($parentId, $repliesByParent, $level = 0, $accessLevel = 0, $discussionTitle = '') {
    if (!isset($repliesByParent[$parentId])) return;

    foreach ($repliesByParent[$parentId] as $reply) {
        ?>
        <div class="reply" style="margin-left: <?php echo ($level * 40); ?>px; position: relative; border-left: <?php echo $level > 0 ? '2px solid #ccc' : 'none'; ?>; padding-left: 15px;">
            <?php if ($accessLevel > 2): ?>
                <a href="deleteReply.php?reply_id=<?php echo htmlspecialchars($reply['reply_id']); ?>&title=<?php echo urlencode($discussionTitle); ?>" onclick="return confirm('Are you sure you want to delete this reply?');">
                    <img src="images/trash.svg" alt="Delete" style="width: 20px; height: 20px; cursor: pointer; position: absolute; top: 10px; right: 10px;">
                </a>
            <?php endif; ?>

            <?php if (!empty($reply['parent_reply_id'])): ?>
                <div style="font-size: 12px; color: #777; margin-bottom: 5px;">
                    Responding to <strong><?php echo htmlspecialchars(get_username_by_reply_id($reply['parent_reply_id'])); ?></strong>
                </div>
            <?php endif; ?>

            <div class="reply-author"><?php echo htmlspecialchars($reply['user_reply_id']); ?></div>
            <div class="reply-body"><?php echo nl2br(htmlspecialchars($reply['reply_body'])); ?></div>

            <?php if (isset($_SESSION['_id'])): ?>
                <button class="small-reply-btn" style="width: 10%;" onclick="toggleReplyBox('<?php echo $reply['reply_id']; ?>')">Reply</button>
                <div class="reply-box" id="replyBox-<?php echo $reply['reply_id']; ?>" style="display:none; margin-top:5px;">
                    <form method="post">
                        <textarea name="reply_body" placeholder="Write your reply here..." required></textarea>
                        <input type="hidden" name="parent_reply_id" value="<?php echo $reply['reply_id']; ?>">
                        <button type="submit" class="reply-btn">Submit Reply</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <?php
        displayReplies($reply['reply_id'], $repliesByParent, $level + 1, $accessLevel, $discussionTitle);
    }
}
function get_username_by_reply_id($reply_id) {
    // Fetch reply from database
    $reply = get_reply_by_id($reply_id);
    if ($reply) {
        return $reply['user_reply_id'];
    } else {
        return "Unknown";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require('universal.inc'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .discussion-content {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .author-info, .title, .body {
            margin-bottom: 20px;
        }
        .title {
            font-size: 28px;
            font-weight: bold;
        }
        .body {
            font-size: 20px;
            white-space: pre-wrap;
        }
        .reply-section, .replies {
            margin-top: 30px;
        }
        .reply-btn, .small-reply-btn {
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #008CBA;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }
        .reply-btn:hover, .small-reply-btn:hover {
            background-color: #005f73;
        }
        .reply-box textarea {
            width: 100%;
            height: 80px;
            padding: 8px;
            margin-top: 5px;
        }
        .replies h3 {
            margin-bottom: 15px;
        }
        .reply {
            background-color: #eef;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .reply-author {
            font-weight: bold;
        }
        .reply-body {
            margin-top: 5px;
        }
        .back-btn {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn:hover {
            background-color: #45a049;
        }
    </style>
    <script>
        function toggleReplyBox(replyId) {
            var box = document.getElementById("replyBox-" + replyId);
            if (box.style.display === "none" || box.style.display === "") {
                box.style.display = "block";
            } else {
                box.style.display = "none";
            }
        }
        function toggleMainReplyBox() {
            var box = document.getElementById("mainReplyBox");
            if (box.style.display === "none" || box.style.display === "") {
                box.style.display = "block";
            } else {
                box.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <?php require('header.php'); ?>
    
    <div class="discussion-content">
        <div class="author-info">
            <strong><?php echo $author_name; ?></strong> • <?php echo $discussionDate; ?>
        </div>
        
        <div class="title"><?php echo htmlspecialchars($discussion['title']); ?></div>
        <div class="body"><?php echo nl2br(htmlspecialchars($discussion['body'])); ?></div>

        <div class="reply-section">
            <?php if ($loggedIn): ?>
                <button class="reply-btn" onclick="toggleMainReplyBox()">Reply to Discussion</button>
                <div class="reply-box" id="mainReplyBox" style="display:none;">
                    <form method="post">
                        <textarea name="reply_body" placeholder="Write your reply here..." required></textarea>
                        <button type="submit" class="reply-btn">Submit Reply</button>
                    </form>
                </div>
            <?php else: ?>
                <p><em>Login to post a reply.</em></p>
            <?php endif; ?>
        </div>

        <div class="replies">
            <h3>Replies</h3>
            <?php displayReplies('root', $repliesByParent, 0, $accessLevel, $discussion['title']); ?>
        </div>

        <a href="viewDiscussions.php" class="back-btn">Back to Discussions</a>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>