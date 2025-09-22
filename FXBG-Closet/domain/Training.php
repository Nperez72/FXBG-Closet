<?php
/**
 * Encapsulated version of a dbTrainings entry.
 */
class Training {
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
    private $event_type;
    private $restricted_signup;
    #private $trainingMedia;
    #private $postMedia;
    #private $animalId;

    function __construct($id, $name, $date, $startTime, $endTime, $description, $capacity, $completed, $event_type, $restricted_signup) {
        $this->id = $id;
        $this->name = $name;
        $this->date = $date;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->description = $description;
        $this->capacity = $capacity;
        $this->completed = $completed;
        $this->event_type = $event_type;
        $this->restricted_signup = $restricted_signup;
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

    function getTrainingType() {
        return $this->event_type;
    }

    function getRestrictedSignup() {
        return $this->restricted_signup;
    }


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