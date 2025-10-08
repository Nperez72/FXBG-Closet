<?php /* Implemented by Aidan Meyer */

class Group {

    private $group_name; // Primary key
    private $color_level;

    function __construct($group_name, $color_level) {
        $this->group_name = $group_name;
        $this->color_level = $color_level;
    }

    function get_group_name() {
        return $this->group_name;
    }

    function get_color_level() {
        return $this->color_level;
    }
}
