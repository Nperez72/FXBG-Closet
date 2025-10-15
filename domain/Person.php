<?php
/*
 * Copyright 2013 by Allen Tucker. 
 * This program is part of RMHC-Homebase, which is free software.  It comes with 
 * absolutely no warranty. You can redistribute and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 * 
 */

/*
 * Created on Mar 28, 2008
 * @author Oliver Radwan <oradwan@bowdoin.edu>, Sam Roberts, Allen Tucker
 * @version 3/28/2008, revised 7/1/2015
 */

// ONLY REQUIRED FIELDS HAVE BEEN ADDED SO FAR.
class Person {

	private $access_level; // normal user = 1, admin = 2, superadmin = 3
	
	// REQUIRED FIELDS
   	// REQUIRED FIELDS
	private $id; // (username)
	private $password;
	private $start_date; // (dete of account creation)
	private $first_name;
	private $last_name;
	private $birthday;
	private $street_address;
	private $city;
	private $state;
	private $zip_code;
	private $phone1;
	private $phone1type;
	private $email;
	private $emergency_contact_first_name;
	private $emergency_contact_last_name;
	private $emergency_contact_phone;
	private $emergency_contact_phone_type;
	private $emergency_contact_relation;
	//private $tshirt_size;
	//private $school_affiliation;
	//private $photo_release;
	//private $photo_release_notes;
	private $type; // admin or volunteer or participant...
	private $status;
	private $archived;
        // TRAINING REQS
	//private $training_complete;
	//private $training_date;
	//private $orientation_complete;
	//private $orientation_date;
	//private $background_complete;
	//private $background_date;

	
    private $skills;
    private $interests;
    //private $disability_accomodation_needs;
   // ADDED NEW ONES -YALDA
    private $is_new_volunteer;
    private $is_community_service_volunteer;
	//private $race;
	//private $gender;
	//YALDA AGAIN
      private $total_hours_volunteered;
	  private $training_level;
	/*
	 * This is a temporary mini constructor for testing purposes. It will be expanded later.
	 */
	function __construct(
        $id, $password, $start_date, $first_name, $last_name, $birthday, $street_address, $city,
    	$state, $zip_code, $phone1, $phone1type, $email, $emergency_contact_first_name,
    	$emergency_contact_last_name, $emergency_contact_phone, $emergency_contact_phone_type,
    	$emergency_contact_relation, $type, $status, $archived, 
    	$skills, $interests, $training_level,  
   	 	$is_community_service_volunteer,           // required param FIRST
    	$is_new_volunteer = 1,                     // optional
    	$total_hours_volunteered = 0.00    		     // optional
    ) {
        $this->id = $id;
        $this->password = $password;
        $this->start_date = $start_date;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->birthday = $birthday;
        $this->street_address = $street_address;
        $this->city = $city;
        $this->state = $state;
        $this->zip_code = $zip_code;
        $this->phone1 = $phone1;
        $this->phone1type = $phone1type;
        $this->email = $email;
        $this->emergency_contact_first_name = $emergency_contact_first_name;
        $this->emergency_contact_last_name = $emergency_contact_last_name;
        $this->emergency_contact_phone = $emergency_contact_phone;
        $this->emergency_contact_phone_type = $emergency_contact_phone_type;
        $this->emergency_contact_relation = $emergency_contact_relation;
        //$this->tshirt_size = $tshirt_size;
        //$this->school_affiliation = $school_affiliation;
        //$this->photo_release = $photo_release;
        //$this->photo_release_notes = $photo_release_notes;
        $this->type = $type;
        $this->status = $status;
        $this->archived = $archived;
        //$this->how_you_heard_of_stepva = $how_you_heard_of_stepva;
        //$this->preferred_feedback_method = $preferred_feedback_method;
        $this->skills = $skills;
        $this->interests = $interests;
        //$this->disability_accomodation_needs = $disability_accomodation_needs;
        //$this->training_complete = $training_complete;
        //$this->training_date = $training_date;
        //$this->orientation_complete = $orientation_complete;
        //$this->orientation_date = $orientation_date;
        //$this->background_complete = $background_complete;
        //$this->background_date = $background_date;
        $this->is_community_service_volunteer = $is_community_service_volunteer;
		$this->is_new_volunteer = $is_new_volunteer;
        $this->total_hours_volunteered = $total_hours_volunteered;
		$this->training_level = $training_level;
        // Access level
        $this->access_level = ($id == 'vmsroot') ? 3 : 1;

    }

    function get_is_new_volunteer() {
        return $this->is_new_volunteer;
    }

    function get_is_community_service_volunteer() {
        return $this->is_community_service_volunteer;
    }

    //YALDA DID THIS.
    function get_total_hours_volunteered() {
    	return $this->total_hours_volunteered;
   }



	function get_id() {
		return $this->id;
	}

	function get_password() {
		return $this->password;
	}

	function get_start_date() {
		return $this->start_date;
	}

	function get_first_name() {
		return $this->first_name;
	}

	function get_last_name() {
		return $this->last_name;
	}

	function get_birthday() {
		return $this->birthday;
	}

	function get_street_address() {
		return $this->street_address;
	}

	function get_city() {
		return $this->city;
	}

	function get_state() {
		return $this->state;
	}

	function get_zip_code() {
		return $this->zip_code;
	}

	function get_phone1() {
		return $this->phone1;
	}

	function get_phone1type() {
		return $this->phone1type;
	}

	function get_email() {
		return $this->email;
	}

	function get_emergency_contact_first_name() {
		return $this->emergency_contact_first_name;
	}

	function get_emergency_contact_last_name() {
		return $this->emergency_contact_last_name;
	}

	function get_emergency_contact_phone() {
		return $this->emergency_contact_phone;
	}

	function get_emergency_contact_phone_type() {
		return $this->emergency_contact_phone_type;
	}

	function get_emergency_contact_relation() {
		return $this->emergency_contact_relation;
	}

	//function get_tshirt_size() {
	//	return $this->tshirt_size;
	//}

	//function get_school_affiliation() {
	//	return $this->school_affiliation;
	//}

	//function get_photo_release() {
	//	return $this->photo_release;
	//}

	//function get_photo_release_notes() {
	//	return $this->photo_release_notes;
	//}

	function get_type() {
		return $this->type;
	}

	function get_status() {
		return $this->status;
	}

	function get_archived() {
		return $this->archived;
	}

	function get_access_level() {
		return $this->access_level;
	}

	//function get_how_you_heard_of_stepva() {
	//	return $this->how_you_heard_of_stepva;
	//}

	//function get_preferred_feedback_method() {
	//	return $this->preferred_feedback_method;
	//}

	function get_skills() {
		return $this->skills;
	}

	function get_interests() {
		return $this->interests;
	}

	function get_training_level() {
		return $this->training_level;
	}

	//function get_disability_accomodation_needs() {
	//	return $this->disability_accomodation_needs;
	//}

	//function get_training_complete() {
        //return $this->training_complete;
   // }

    //function get_training_date() {
      //  return $this->training_date;
    //}

	//function get_orientation_complete() {
	//	return $this->orientation_complete;
	//}
	
	//function get_orientation_date() {
	//	return $this->orientation_date;
	//}
	
	//function get_background_complete() {
	//	return $this->background_complete;
	//}
	
	//function get_background_date() {
	//	return $this->background_date;
	//}
	//function get_gender(){
	//	return $this->gender;
	//}
	//function get_race(){
	//	return $this->race;
	//}
}
