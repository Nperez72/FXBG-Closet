<?php
/*
 * Copyright 2013 by Jerrick Hoang, Ivy Xing, Sam Roberts, James Cook, 
 * Johnny Coster, Judy Yang, Jackson Moniaga, Oliver Radwan, 
 * Maxwell Palmer, Nolan McNair, Taylor Talmage, and Allen Tucker. 
 * This program is part of RMH Homebase, which is free software.  It comes with 
 * absolutely no warranty. You can redistribute and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 * 
 */

/**
 * @version March 1, 2012
 * @author Oliver Radwan and Allen Tucker
 */

/* 
 * Created for Gwyneth's Gift in 2022 using original Homebase code as a guide
 */


include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/Training.php');

/*
 * add an training to dbTrainings table: if already there, return false
 */

function add_training($training) {
    if (!$training instanceof Training)
        die("Error: add_training type mismatch");
    $con=connect();
    $query = "SELECT * FROM dbtrainings WHERE id = '" . $training->getID() . "'";
    $result = mysqli_query($con,$query);
    //if there's no entry for this id, add it
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_query($con,'INSERT INTO dbtrainings VALUES("' .
                $training->getID() . '","' .
                $training->getDate() . '","' .
                $training->getStartTime() . "," .
                #$training->get_venue() . '","' .
                $training->getName() . '","' . 
                $training->getDescription() . '","' .
                $training->getCapacity() . "," .
                $training->getCompleted() . "," .
                $training->getTrainingType() . "," .
                $training->getRestrictedSignup() . "," .
                #$training->getID() .            
                '");');							
        mysqli_close($con);
        return true;
    }
    mysqli_close($con);
    return false;
}

/*function fetch_training_name_by_id($id) {
    $connection = connect();
    $id = mysqli_real_escape_string($connection, $id);
    $query = "select name from dbtrainings where id = '$id'";
    $result = mysqli_query($connection, $query);
    $training = mysqli_fetch_assoc($result);
    if ($training) {
        require_once('include/output.php');
        $training = hsc($training);
        mysqli_close($connection);
        return $training;
    }
    mysqli_close($connection);
    return null;
}*/

function request_training_signup($trainingID, $account_name, $role, $notes) {
    $connection = connect();
    $query1 = "SELECT id FROM dbtrainings WHERE name LIKE '$trainingID'";
    $result1 = mysqli_query($connection, $query1);
    $row = mysqli_fetch_assoc($result1);
    $value = $row['id'];
   
    $query2 = "SELECT userID FROM dbtrainingpersons WHERE trainingID LIKE '$value' AND userID LIKE '$account_name'";
    $result2 = mysqli_query($connection, $query2);

    $query3 = "SELECT username FROM dbpendingsignups WHERE trainingname LIKE '$value' AND username LIKE '$account_name'";
    $result3 = mysqli_query($connection, $query3);

    $row2 = null;
    $row2 = mysqli_fetch_assoc($result2);
    $row3 = null;
    $row3 = mysqli_fetch_assoc($result3);

    if(!is_null($row2) || !is_null($row3)) {
            $value2 = $row2['userID'];
            $value3 = $row3['username'];
            if($value2 == $account_name || $value3 == $account_name){
                return null;
        } 
    } else {       
            $query = "insert into dbpendingsignups (username, trainingname, role, notes) values ('$account_name', '$value', '$role', '$notes')";
            $result = mysqli_query($connection, $query);
            mysqli_commit($connection);
            return $value;
        }
    return $value;
}
function sign_up_for_training($trainingID, $account_name, $role, $notes) {
    $connection = connect();
    $query1 = "SELECT id FROM dbtrainings WHERE name LIKE '$trainingID'";
    $result1 = mysqli_query($connection, $query1);
    $row = mysqli_fetch_assoc($result1);
    $value = $row['id'];
   
    $query2 = "SELECT userID FROM dbtrainingpersons WHERE trainingID LIKE '$value' AND userID LIKE '$account_name'";
    $result2 = mysqli_query($connection, $query2);

    $row2 = null;
    $row2 = mysqli_fetch_assoc($result2);

    if(!is_null($row2)) {
            $value2 = $row2['userID'];
            if($value2 == $account_name){
                return null;
        } 
    } else {       
            $query = "insert into dbtrainingpersons (trainingID, userID, position, notes) values ('$value', '$account_name', '$role', '$notes')";
            $result = mysqli_query($connection, $query);
            mysqli_commit($connection);
            return $value;
        }
    return $value;
}

