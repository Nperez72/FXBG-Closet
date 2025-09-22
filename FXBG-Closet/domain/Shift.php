<?php
/**
 * Encapsulated version of a dbShifts entry.
 */
class Shift{
    private $shift_id;  // primary key
    private $person_id; // foreign key to user
    private $date;
    private $startTime;
    private $endTime;
    private $description;
    private $totalHours;

    function __construct($shift_id, $person_id, $date, $startTime, $endTime, $description, $totalHours) {
        $this->shift_id = $shift_id;
        $this->person_id = $person_id;
        $this->date = $date;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->description = $description;
        $this->totalHours = $totalHours;
    }

    function getShiftID() {
        return $this->shift_id;
    }

    function getPersonID() {
        return $this->person_id;
    }

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

    function getTotalHours() {
        return $this->totalHours;
    }
}
