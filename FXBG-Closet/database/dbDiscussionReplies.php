
<?php /* Implemented by Aidan Meyer */

include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/DiscussionReply.php');
include_once(dirname(__FILE__).'/../domain/Discussion.php');

function get_replies_from($discussion){
    $con = connect();
    $discussion_title = mysqli_real_escape_string($con, $discussion['title']);
    
    $query = "SELECT * FROM discussion_replies WHERE discussion_title = '$discussion_title' ORDER BY created_at ASC";
    $result = mysqli_query($con, $query);
    
    $replies = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $replies[] = $row;
    }

    mysqli_close($con);
    return $replies;
}
function add_reply_to_discussion($discussion, $user_reply_id, $reply_body){
    $con = connect();
    
    $discussion_title = mysqli_real_escape_string($con, $discussion['title']);
    $author_id = mysqli_real_escape_string($con, $discussion['author_id']);
    $reply_body = mysqli_real_escape_string($con, $reply_body);
    $author_id = mysqli_real_escape_string($con, $author_id);
    $created_at = date("Y-m-d-H:i");

    $query = "INSERT INTO discussion_replies (user_reply_id, author_id, discussion_title, reply_body, created_at) 
              VALUES ('$user_reply_id', '$author_id', '$discussion_title', '$reply_body', '$created_at')";

    $result = mysqli_query($con, $query);
    mysqli_close($con);
    
    return $result;
}

function get_reply_by_id($reply_id) {
    $con = connect();
    $reply_id = mysqli_real_escape_string($con, $reply_id);

    $query = "SELECT * FROM discussion_replies WHERE reply_id = '$reply_id' LIMIT 1";
    $result = mysqli_query($con, $query);

    $reply = null;
    if ($result && mysqli_num_rows($result) > 0) {
        $reply = mysqli_fetch_assoc($result);
    }

    mysqli_close($con);
    return $reply;
}

function add_reply_to_reply($discussion, $replyID, $reply_body, $author_id, $parent_reply_id) {
    $con = connect();
    
    $discussion_title = mysqli_real_escape_string($con, $discussion['title']);
    $replyID = mysqli_real_escape_string($con, $replyID);
    $reply_body = mysqli_real_escape_string($con, $reply_body);
    $author_id = mysqli_real_escape_string($con, $author_id);
    $parent_reply_id = mysqli_real_escape_string($con, $parent_reply_id);
    $created_at = date("Y-m-d-H:i");

    $query = "INSERT INTO discussion_replies (user_reply_id, author_id, discussion_title, reply_body, parent_reply_id, created_at) 
              VALUES ('$replyID', '$author_id', '$discussion_title', '$reply_body', '$parent_reply_id', '$created_at')";

    $result = mysqli_query($con, $query);
    mysqli_close($con);
    
    return $result;
}


function remove_reply($replyID) {
    $con = connect();
    $replyID = mysqli_real_escape_string($con, $replyID);

    $query = "DELETE FROM discussion_replies WHERE reply_id = '$replyID'";
    $result = mysqli_query($con, $query);
    
    mysqli_close($con);
    return $result;
}
function delete_all_replies_in($title) {
    $con = connect();
    $title = mysqli_real_escape_string($con, $title);

    $query = "DELETE FROM discussion_replies WHERE discussion_title = '$title'";
    $result = mysqli_query($con, $query);

    mysqli_close($con);
    return $result;
}
?>