/* @@@ Thomas's work! */
/*
 * Check if a user is is signed up for an training. Return true or false.
 */
function check_if_signed_up($trainingID, $userID) {
    // look up training+user pair
    $connection = connect();
    $query1 = "SELECT * FROM dbtrainingpersons WHERE trainingID = '$trainingID' and userID = '$userID'";
    $result1 = mysqli_query($connection, $query1);
    $row = mysqli_fetch_assoc($result1);
    mysqli_close($connection);

    // check if a row was returned
    if ($row) {
        return True;
    } else {
        return False;
    }
}

/* @@@ Madison's work! */
/*
 * Check for all users signed up for an training. 
 */
function fetch_training_signups($trainingID) {
    $connection = connect();
    $query = "SELECT userID, position, notes FROM dbtrainingpersons WHERE trainingID = '$trainingID'";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die('Query failed: ' . mysqli_error($connection));
    }

    $signups = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $signups[] = $row;
    }

    mysqli_close($connection);
    return $signups;
}

function fetch_pending($trainingID) {
    $connection = connect();
    $query = "SELECT username, role, notes FROM dbpendingsignups WHERE trainingname = '$trainingID'";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die('Query failed: ' . mysqli_error($connection));
    }
    $signups = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $signups[] = $row;
    }

    mysqli_close($connection);
    return $signups;
}

function fetch_all_pending() {
    $connection = connect();
    $query = "SELECT trainingname, username, role, notes FROM dbpendingsignups";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die('Query failed: ' . mysqli_error($connection));
    }
    $signups = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $signups[] = $row;
    }

    mysqli_close($connection);
    return $signups;
}

function all_pending_names() {
    $connection = connect();
    $query = "SELECT trainingname FROM dbpendingsignups";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die('Query failed: ' . mysqli_error($connection));
    }

    $signups = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $signups[] = $row;
    }

    $training_names = [];
    $length = sizeof($signups);
    for ($x = 0; $x < $length; $x++) {
        $val = (int)$signups[$x]['trainingname'];
        $query2 = "SELECT name FROM dbtrainings WHERE id = $val";
        $result2 = mysqli_query($connection, $query2);
        while ($row = mysqli_fetch_assoc($result2)) {
            $training_names[] = $row;
        }
    }

    mysqli_close($connection);
    return $training_names;
}

function all_pending_ids() {
    $connection = connect();
    $query = "SELECT trainingname FROM dbpendingsignups";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die('Query failed: ' . mysqli_error($connection));
    }

    $signups = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $signups[] = $row;
    }

    mysqli_close($connection);
    return $signups;
}

function remove_user_from_training($training_id, $user_id) {    
    $query = "DELETE FROM dbtrainingpersons WHERE trainingID LIKE '$training_id' AND userID LIKE '$user_id'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $result = boolval($result);
    mysqli_close($connection);
    return $result;
}

function remove_user_from_pending_training($training_id, $user_id) {    
    $query = "DELETE FROM dbpendingsignups WHERE trainingname = '$training_id' AND username = '$user_id'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $result = boolval($result);
    mysqli_close($connection);
    return $result;
}

/* @@@ Thomas's work! */
/*
 * Returns true if the given training is archived.
 */
function is_archived($id) {
    // look-up 'completed' in the training's DB entry
    $connection = connect();
    $query1 = "SELECT completed FROM dbtrainings WHERE id = '$id'";
    $result1 = mysqli_query($connection, $query1);
    $row = mysqli_fetch_assoc($result1);
    mysqli_close($connection);

    if ($row == NULL) return False; // no match for that training ID

    if ($row['completed'] == 'yes') {
        // training is archived
        return True;
    } else {
        return False;
    }
}

/*
 * Mark an training as archived in the DB by setting the 'completed' column to 'yes'.
 */
