<?php /* Implemented by Aidan Meyer */

class Discussion {

    private $author_id; // author_id + title = Primary Key
    private $title; 
    private $body;
    private $time;

    function __construct($author_id, $title, $body, $time) {
        $this->author_id = $author_id;
        $this->title = $title;
        $this->body = $body;
        $this->time = $time;
    }

    function get_author_id() {
        return $this->author_id;
    }

    function get_title() {
        return $this->title;
    }
    function get_body(){
        return $this->body;
    }
    function get_time(){
        return $this->time;
    }
}
