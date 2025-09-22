<?php
include_once('dbinfo.php');
include_once('dbMessages.php');
include_once(dirname(__FILE__).'/../domain/Shift.php');

//function get_shift_today($person_id,$today) {
//    $con=connect();
//    $query = "SELECT * FROM dbshifts WHERE person_id = '" . $person_id . "' AND date = '" . $today . "'";
//    $result = mysqli_query($con,$query);
//    return $result;
//}
function get_shift_today($person_id, $today) {
    $con = connect();
    $query = "SELECT * FROM dbshifts WHERE person_id = ? AND date = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ss", $person_id, $today);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $shift = mysqli_fetch_assoc($result); // Fetch the row as an associative array
    //$shift = $result->fetch_object();

    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $shift; // Return the shift data or null if not found
}

// Check if a shift already exists for today
function check_existing_shift($person_id, $date) {
    $con = connect();
    $query = "SELECT * FROM dbshifts WHERE person_id = ? AND date = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ss", $person_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0; // Returns true if a shift exists
}

function get_shift_hours($shift_id) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $con = connect();
    
    $query = "SELECT totalHours FROM dbshifts WHERE shift_id=?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $shift_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc(); // Fetch the actual data
    
    $stmt->close();
    $con->close();
    
    return $row ? $row['totalHours'] : null; // Return the hours or null if not found
}


// Insert a new shift (check-in)
function insert_shift($person_id, $date, $startTime) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $con = connect();
    $query = "INSERT INTO dbshifts (person_id, date, startTime) VALUES (?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sss", $person_id, $date, $startTime);
    $success = $stmt->execute();
    $stmt->close();
    $con->close();
    return $success;
}

// Get a shift that has no endTime (i.e., currently checked in)
function get_open_shift($person_id, $date) {
    $con = connect();
    $query = "SELECT * FROM dbshifts WHERE person_id = ? AND date = ? AND endTime IS NULL";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ss", $person_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $shift = $result->fetch_assoc(); // Fetch a single shift
    $stmt->close();
    $con->close();
    return ($shift) ? $shift['shift_id'] : null;
}

// get the person_id and check-in time from a shift_id
function get_checkin_info_from_shift_id($shift_id){
    $con=connect();
    $query = "SELECT * FROM dbshifts WHERE shift_id = '" . $shift_id . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) !== 1) {
        mysqli_close($con);
        return false;
    }
    $shift = mysqli_fetch_assoc($result);
    $check_in_info = [
        'person_id' => $shift['person_id'],
        'startTime' => $shift['startTime'],
	'shift_id' => $shift['shift_id'],
    ];
    return $check_in_info;
}



function update_shift_end_time($shift_id, $endTime, $desc) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $con = connect();

    $dateTime = date('Y-m-d') . ' ' . $endTime;

    $query = "UPDATE dbshifts 
              SET endTime = ?, 
                  description = ?, 
                  totalHours = ROUND(TIMESTAMPDIFF(SECOND, startTime, ?) / 3600, 2) 
              WHERE shift_id = ?";

    $stmt = $con->prepare($query);
    $stmt->bind_param("sssi", $dateTime, $desc, $dateTime, $shift_id);

    $success = $stmt->execute();

    $stmt->close();
   

    // Step 2: If successful, get totalHours and person_id for the shift
    if ($success) {
        $query2 = "SELECT person_id, totalHours FROM dbshifts WHERE shift_id = ?";
        $stmt2 = $con->prepare($query2);
        $stmt2->bind_param("i", $shift_id);
        $stmt2->execute();
        $stmt2->bind_result($person_id, $workedHours);
        $stmt2->fetch();
        $stmt2->close();
        include_once 'dbPersons.php';
        add_hours_to_person($person_id, $workedHours);
    }

    $con->close();

    return $success;
}

function auto_checkout_missing_shifts() {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $con = connect();

    // Get the current date
    $today = date('Y-m-d');
    $cutoffTime = $today . ' 19:07:00';

    // Find all shifts where the user hasn't checked out yet (endTime is NULL)
    $query = "UPDATE dbshifts
              SET endTime = ?, 
                  totalHours = ROUND(TIMESTAMPDIFF(SECOND, startTime, ?) / 3600, 2),
                  description = '[Auto Checked Out]'
              WHERE endTime IS NULL AND DATE(startTime) <= ?";

    $stmt = $con->prepare($query);
    $stmt->bind_param("sss", $cutoffTime, $cutoffTime, $today);
    $stmt->execute();
    $affected_rows = $stmt->affected_rows;
    $stmt->close();

    // Fetch affected person_ids
    $query = "SELECT DISTINCT person_id FROM dbshifts WHERE endTime = ? AND DATE(startTime) <= ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ss", $cutoffTime, $today);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $person_id = $row['person_id'];
        send_system_message('vmsroot', '[Auto Checkout]', "$person_id was Automatically Checked Out for their shift on $today");
    }
    
    $stmt->close();
    $con->close();

    echo "✅ Auto checked out $affected_rows shifts.\n";
    return $affected_rows;
}

//this will be where volunteers will be automatically archived if they do not work at least 2 hours in 3 months.
function archive_volunteers_from_shifts(){
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $con = connect();

    //defining the 3 month range
    $threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));

    $query = "
        SELECT person_id, SUM(totalHours) AS total_hours
        FROM dbShifts
        WHERE date >= ?
        GROUP BY person_id
        HAVING total_hours < 2 OR total_hours IS NULL";
     
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $threeMonthsAgo);
    $stmt->execute();
    $result = $stmt->get_result();

    $archivedCount = 0;

    //archiving all the volunteers that meet the reuqirements of becoming 'inactive'
    while ($row = $result->fetch_assoc()){
        $person_id = $row['person_id'];

        //updating the status to inactive instead of active - archived volunteer now
        $updateQuery = "UPDATE dbPersons SET status = 'archived' WHERE id = ?";
        $updateStmt = $con->prepare($updateQuery);
        $updateStmt->bind_param("s", $person_id);
        $updateStmt->execute();
        $updateStmt->close();

        //calling the function in dbPersons.php - to fully archive the volunteer 
        archive_volunteer($person_id);

        $archivedCount++;
    }

    $stmt->close();
    $con->close();

    echo "✅ Archived $archivedCount inactive volunteers.\n";
    return $archivedCount;
}
function clockOutByShiftId($shift_id, $description) {
    $con = connect();

    // Step 1: Get the shift's startTime
    $query = "SELECT startTime FROM dbshifts WHERE shift_id = ? AND endTime IS NULL";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $shift_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $shift = $result->fetch_assoc();
    $stmt->close();

    if ($shift && isset($shift['startTime'])) {
        $startTime = new DateTime($shift['startTime']);
        $endTime = new DateTime(); // current time
        $interval = $startTime->diff($endTime);
        $totalHours = $interval->h + ($interval->i / 60); // total as decimal hours

        // Step 2: Update shift with endTime, description, and totalHours
        $update = "UPDATE dbshifts SET endTime = NOW(), description = ?, totalHours = ? WHERE shift_id = ? AND endTime IS NULL";
        $stmt = $con->prepare($update);
        $stmt->bind_param("sds", $description, $totalHours, $shift_id);
        $stmt->execute();
        $stmt->close();
    }
}

?>