function archive_training($id) {
    $con=connect();
    $query = "UPDATE dbtrainings SET completed = 'yes' WHERE id = '" .$id. "'";
    $result = mysqli_query($con, $query);
    mysqli_close($con);
    return $result;
}

/*
 * Mark an training as not archived in the DB by setting the 'completed' column to 'no'.
 */
function unarchive_training($id) {
    $con=connect();
    $query = "UPDATE dbtrainings SET completed = 'no' WHERE id = '" .$id. "'";
    $result = mysqli_query($con,$query);
    mysqli_close($con);
    return $result;
}

/* end of Thomas's work*/

/**/

/*
 * remove an training from dbTrainings table.  If already there, return false
 */

function remove_training($id) {
    $con=connect();
    $query = 'SELECT * FROM dbtrainings WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $query = 'DELETE FROM dbtrainings WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    mysqli_close($con);
    return true;
}


/*
 * @return an Training from dbTrainings table matching a particular id.
 * if not in table, return false
 */

function retrieve_training($id) {
    $con=connect();
    $query = "SELECT * FROM dbtrainings WHERE id = '" . $id . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) !== 1) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    // var_dump($result_row);
    $theTraining = make_an_training($result_row);
//    mysqli_close($con);
    return $theTraining;
}

function retrieve_training2($id) {
    $con=connect();
    $query = "SELECT * FROM dbtrainings WHERE id = '" . $id . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) !== 1) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
//    var_dump($result_row);
    return $result_row;
}

// not in use, may be useful for future iterations in changing how trainings are edited (i.e. change the remove and create new training process)
function update_training_date($id, $new_training_date) {
	$con=connect();
	$query = 'UPDATE dbtrainings SET training_date = "' . $new_training_date . '" WHERE id = "' . $id . '"';
	$result = mysqli_query($con,$query);
	mysqli_close($con);
	return $result;
}

function make_an_training($result_row) {
	/*
	 ($en, $v, $sd, $description, $ev))
	 */
    $theTraining = new Training(
                    $result_row['id'],
                    $result_row['name'],                   
                    date: $result_row['date'],
                    startTime: $result_row['startTime'],
                    endTime: $result_row['endTime'],
                    description: $result_row['description'],
                    capacity: $result_row['capacity'],
                    completed: $result_row['completed'],
                    event_type: $result_row['training_type'],
                    restricted_signup: $result_row['restricted_signup']
                ); 
    return $theTraining;
}

function get_all_trainings() {
    $con=connect();
    $query = "SELECT * FROM dbtrainings" . 
            " ORDER BY completed";
    $result = mysqli_query($con,$query);
    $theTrainings = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $theTraining = make_an_training($result_row);
        $theTrainings[] = $theTraining;
    }
    mysqli_close($con);
    return $theTrainings;
 }
 
 function get_all_trainings_sorted_by_date_not_archived() {
    $con=connect();
    $query = "SELECT * FROM dbtrainings" .
            " WHERE completed = 'no'" .
            " ORDER BY date ASC";
    $result = mysqli_query($con,$query);
    $theTrainings = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $theTraining = make_an_training($result_row);
        $theTrainings[] = $theTraining;
    }
    mysqli_close($con);
    return $theTrainings;
 }

 function get_all_trainings_sorted_by_date_and_archived() {
    $con=connect();
    $query = "SELECT * FROM dbtrainings" .
            " WHERE completed = 'yes'" .
            " ORDER BY date ASC";
    $result = mysqli_query($con,$query);
    $theTrainings = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $theTraining = make_an_training($result_row);
        $theTrainings[] = $theTraining;
    }
    mysqli_close($con);
    return $theTrainings;
 }

// retrieve only those trainings that match the criteria given in the arguments
function getonlythose_dbTrainings($name, $day, $venue) {
   $con=connect();
   $query = "SELECT * FROM dbtrainings WHERE training_name LIKE '%" . $name . "%'" .
           " AND training_name LIKE '%" . $name . "%'" .
           " AND venue = '" . $venue . "'" . 
           " ORDER BY training_name";
   $result = mysqli_query($con,$query);
   $theTrainings = array();
   while ($result_row = mysqli_fetch_assoc($result)) {
       $theTraining = make_an_training($result_row);
       $theTrainings[] = $theTraining;
   }
   mysqli_close($con);
   return $theTrainings;
}

