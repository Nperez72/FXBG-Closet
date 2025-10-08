<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();
    date_default_timezone_set("America/New_York");

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    $user = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }
    if ($accessLevel < 1) {
        header('Location: login.php');
        die();
    }
    if ($accessLevel == 1) {
        $user = $_SESSION['_id'];
    }
    $eventId = isset($_GET['eventId']) ? htmlspecialchars($_GET['eventId']) : null;
    $oldStartTime = isset($_GET['start_time']) ? htmlspecialchars($_GET['start_time']) : null;
    $oldEndTime = isset($_GET['end_time']) ? htmlspecialchars($_GET['end_time']) : null;
    //$UNIX = 0;

    // Ensure fallback values or error handling
    if (!$eventId || !$oldStartTime || !$oldEndTime) {
        echo "Missing required data.";
        die(); // Stop further execution
    }
    // if (!isset($_GET['date'])) {
    //     header('Location: calendar.php');
    //     die();
    // }
    // debugged variables
//     echo "<pre>";
// print_r($_GET);
// print_r($_POST);
// echo "</pre>";
//     require_once('include/input-validation.php');
  //  $get = sanitize($_GET);
    //$eventIDGiven = $get['id'];
    
    // Split the string by "?"
    //$parts = explode('?', $eventIDGiven);

    // Assign each part to a variable
    //$id = $parts[0];
    //parse_str($parts[1], $userArray);  // Extracts "user" key-value pair
    //$user = $userArray['user'];
    //parse_str($parts[2], $timeArray);  // Extracts "start_time" key-value pair
    //$old_start_time = $timeArray['old_start_time'];

    // Output the variables
    //echo "ID: $id\n";
    //echo "User: $user\n";
    //echo "Start Time: $old_start_time\n";

    // Split the string by "?"
   // $parts = explode('?', $eventIDGiven);

    // Assign each part to a variable
 //   $id = $parts[0];

    // Check if $parts[1] exists before trying to parse it
    if (isset($parts[1])) {
        parse_str($parts[1], $userArray);
        $user = isset($userArray['user']) ? $userArray['user'] : 'Unknown User';
    } else {
        $user = 'Unknown User';
    }

    // Check if $parts[2] exists before trying to parse it
    if (isset($parts[2])) {
        parse_str($parts[2], $timeArray);
        $old_start_time = isset($timeArray['old_start_time']) ? $timeArray['old_start_time'] : 'No Start Time';
    } else {
        $old_start_time = 'No Start Time';
    }

    // Output the variables
    // echo "ID: $id\n";
    // echo "User: $user\n";
    // echo "Start Time: $old_start_time\n";


    // $datePattern = '/[0-9]{4}-[0-9]{2}-[0-9]{2}/';
    $timeStamp = strtotime($old_start_time);
    // if (!preg_match($datePattern, $date) || !$timeStamp) {
    //     header('Location: calendar.php');
    //     die();
    // }

// Check if session variables are set
// if (isset($_SESSION['edit_event'])) {
//     $eventData = $_SESSION['edit_event'];

//     $eventID = $eventData['id'];
//     $username = $eventData['user'];
//     $oldStartTime = $eventData['old_start_time'];
//     $oldEndTime = $eventData['old_end_time'];

//     $timeStampStart = strtotime($oldStartTime);
//     $timeStampEnd = strtotime($oldEndTime);

//     $displayTimeStart = $timeStampStart !== false 
//         ? date("l, F j, Y, H:i:s", $timeStampStart) 
//         : "Invalid time format.";

//     $displayTimeEnd = $timeStampEnd !== false 
//         ? date("l, F j, Y, H:i:s", $timeStampEnd) 
//         : "Invalid time format.";
// } else {
//     $displayTimeStart = "Invalid or missing data.";
//     $displayTimeEnd = "Invalid or missing data.";
// }

// TEMPORARY
// TODO GET RID OF
// $time = "03:02 PM";
// $convertedTime = validate12hTimeAndConvertTo24hAmPm($time);
// if (!$convertedTime) {
//     echo "Invalid time format: " . htmlspecialchars($time);
// } else {
//     echo "Converted time: $convertedTime";
// }

