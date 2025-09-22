<?php /* Implemented by Aidan Meyer */

class DiscussionReply {

    private $reply_id;
    private $user_reply_id; 
    private $author_id; 
    private $discussion_title;
    private $reply_body;
    private $parent_reply_id;
    private $created_at;

    function __construct($reply_id, $user_reply_id, $author_id, $discussion_title, $reply_body, $parent_reply_id, $created_at) {
        $this->reply_id = $reply_id;
        $this->user_reply_id = $user_reply_id;
        $this->author_id = $author_id;
        $this->discussion_title = $discussion_title;
        $this->reply_body = $reply_body;
        $this->parent_reply_id = $parent_reply_id;
        $this->created_at = $created_at;
    }
    function get_user_reply_id(){
        return $this->user_reply_id;
    }
    function get_author_id() {
        return $this->author_id;
    }
    function get_discussion_title() {
        return $this->discussion_title;
    }
    function get_reply_body(){
        return $this->reply_body;
    }
    function get_parent_reply_id(){
        return $this->parent_reply_id;
    }
    function get_time(){
        return $this->created_at;
    }
}