function fetch_trainings_in_date_range($start_date, $end_date) {
    $connection = connect();
    $start_date = mysqli_real_escape_string($connection, $start_date);
    $end_date = mysqli_real_escape_string($connection, $end_date);
    $query = "select * from dbtrainings
              where date >= '$start_date' and date <= '$end_date'
              order by startTime asc";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }
    require_once('include/output.php');
    $trainings = array();
    while ($result_row = mysqli_fetch_assoc($result)) {
        $key = $result_row['date'];
        if (isset($trainings[$key])) {
            $trainings[$key] []= hsc($result_row);
        } else {
            $trainings[$key] = array(hsc($result_row));
        }
    }
    mysqli_close($connection);
    return $trainings;
}

function fetch_trainings_on_date($date) {
    $connection = connect();
    $date = mysqli_real_escape_string($connection, $date);
    $query = "select * from dbtrainings
              where date = '$date' order by startTime asc";
    $results = mysqli_query($connection, $query);
    if (!$results) {
        mysqli_close($connection);
        return null;
    }
    require_once('include/output.php');
    $trainings = [];
    foreach ($results as $row) {
        $trainings []= hsc($row);
    }
    mysqli_close($connection);
    return $trainings;
}

function fetch_training_by_id($id) {
    $connection = connect();
    $id = mysqli_real_escape_string($connection, $id);
    $query = "select * from dbtrainings where id = '$id'";
    $result = mysqli_query($connection, $query);
    $training = mysqli_fetch_assoc($result);
    if ($training) {
        require_once('include/output.php');
        $training = hsc($training);
        mysqli_close($connection);
        return $training;
    }
    mysqli_close($connection);
    return null;
}

function create_training($training) {
    $connection = connect();
    $name = $training["name"];
    //$abbrevName = $training["abbrev-name"];
    $date = $training["date"];
    $startTime = $training["start-time"];    
    $endTime = $training["end-time"];
    $description = $training["description"];
    if (isset($training["capacity"])) {
        $capacity = $training["capacity"];
    } else {
        $capacity = 999;
    }
    if (isset($training["location"])) {
        $location = $training["location"];
    } else {
        $location = "";
    }
    //$completed = $training["completed"];
    //$training_type = $training["training_type"];
    $restricted_signup = $training["role"];
    if ($restricted_signup == "r") {
        $restricted = 1;
    } else {
        $restricted = 0;
    }
    $description = $training["description"];
    //$location = $training["location"];
    //$services = $training["service"];

    //$animal = $training["animal"];
    $completed = "no";
    $query = "
        insert into dbtrainings (name, date, startTime, endTime, restricted_signup, description, capacity, completed, location)
        values ('$name', '$date', '$startTime', '$endTime', $restricted, '$description', $capacity, '$completed', '$location')
    ";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return null;
    }
    $id = mysqli_insert_id($connection);
    //add_services_to_training($id, $services);
    mysqli_commit($connection);
    mysqli_close($connection);
    return $id;
}

function add_services_to_training($trainingID, $serviceIDs) {
    $connection = connect();
    foreach($serviceIDs as $serviceID) {
        $query = "insert into dbtrainingsservices (trainingID, serviceID) values ('$trainingID', '$serviceID')";
        $result = mysqli_query($connection, $query);
        if (!$result) {
            return null;
        }
        $id = mysqli_insert_id($connection);
    }
    mysqli_commit($connection);
    return $id;
}