// Fetch event data from session
if (isset($_GET['eventId'], $_GET['user'], $_GET['start_time'], $_GET['end_time'])) {
    $eventId = htmlspecialchars($_GET['eventId']);
    if ($accessLevel == 1) {
        $user = $_SESSION['_id'];
    }
    else {
    $user = htmlspecialchars($_GET['user']);
    }
    $oldStartTime = htmlspecialchars($_GET['start_time']); // good
    $oldEndTime = htmlspecialchars($_GET['end_time']); // good
    //$eventData = $_SESSION['edit_event'];

    //$eventID = $eventData['id'];
    //$username = $eventData['user'];
    //$oldStartTime = $eventData['old_start_time'];
    //$oldEndTime = $eventData['old_end_time'];

    $timeStampStart = strtotime($oldStartTime);
    $timeStampEnd = strtotime($oldEndTime);

    $displayTimeStart = $timeStampStart !== false 
        ? date("l, F j, Y, g:i:s A", $timeStampStart)  
        : "Invalid time format.";

    $displayTimeEnd = $timeStampEnd !== false 
        ? date("l, F j, Y, g:i:s A", $timeStampEnd) 
        : "Invalid time format.";
    
    $displayTimeStartSQL = $oldStartTime !== false ? date("Y-m-d H:i:s", $timeStampStart) : null;
    $displayTimeEndSQL = $oldEndTime !== false ? date("Y-m-d H:i:s", $timeStampEnd) : null;

  //  echo $displayTimeEndSQL;
   // echo $displayTimeStartSQL;
    
    // Validate conversion
    if (!$displayTimeStartSQL || !$displayTimeEndSQL) {
        echo "Invalid date or time format.";
       // die();
    }
} else {
    $displayTimeStart = "Invalid or missing data.";
    $displayTimeEnd = "Invalid or missing data.";
}
?>
<?php


require_once('include/input-validation.php');
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $startTime = trim($_POST['start-time'] ?? '');
    $endTime = trim($_POST['end-time'] ?? '');
    // echo "STARTTTT: ";
    // echo $startTime;
    // echo "ENDDDD: ";
    // echo $endTime;
    //echo $user;

    if (!empty($startTime) && !empty($endTime)) {
        $formattedStartDateTime = date("Y-m-d", strtotime($displayTimeStartSQL));
       // echo $formattedStartDateTime;
      //  echo $timeStampStart;
        //$ymdStartDate = strtotime($displayTimeStart->format('Y-m-d'));
       // $formattedStartDateTime->modify($ymdStartDate);
       // echo $formattedStartDateTime;
        $formattedEndDateTime = date("Y-m-d", strtotime($displayTimeEndSQL));
// Y-m-d needed before times too
// use this to convert start time and end time
$validated = validate12hTimeRangeAndConvertTo24h($_POST['start-time'], $_POST['end-time']);
// AmPm version vs not no diff at top?
if (!$validated) {
    $errors .= '<p>The provided time range was invalid.</p>';
}
$startTime = $args['start-time'] = $validated[0];
$endTime = $args['end-time'] = $validated[1];
//echo "start:" . $startTime;
//echo $endTime;
//echo "START: ";
// Combine date and time
$formattedStartDateTime = $formattedStartDateTime . ' ' . $startTime;


// Use the string in your query or other operations
//echo "START: " . $formattedStartDateTime;
// Combine date and time
$formattedEndDateTime = $formattedEndDateTime . ' ' . $endTime;




//echo "END: ";
//echo $formattedEndDateTime; 
//echo "<br>" . $user . "<br>" . $eventId;

        //echo $displayTimeStartSQL;
        //echo $displayTimeEndSQL;
        //echo $oldStartTime;

        // Connect to the database
        $connection = connect();

        // Prepare the SQL query
        $query = "UPDATE dbpersonhours 
                  SET start_time = '" . $formattedStartDateTime . "', end_time = '" . $formattedEndDateTime .  "'" .
                  " WHERE personID = '" . $user . "' AND eventID = " . $eventId . " AND start_time = '" . $displayTimeStartSQL . "' AND end_time = '" . $displayTimeEndSQL . "' LIMIT 1";
        // Debugging: Echo the query to see what is being executed
//echo "SQL Query: " . $query . "<br>";
                  $stmt = mysqli_prepare($connection, $query);
        // mysqli_stmt_bind_param($stmt, "ssss", $formattedStartDateTime, $formattedEndDateTime, $user, $eventID);

        // if (mysqli_stmt_execute($stmt)) {
        //     echo "Volunteer hours updated successfully.";
        // } else {
        //     echo "Error updating hours: " . mysqli_error($connection);
        // }

        // // Close statement and connection
        // mysqli_stmt_close($stmt);
        // Execute the query
if (mysqli_query($connection, $query)) {
    // On successful update, redirect to the desired URL with query parameters
    // CHANGE IF LOCALHOST OR NOT
    if ($accessLevel == 1) {
        header('Location: eventList.php');
    } else {
        header('Location: eventList.php?username=' . $user);
    }
    //header("Location: editTimes.php?eventId=$eventId&user=" . urlencode($user) . "&start_time=" . urlencode($formattedStartDateTime) . "&end_time=" . urlencode($formattedEndDateTime));
    //header("Location: http://localhost/stepvarepo/editTimes.php?eventId=$eventId&user=" . urlencode($user) . "&start_time=" . urlencode($formattedStartDateTime) . "&end_time=" . urlencode($formattedEndDateTime));
    exit(); // Make sure to call exit after the header to stop further code execution
} else {
    echo "Error updating hours: " . mysqli_error($connection);
}
        mysqli_close($connection);
    } else {
        echo "Please fill in both start and end times.";
    }
}

 ?>



