<?php /* Implemented by Aidan Meyer */

include_once('dbinfo.php');
include_once('dbDiscussionReplies.php');
include_once(dirname(__FILE__).'/../domain/Discussion.php');

function add_discussion($discussion) {
    if (!$discussion instanceof Discussion)
        die("Error: add_discussion type mismatch");

    $con = connect();
    $query = "SELECT * FROM dbdiscussions WHERE author_id = '" . $discussion->get_author_id() . 
             "' AND title = '" . $discussion->get_title() . "'";
    $result = mysqli_query($con, $query);

    if ($result == null || mysqli_num_rows($result) == 0) {
        $query = 'INSERT INTO dbdiscussions (author_id, title, body, time) VALUES ("' .
            $discussion->get_author_id() . '", "' .
            $discussion->get_title() . '", "' .
            $discussion->get_body() . '", "' .
            $discussion->get_time() . '")';

        mysqli_query($con, $query);
        mysqli_close($con);
        return true;
    }

    mysqli_close($con);
    return false;
}

function remove_discussion($author_id, $title) {
    $con = connect();
    $query = "DELETE FROM dbdiscussions WHERE author_id = '" . $author_id . "' AND title = '" . $title . "'";
    $result = mysqli_query($con, $query);
    mysqli_close($con);
    return $result;
}

function get_discussion($title) {
    $con = connect();
    $query = "SELECT * FROM dbdiscussions WHERE title = '" . $title . "'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $discussion = mysqli_fetch_assoc($result);
        mysqli_close($con);
        return $discussion;
    }

    mysqli_close($con);
    return null;
}

function get_all_discussions() {
    $con = connect();
    $query = "SELECT * FROM dbdiscussions";
    $result = mysqli_query($con, $query);
    $discussions = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $discussions[] = $row;
    }

    mysqli_close($con);
    return $discussions;
}
function get_user_from_author($author_id){
    $con=connect();
    $query = "SELECT * FROM dbpersons WHERE id = '" . $author_id . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) !== 1) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    // var_dump($result_row);
    $thePerson = make_a_person($result_row);
//    mysqli_close($con);
    return $thePerson;
}
function discussion_exists($title) {
    $existingDiscussion = get_discussion($title);
    return !empty($existingDiscussion); // If a discussion is found, return true.
}

function deleteAllDiscussions() {
    $con = connect();
    $query = "TRUNCATE TABLE dbdiscussions";
    $query1 = "TRUNCATE TABLE discussion_replies";

    $result1 = mysqli_query($con, $query);
    $result2 = mysqli_query($con, $query1);

    mysqli_close($con);
    
    return $result1 && $result2;
}

function deleteDiscussions($discussions) {
    $con = connect();
    $success = true;

    foreach ($discussions as $entry) {
        $data = explode('|', $entry); // expects "author_id|title"
        if (count($data) == 2) {
            $author_id = mysqli_real_escape_string($con, $data[0]);
            $title = mysqli_real_escape_string($con, $data[1]);

            delete_all_replies_in($title); //delete replies in the discussion
            
            $query = "DELETE FROM dbdiscussions WHERE author_id = '$author_id' AND title = '$title'";
            $result = mysqli_query($con, $query);
            if (!$result) $success = false;
        }
    }

    mysqli_close($con);
    return $success;
}

?>