function update_training($trainingID, $trainingDetails) {
    $connection = connect();
    $id = $trainingDetails["id"];
    $name = $trainingDetails["name"];
    #$abbrevName = $trainingDetails["abbrev-name"];
    $date = $trainingDetails["date"];
    $startTime = $trainingDetails["start-time"];
    #$restricted = $trainingDetails["restricted"];
    $endTime = $trainingDetails["end-time"];
    $description = $trainingDetails["description"];
    $capacity = $trainingDetails["capacity"];
    #$completed = $trainingDetails["completed"];
    #$training_type = $trainingDetails["training_type"];
    #$restricted_signup = $trainingDetails["restricted_signup"];
    $location = $trainingDetails["location"];
    //$services = $trainingDetails["service"];
    
    #$completed = $trainingDetails["completed"];
    #$query = "
       # update dbTrainings set name='$name', abbrevName='$abbrevName', date='$date', startTime='$startTime', restricted='$restricted', description='$description', locationID='$location', completed='$completed'
       # where id='$trainingID'
    #";
   # $query = "
    #    update dbtrainings set id='$id', name='$name', date='$date', startTime='$startTime', endTime='$endTime', description='$description', capacity='$capacity', completed='$completed', training_type='$training_type', restricted_signup='$restricted_signup'
    #    where id='$trainingID'
    #";
    $query = "
        update dbtrainings set id='$id', name='$name', date='$date', startTime='$startTime', endTime='$endTime', description='$description', location='$location', capacity=$capacity
        where id='$trainingID'
    ";
    $result = mysqli_query($connection, $query);
    // update_services_for_training($trainingID, $services);
    mysqli_commit($connection);
    mysqli_close($connection);
    return $result;
}

function update_training2($trainingID, $trainingDetails) {
    $connection = connect();
    $id = $trainingDetails["id"];
    $name = $trainingDetails["name"];
    #$abbrevName = $trainingDetails["abbrevName"];
    $date = $trainingDetails["date"];
    $startTime = $trainingDetails["startTime"];
    $endTime = $trainingDetails["endTime"];
    $description = $trainingDetails["description"];
    $capacity = $trainingDetails["capacity"];
    $completed = $trainingDetails["completed"];
    $training_type = $trainingDetails["training_type"];
    $restricted_signup = $trainingDetails["restricted_signup"];
    #$query = "
    #    update dbTrainings set name='$name', abbrevName='$abbrevName', date='$date', startTime='$startTime', endTime='$endTime', description='$description', locationID='$location', capacity='$capacity', animalId='$animalID', completed='$completed'
    #    where id='$trainingID'
    #";
    $query = "
        update dbtrainings set id='$id', name='$name', date='$date', startTime='$startTime', endTime='$endTime', description='$description', capacity='$capacity', completed='$completed', training_type='$training_type', restricted_signup='$restricted_signup'
        where id='$trainingID'
    ";
    $result = mysqli_query($connection, $query);
    //update_services_for_training($trainingID, $services);
    mysqli_commit($connection);
    mysqli_close($connection);
    return $result;
}

function update_services_for_training($trainingID, $serviceIDs) {
    $connection = connect();

    $current_services = get_services($trainingID);
    foreach($current_services as $curr_serv) {
        $curr_servIDs[] = $curr_serv['id'];
    }

    // add new services
    foreach($serviceIDs as $serviceID) {
        if (!in_array($serviceID, $curr_servIDs)) {
            $query = "insert into dbtrainingsservices (trainingID, serviceID) values ('$trainingID', '$serviceID')";
            $result = mysqli_query($connection, $query);
        }
    }
    // remove old services
    foreach($curr_servIDs as $curr_serv) {
        if (!in_array($curr_serv, $serviceIDs)) {
            $query = "delete from dbtrainingsservices where serviceID='$curr_serv'";
            $result = mysqli_query($connection, $query);
        }
    }
    mysqli_commit($connection);
    return;
}

function find_training($nameLike) {
    $connection = connect();
    $query = "
        select * from dbtrainings
        where name like '%$nameLike%'
    ";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return null;
    }
    $all = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $all;
}

function fetch_trainings_in_date_range_as_array($start_date, $end_date) {
    $connection = connect();
    $start_date = mysqli_real_escape_string($connection, $start_date);
    $end_date = mysqli_real_escape_string($connection, $end_date);
    $query = "select * from dbtrainings
              where date >= '$start_date' and date <= '$end_date'
              order by date, startTime asc";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }
    $trainings = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $trainings;
}

function fetch_all_trainings() {
    $connection = connect();
    $query = "select * from dbtrainings
              order by date, startTime asc";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }
    $trainings = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $trainings;
}

function get_animal($id) {
    $connection = connect();
    $query = "select * from dbanimals
              where id='$id'";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return [];
    }
    $animal = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $animal;
}