<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Step VA | View Date</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Edit Event</h1>
        <!-- <?php
        echo "<p>rain</p>";
        echo "<p>EndFormatted:" . $formattedEndDateTime . "</p>";
        echo "<p>" . $formattedStartDateTime . "</p>";
        echo "<p>" . $oldStartTime . "</p>";
        echo "<p>" . $oldEndTime . "</p>";
        ?> -->
        <main class="date">
            <h2>Edit this time: 
            </h2>            
            <form id="new-event-form" method="post">
            <!-- <p><?= $formattedEndDateTime ?></p>   -->
            <p><strong>Start Time: </strong><?= htmlspecialchars($displayTimeStart); ?></p>  
            <p><strong>End Time: </strong><?= htmlspecialchars($displayTimeEnd); ?></p> 
            <!--  <label for="name">* Event Name </label>
                <input type="text" id="name" name="name" required placeholder="Enter name"> 
                <!-- <label for="name">* Abbreviated Event Name</label> 
                <input type="text" id="abbrev-name" name="abbrev-name" maxlength="11" required placeholder="Enter name that will appear on calendar">
-->
                <label for="name">* New Start Time </label>
                <!--- add pattern -->
                
                <input type="text" id="start-time" name="start-time" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter new start time. Ex. 12:00 PM">
                <label for="name">* New End Time </label>
                 <!--- add pattern -->
                 <input type="text" id="end-time" name="end-time" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter new end time. Ex. 12:00 PM">
                
                <!--
                <label for="name">* What Time Did You Arrive For This Event? </label>
                <input type="text" id="start-time" name="start-time" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter arrival time. Ex. 12:00 PM">
                <label for="name">* What Time Did You Leave For This Event? </label>
                <input type="text" id="departure-time" name="departure-time" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter departure time. Ex. 3:00 PM">
-->

                <p></p>
                <br/>
                <p></p>
                <input type="submit" value="Change Volunteer Hours">
            </form>

            <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
  
                <!--
                <label for="name">* Animal</label>
                <select for="name" id="animal" name="animal" required>
                    <?php 
                        // fetch data from the $all_animals variable
                        // and individually display as an option
                        while ($animal = mysqli_fetch_array(
                                $all_animals, MYSQLI_ASSOC)):; 
                    ?>
                    <option value="<?php echo $animal['id'];?>">
                        <?php echo $animal['name'];?>
                    </option>
                    <?php 
                        endwhile; 
                        // terminate while loop
                    ?>
                </select>
                <br/>
                <p></p>
                <input type="submit" value="Create Event">
            </form>
                <?php if ($date): ?>
                    <a class="button cancel" href="calendar.php?month=<?php echo substr($date, 0, 7) ?>" style="margin-top: -.5rem">Return to Calendar</a>
                <?php else: ?>
                    <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
                <?php endif ?>

                <!-- Require at least one checkbox be checked -->
                <script type="text/javascript">
                    $(document).ready(function(){
                        var checkboxes = $('.checkboxes');
                        checkboxes.change(function(){
                            if($('.checkboxes:checked').length>0) {
                                checkboxes.removeAttr('required');
                            } else {
                                checkboxes.attr('required', 'required');
                            }
                        });
                    });

                </script>
                
        </main>
    </body>
</html>