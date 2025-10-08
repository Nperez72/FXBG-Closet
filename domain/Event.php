<?php
/**
 * Encapsulated version of a dbs entry.
 */
class Event {
    private $id;
    private $name;
    #private $abbrevName;
    private $date;
    private $startTime;
    private $endTime;
    private $description;
    #private $location;
    private $capacity;
    private $completed;
    private $restricted_signup;
    private $training_level_required;
    private $type;
    #private $trainingMedia;
    #private $postMedia;
    #private $animalId;

    function __construct($id, $name, $date, $startTime, $endTime, $description, $capacity, $completed, $restricted_signup, $training_level_required, $type) {
        $this->id = $id;
        $this->name = $name;
        $this->date = $date;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->description = $description;
        $this->capacity = $capacity;
        $this->completed = $completed;
        $this->restricted_signup = $restricted_signup;
        $this->training_level_required = $training_level_required;
        $this->type = $type;
    }

    function getID() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    #function getAbbreviatedName() {
    #    return $this->abbrevName;
    #}
    // new Event
    function getDate() {
        return $this->date;
    }

    function getStartTime() {
        return $this->startTime;
    }

    function getEndTime() {
        return $this->endTime;
    }

    function getDescription() {
        return $this->description;
    }

    #function getLocation() {
    #    return $this->location;
    #}

    function getCapacity() {
        return $this->capacity;
    }

    function getCompleted() {
        return $this->completed;
    }

    function getRestrictedSignup() {
        return $this->restricted_signup;
    }

    function getTrainingLevelRequired() {
        return $this->training_level_required;
    }
    function getEventType(){
        return $this->type;
    }

    //TODO DELETE
    #function getTrainingMedia() {
    #    return $trainingMedia;
    #}

    #function getPostMedia() {
    #    return $postMedia;
    #}

    #function getAnimalId() {
    #    return $animalId;
    #}
}