function get_description($id) {
    $connection = connect();
    $query = "select description from dbtrainings
              where id='$id'";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return [];
    }
    $description = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $description;
}
  

function get_location($id) {
    $connection = connect();
    $query = "select * from dblocations
              where id='$id'";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return [];
    }
    $location = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $location;
}

function get_services($trainingID) {
    $connection = connect();
    $query = "select * from dbservices AS serv JOIN dbtrainingsservices AS es ON es.serviceID = serv.id
              where es.trainingID='$trainingID'";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return [];
    }
    $services = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $services;
}

function get_media($id, $type) {
    $connection = connect();
    $query = "select * from dbtrainingmedia
              where trainingID='$id' and type='$type'";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return [];
    }
    $media = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($connection);
    return $media;
}

function get_training_training_media($id) {
    return get_media($id, 'training');
}

function get_post_training_media($id) {
    return get_media($id, 'post');
}

function attach_media($trainingID, $type, $url, $format, $description) {
    $query = "insert into dbtrainingmedia
              (trainingID, type, url, format, description)
              values ('$trainingID', '$type', '$url', '$format', '$description')";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    mysqli_close($connection);
    if (!$result) {
        return false;
    }
    return true;
}

function attach_training_training_media($trainingID, $url, $format, $description) {
    return attach_media($trainingID, 'training', $url, $format, $description);
}

function attach_post_training_media($trainingID, $url, $format, $description) {
    return attach_media($trainingID, 'post', $url, $format, $description);
}

function detach_media($mediaID) {
    $query = "delete from dbtrainingmedia where id='$mediaID'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    mysqli_close($connection);
    if ($result) {
        return true;
    }
    return false;
}

function delete_training($id) {
    $query = "delete from dbtrainings where id='$id'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $result = boolval($result);
    mysqli_close($connection);
    return $result;
}

function cancel_training($training_id, $account_name) {
    $query = "DELETE from dbtrainingpersons where userID LIKE '$account_name' AND trainingID LIKE $training_id";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $result = boolval($result);
    mysqli_close($connection);
    return $result;
}

function approve_signup($training_id, $account_name, $position, $notes) {
    $query = "DELETE from dbpendingsignups where username = '$account_name' AND trainingname = $training_id";
    $connection = connect();
    //echo "username " . $account_name . " trainingname " . $training_id;
    $result = mysqli_query($connection, $query);
    $result = boolval($result);

    //echo "hello" . $account_name;

    $query2 = "insert into dbtrainingpersons (trainingID, userID, position, notes) values ('$training_id', '$account_name',  '$position', '$notes')";
    $result2 = mysqli_query($connection, $query2);
    //$result2 = boolval($result2);
    //mysqli_close($connection);
    mysqli_commit($connection);
    return $result2;
}

function reject_signup($training_id, $account_name, $position, $notes) {
    $query = "DELETE from dbpendingsignups where username = '$account_name' AND trainingname = '$training_id'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    $result = boolval($result);
    
    return $result;
}

function complete_training($id) {
    $training = retrieve_training2($id);
    $animal = get_animal($training["animalID"])[0];
    $date = $training["date"];
    $training["completed"] = "yes";

    $services = get_services($training["id"]);
    $length = count($services);

    for ($i = 0; $i < $length; $i++) { 
        $check = $services[$i]['name'];
        $dur = $services[$i]['duration_years'];
        if(stripos($check, "spay") !== false || stripos($check, "neuter") !== false){
            $animal["spay_neuter_done"] = "yes";
            $animal["spay_neuter_date"] = $date;
        }
        else if(stripos($check, "rabie") !== false){
            $animal["rabies_given_date"] = $date;
            $animal["rabies_due_date"] = date('Y-m-d', strtotime($date."+".$dur." years"));
        }
        else if(stripos($check, "heartworm") !== false){
            $animal["heartworm_given_date"] = $date;
            $animal["heartworm_due_date"] = date('Y-m-d', strtotime($date."+".$dur." years"));
        }
        else if(stripos($check, "distemper 1") !== false){
            $animal["distemper1_given_date"] = $date;
            $animal["distemper1_due_date"] = date('Y-m-d', strtotime($date."+".$dur." years"));
        }
        else if(stripos($check, "distemper 2") !== false){
            $animal["distemper2_given_date"] = $date;
            $animal["distemper2_due_date"] = date('Y-m-d', strtotime($date."+".$dur." years"));
        }
        else if(stripos($check, "distemper 3") !== false){
            $animal["distemper3_given_date"] = $date;
            $animal["distemper3_due_date"] = date('Y-m-d', strtotime($date."+".$dur." years"));
        }
        else if(stripos($check, "microchip") !== false){
            $animal["microchip_done"] = "yes";
        }
        else{
            $animal["notes"] = $animal["notes"]." | ".$check.": ".$date;
        }
    
    }
//    var_dump($training);
    $result = update_animal2($animal);
    $result = update_training2($training["id"], $training);
    return $result;
}

