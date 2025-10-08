<?php
    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    } 
    // Require admin privileges
    /*if ($accessLevel == 1) {
        header('Location: login.php');
        echo 'bad access level';
        die();
    }*/
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once('include/input-validation.php');
        require_once('database/dbEvents.php');
        require_once('database/dbPersons.php');
        $args = sanitize($_POST, null);
        $required = array(
            "event-name", "account-name", /*"start-time", "departure-time", /*"skills",*/ /*"diet-restrictions", "disabilities", "materials", "role"*/
        );

        if (!wereRequiredFieldsSubmitted($args, $required)) {
            echo 'bad form data';
            die();
        // } else if(!checkIfTrainingComplete()) {
        //     echo 'training requirement not met';
        //     die();
        // } else {
            //$validated = validate12hTimeRangeAndConvertTo24h($args["start-time"], "11:59 PM");
            if (!$validated) {
                echo 'bad time range';
                die();
            }
            //$startTime = $args['start-time'] = $validated[0];
            //$validated2 = validate12hTimeRangeAndConvertTo24h($args["departure-time"], "11:59 PM");
            // if (!$validated2) {
            //     echo 'bad time range';
            //     die();
            // }

            //$departureTime = $args['departure-time'] = $validated2[0];

            // address trying to validate time range of start time and end time
            // $validatedDepartureTimeAfterStartTime = validate24hTimeRange($startTime, $departureTime);
            // if (!$validatedDepartureTimeAfterStartTime) {
            //     header("Location: eventFailureBadDepartureTime.php");
            //     die();
            // }

            
            $name = htmlspecialchars_decode($args['name']);
            $account_name = htmlspecialchars_decode($args['account-name']);
            $role = $args['role'];
            $notes = "Skills: " . $args['skills'] . " | Dietary restrictions: " . $args['restrictions'] . " | Disabilities: " . $args['disabilities'] . " | Materials: " . $args['materials'];
            //$date = $args['date'] = validateDate($args["date"]);
            //$capacity = intval($args["capacity"]);
            //$abbrevLength = strlen($args['abbrev-name']);
            //if (!$startTime /*|| !$date */|| $abbrevLength > 11){
                //echo 'bad args';
                //die();
            //}
            //if (!$departureTime /*|| !$date */|| $abbrevLength > 11){
                //echo 'bad args';
                //die();
            //}
            $restricted = htmlspecialchars(string: isset($_GET['restricted']) ? $_GET['restricted'] : '');

            if($restricted == "Yes") {
                $name = htmlspecialchars(string: isset($_GET['event_name']) ? $_GET['event_name'] : '');
                //echo "hey";
                $id = request_event_signup($name, $account_name, $role, $notes/*$signup*/);

                if(!$id){
                    header('Location: requestFailed.php');
                    die();
                }
                
                require_once('database/dbMessages.php');
                send_system_message($userID, "Your request to sign up for $name has been sent to an admin.", "Your request to sign up for $name will be reviewed by an admin shortly. You will get another notification when you get approved or denied.");
                //send_system_message("vmsroot", "$userID requested to sign up for a restricted event", "$userID requested to sign up for $name. Please review.");

                //$name = htmlspecialchars(string: isset($_GET['event_name']) ? $_GET['event_name'] : '');
                //$id = sign_up_for_event($name, $account_name, $role, $notes);
                header('Location: signupPending.php');
                die();
            } else {
                $name = htmlspecialchars(string: isset($_GET['event_name']) ? $_GET['event_name'] : '');
            
                $id = sign_up_for_event($name, $account_name, $role, $notes);
            if(!$id){
                header(header: 'Location: eventFailure.php');
                exit();
            }
            require_once('database/dbMessages.php');
            send_system_message($userID, "You are now signed up for $name!", "Thank you for signing up for $name!");
            header('Location: signupSuccess.php');

            //require_once('include/output.php');
            require_once('eventApproved.php');

            //$startTime = time24hto12h($startTime);
            //$date = date('l, F j, Y', timestamp: strtotime($date));
            //require_once('database/dbMessages.php');
            //system_message_all_users_except($userID, "Your sign-up has been approved!", "Congratulations!");
            //header(header: "Location: eventApproved.php?id=$id&createSuccess");
            //header(header: "Location: eventApproved.php?id=$id&createSuccess");
            die();
        }
        }
    }
    /*$date = null;
    if (isset($_GET['date'])) {
        $date = $_GET['date'];
        $datePattern = '/[0-9]{4}-[0-9]{2}-[0-9]{2}/';
        $timeStamp = strtotime($date);
        if (!preg_match($datePattern, $date) || !$timeStamp) {
            header('Location: calendar.php');
            die();
        }
    }*/

    // get animal data from database for form
    // Connect to database
    include_once('database/dbinfo.php'); 
    $con=connect();  
    // Get all the animals from animal table
    /*$sql = "SELECT * FROM `dbAnimals`";
    $all_animals = mysqli_query($con,$sql);
    $sql = "SELECT * FROM `dbLocations`";
    $all_locations = mysqli_query($con,$sql);
    $sql = "SELECT * FROM `dbServices`";
    $all_services = mysqli_query($con,$sql);*/
    
    // Get the event information from URL parameters if they exist
    $event_name = isset($_GET['event_name']) ? htmlspecialchars($_GET['event_name']) : '';

    //$abbrevName = isset($_GET['abbrev-name']) ? htmlspecialchars($_GET['abbrev-name']) : '';
    //$startTime = isset($_GET['start-time']) ? htmlspecialchars($_GET['start-time']) : '';
    //$departureTime = isset($_GET['departure-time']) ? htmlspecialchars($_GET['departure-time']) : '';
    
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else {
        // Handle the case where the username is not set in the session
        $username = ''; // You can set it to an empty string or handle the error as needed
        // For example, redirecting the user to login page:
        // header('Location: login.php');
        // exit();
    }

    // Debugging: Output the session data to inspect
    //var_dump($_SESSION); // This will show all session variables

    if (isset($_SESSION['_id'])) {
        $account_name = $_SESSION['_id'];
    } else {
        $account_name = ''; // Default value if the account name is not set
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Fredericksburg SPCA | Sign-Up for Event</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Sign-Up for Event</h1>
        <main class="date">
            <h2>Sign-Up for Event Form</h2>
            <form id="new-event-form" method="post">
                <label for="event-name">* Event Name </label>
                <input type="text" id="event-name" name="event-name" required 
                    value="<?php echo htmlspecialchars(isset($_GET['event_name']) ? $_GET['event_name'] : ''); ?>" 
                    placeholder="Event name" readonly>



                <!-- Autofill and make the account name readonly -->               
                <label for="account-name">* Your Account Name </label>
                <input type="text" id="account-name" name="account-name" 
                    <?php echo ($accessLevel >= 2) ? '' : 'readonly'; ?> 
                    value="<?php echo htmlspecialchars($account_name); ?>" 
                    placeholder="Enter account name">

                <label for="skills"> Do You Have Any Skills To Share? </label>
                <input type="text" id="skills" name="skills" placeholder="Enter skills. Ex. crochet, tap dancer">
                <label for="disabilities"> Do You Have Any Disabilities We Should Be Aware Of? </label>
                <input type="text" id="disabilities" name="disabilities" placeholder="Enter disabilities">
                <label for="materials"> Are You Bringing Any Materials (e.g. snacks, craft supplies)? </label>
                <input type="text" id="materials" name="materials" placeholder="Enter materials. Ex. felt, pipe cleaners">
                
                <fieldset>
                <label for="role">* Are you a volunteer or a participant? </label>
            <div class="radio-group">
                <input type="radio" id="v" name="role" value="v" required><label for="role">Volunteer</label>
                <input type="radio" id="p" name="role" value="p" required><label for="role">Participant</label>
            </div>
                </fieldset>
                
                </fieldset> 
                <p></p>
                <br/>
                <p></p>
                <input type="submit" value="Sign up for Event">
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
                <?php /*if ($date): ?>
                    <a class="button cancel" href="calendar.php?month=<?php echo substr($date, 0, 7) ?>" style="margin-top: -.5rem">Return to Calendar</a>
                <?php else: ?>
                    <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
                <?php endif */?>
        </main>
    </body>
</html>