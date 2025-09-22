<?php 
    
class Message{
    private $id;
    private $senderID;
    private $recipentID;
    private $title;
    private $body;
    private $time;
    private $wasRead;
    private $prioritylevel;

    //ID
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    // Sender ID
    public function getSenderID() {
        return $this->senderID;
    }

    public function setSenderID($senderID) {
        $this->senderID = $senderID;
    }

    // Recipient ID
    public function getRecipentID() {
        return $this->recipentID;
    }

    public function setRecipentID($recipentID) {
        $this->recipentID = $recipentID;
    }

    // Title
    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    // Body
    public function getBody() {
        return $this->body;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    // Time
    public function getTime() {
        return $this->time;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    // Was Read
    public function getWasRead() {
        return $this->wasRead;
    }

    public function setWasRead($wasRead) {
        $this->wasRead = $wasRead;
    }

    // Priority Level
    public function getPriorityLevel() {
        return $this->prioritylevel;
    }

    public function setPriorityLevel($prioritylevel) {
        $this->prioritylevel = $prioritylevel;
    }
}