function update_animal2($animal) {
    $connection = connect();
    $id = $animal['id'];
	$odhsid = $animal["odhs_id"];
    $name = $animal["name"];
	$breed = $animal["breed"];
    $age = $animal["age"];
    $gender = $animal["gender"];
    $notes = $animal["notes"];
    $spay_neuter_done = $animal["spay_neuter_done"];
	$spay_neuter_date = $animal["spay_neuter_date"];
    if (empty($animal["spay_neuter_date"])) {
        $spay_neuter_date = '0000-00-00';
    }
    $rabies_given_date = $animal["rabies_given_date"];
    if (empty($animal["rabies_given_date"])) {
        $rabies_given_date = '0000-00-00';
    }
	$rabies_due_date = $animal["rabies_due_date"];
    if (empty($animal["rabies_due_date"])) {
        $rabies_due_date = '0000-00-00';
    }
    $heartworm_given_date = $animal["heartworm_given_date"];
    if (empty($animal["heartworm_given_date"])) {
        $heartworm_given_date = '0000-00-00';
    }
	$heartworm_due_date = $animal["heartworm_due_date"];
    if (empty($animal["heartworm_due_date"])) {
        $heartworm_due_date = '0000-00-00';
    }
	$distemper1_given_date = $animal["distemper1_given_date"];
    if (empty($animal["distemper1_given_date"])) {
        $distemper1_given_date = '0000-00-00';
    }
	$distemper1_due_date = $animal["distemper1_due_date"];
    if (empty($animal["distemper1_due_date"])) {
        $distemper1_due_date = '0000-00-00';
    }
	$distemper2_given_date = $animal["distemper2_given_date"];
    if (empty($animal["distemper2_given_date"])) {
        $distemper2_given_date = '0000-00-00';
    }
	$distemper2_due_date = $animal["distemper2_due_date"];
    if (empty($animal["distemper2_due_date"])) {
        $distemper2_due_date = '0000-00-00';
    }
	$distemper3_given_date = $animal["distemper3_given_date"];
    if (empty($animal["distemper3_given_date"])) {
        $distemper3_given_date = '0000-00-00';
    }
	$distemper3_due_date = $animal["distemper3_due_date"];
    if (empty($animal["distemper3_due_date"])) {
        $distemper3_due_date = '0000-00-00';
    }
	$microchip_done = $animal["microchip_done"];
    $query = "
        UPDATE dbanimals set odhs_id='$odhsid', name='$name', breed='$breed', age='$age', gender='$gender', notes='$notes', spay_neuter_done='$spay_neuter_done', spay_neuter_date='$spay_neuter_date', rabies_given_date='$rabies_given_date', rabies_due_date='$rabies_due_date', heartworm_given_date='$heartworm_given_date', heartworm_due_date='$heartworm_due_date', distemper1_given_date='$distemper1_given_date', distemper1_due_date='$distemper1_due_date', distemper2_given_date='$distemper2_given_date', distemper2_due_date='$distemper2_due_date', distemper3_given_date='$distemper3_given_date', distemper3_due_date='$distemper3_due_date', microchip_done='$microchip_done'
        where id='$id'
        ";
    $result = mysqli_query($connection, $query);
    if (!$result) {
        return null;
    }
    mysqli_commit($connection);
    mysqli_close($connection);
    return $id;
}

//There was a question mark followed by a > here
