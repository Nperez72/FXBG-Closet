<?php session_cache_expire(30);
    session_start();
    // Make session information accessible, allowing us to associate
    // data with the logged-in user.

    ini_set("display_errors",1);
    error_reporting(E_ALL);

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
    if ($accessLevel < 2) {
        header('Location: login.php');
        //echo 'bad access level';
        die();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once('include/input-validation.php');
        require_once('database/dbTrainings.php');
        $args = sanitize($_POST, null);
        $required = array(
            "name", "date", "start-time", "end-time", "role", "description",
        );
        if (!wereRequiredFieldsSubmitted($args, $required)) {
            echo 'bad form data';
            die();
        } else {
            $validated = validate12hTimeRangeAndConvertTo24h($args["start-time"], $args["end-time"]);
            if (!$validated) {
                echo 'bad time range';
                die();
            }

            $restricted_signup = $args['role'];
            
            
            $startTime = $args['start-time'] = $validated[0];
            $endTime = $args['end-time'] = $validated[1];
            $date = $args['date'] = validateDate($args["date"]);
            //$capacity = intval($args["capacity"]);
            //$abbrevLength = strlen($args['abbrev-name']);
            //if (!$startTime || !$date || $abbrevLength > 11){
            if (!$startTime || !$endTime || !$date > 11){
                echo 'bad args';
                die();
            }
            //var_dump($args);
            $id = create_training($args);
            if(!$id){
                //echo "Oopsy!";
                die();
            } else {
                //echo'<script> location.replace("trainingSuccess.php"); </script>';
                header('Location: trainingSuccess.php');
                exit();
            }
            //require_once('include/output.php');
            
            //$name = htmlspecialchars_decode($args['name']);
            //$startTime = time24hto12h($startTime);
            //$date = date('l, F j, Y', strtotime($date));

            /*require_once('database/dbMessages.php');
            system_message_all_users_except($userID, "A new training was created!", "Exciting news!\r\n\r\nThe [$name](training: $id) training at $startTime on $date was added!\r\nSign up today!");
            header("Location: training.php?id=$id&createSuccess");
            */
            //die();
        }
    }
    $date = null;
    if (isset($_GET['date'])) {
        $date = $_GET['date'];
        $datePattern = '/[0-9]{4}-[0-9]{2}-[0-9]{2}/';
        $timeStamp = strtotime($date);
        if (!preg_match($datePattern, $date) || !$timeStamp) {
            header('Location: calendar.php');
            die();
        }
    }

    // get animal data from database for form
    // Connect to database
    include_once('database/dbinfo.php'); 
    $con=connect();  
    // Get all the animals from animal table
    //$sql = "SELECT * FROM `dbAnimals`";
    //$all_animals = mysqli_query($con,$sql);
    //$sql = "SELECT * FROM `dbLocations`";
    //$all_locations = mysqli_query($con,$sql);
    //$sql = "SELECT * FROM `dbServices`";
    //$all_services = mysqli_query($con,$sql);

?><!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Fredericksburg SPCA | Create Training</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Create Training</h1>
        <main class="date">
            <h2>New Training Form</h2>
            <form id="new-training-form" method="post">
                <label for="name">* Training Name </label>
                <input type="text" id="name" name="name" required placeholder="Enter name"> 
                <!--
                <label for="name">* Abbreviated Name</label>
                <input type="text" id="abbrev-name" name="abbrev-name" maxlength="11" required placeholder="Enter name that will appear on calendar">
                --->
                <label for="name">* Date </label>
                <input type="date" id="date" name="date" <?php if ($date) echo 'value="' . $date . '"'; ?> min="<?php echo date('Y-m-d'); ?>" required>
                <label for="name">* Start Time </label>
                <input type="text" id="start-time" name="start-time" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter start time. Ex. 12:00 PM">
                <label for="name">* End Time </label>
                <input type="text" id="end-time" name="end-time" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter end time. Ex. 1:00 PM">
                <fieldset>
                <label for="role"> * Restrictions </label>
            <div class="radio-group">
                <input type="radio" id="u" name="role" value="u" required><label for="role">Unrestricted</label>
                <input type="radio" id="r" name="role" value="r" required><label for="role">Restricted</label>
            </div>
                </fieldset>
                <label for="name">* Description </label>
                <input type="text" id="description" name="description" required placeholder="Enter description">
                <label for="name">* Training Type </label>
                <input type="text" id="training_level" name="training_level" required placeholder="Enter Traing Type (e.g. Pink, Orange)">
                <label for="name">Location </label>
                <input type="text" id="location" name="location" required placeholder="Enter location">
                <label for="name">Capacity </label>
                <input type="number" id="capacity" name="capacity" required placeholder="Enter capacity (e.g. 1-99)">
                <!-- Service function
                <fieldset>
                    <label for="name">* Service </label>
                    <?php 
                        /*
                        // fetch data from the $all_services variable
                        // and individually display as an option
                        echo '<ul>';
                        while ($service = mysqli_fetch_array(
                                $all_services, MYSQLI_ASSOC)):; 
                            echo '<li><input class="checkboxes" type="checkbox" name="service[]" value="' . $service['id'] . '" required/> ' . $service['name'] . '</li>';
                        endwhile;
                        echo '</ul>';
                        */
                    ?>
                </fieldset> 
                --->

                <!-- Location
                <label for="name">* Location </label>
                <select for="name" id="location" name="location" required>
                    <option value="">--</option>
                    <?php 
                        /*
                        // fetch data from the $all_locations variable
                        // and individually display as an option
                        while ($location = mysqli_fetch_array(
                                $all_locations, MYSQLI_ASSOC)):; 
                        */
                    ?>
                    <option value="
                        <?php //echo $location['id'];?>">
                        <?php //echo $location['name'];?>
                    </option>
                    <?php 
                        //endwhile; 
                        // terminate while loop
                    ?>
                </select><p></p>
                --->
  
                <!--
                <label for="name">* Animal</label>
                <select for="name" id="animal" name="animal" required>
                    <?php 
                        /*
                        // fetch data from the $all_animals variable
                        // and individually display as an option
                        while ($animal = mysqli_fetch_array(
                                $all_animals, MYSQLI_ASSOC)):; 
                        */
                    ?>
                    <option value="
                        <?php //echo $animal['id'];?>
                        ">
                        <?php //echo $animal['name'];?>
                    </option>
                    <?php 
                        // endwhile; 
                        // terminate while loop
                    ?>
                </select>
                <br/>
                <p></p>
                --->
                <input type="submit" value="Create Training">
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