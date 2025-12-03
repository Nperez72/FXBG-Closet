<?php
    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    ini_set("display_errors", 1);
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
if ($accessLevel < 3) {
    header('Location: login.php');
    echo 'bad access level';
    die();
}
    require_once('include/input-validation.php');
    require_once('database/dbEvents.php');
    require_once('database/dbPersons.php');
    require_once('database/dbEventCoordinators.php');
    $errors = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $args = sanitize($_POST, null);
    $required = array(
        "id", "name", "date", "start-time", "end-time", "description");

    if (!wereRequiredFieldsSubmitted($args, $required)) {
        echo 'bad form data';
        die();
    } else {
        require_once('database/dbPersons.php');
        $id = $args['id'];
        $validated = validate12hTimeRangeAndConvertTo24h($args["start-time"], $args["end-time"]);
        if (!$validated) {
            $errors .= '<p>The provided time range was invalid.</p>';
        }
        $startTime = $args['start-time'] = $validated[0];
        $endTime = $args['end-time'] = $validated[1];
        $date = $args['date'] = validateDate($args["date"]);
        $capacity = intval($args["capacity"]);
        $assignedVolunteerCount = count(getvolunteers_byevent($id));
        $difference = $assignedVolunteerCount - $capacity;
        if ($capacity < $assignedVolunteerCount) {
            $errors .= "<p>There are currently $assignedVolunteerCount volunteers assigned to this event. The new capacity must not exceed this number. You must remove $difference volunteer(s) from the event to reduce the capacity to $capacity.</p>";
        }
        if (!$startTime || !$date > 11) {
            $errors .= '<p>Your request was missing arguments.</p>';
        }
        if (!$errors) {
            $success = update_event($id, $args);
            if (!$success) {
                echo "Oopsy!";
                die();
            }
            // Clear existing coordinators for this event
            delete_event_coordinators($id);
            // Add the selected coordinators back
            if (isset($_POST['volunteer-coordinator']) && is_array($_POST['volunteer-coordinator'])) {
                foreach ($_POST['volunteer-coordinator'] as $coord_id) {
                    add_event_coordinator($id, (int)$coord_id);
                }
            }
            header('Location: event.php?id=' . $id . '&editSuccess');
        }
    }
}
if (!isset($_GET['id'])) {
    // uhoh
    die();
}
    $args = sanitize($_GET);
    $id = $args['id'];
    $event = fetch_event_by_id($id);
    $volunteerCoord = getVolunteerCoordinators();
    $assignedCoordinators = get_event_coordinators($id);
if (!$event) {
    echo "Event does not exist";
    die();
}
if ($accessLevel == 3) {
    if (!isset($_SESSION['person_id'])) {
        header('Location: login.php');
        die();
    }
    $personID = $_SESSION['person_id'];
    // Get an array of coordinators assigned to this event
    $coordinator_ids = get_event_coordinators($id);
    $isAssignedCoordinator = false;
    if (isset($_SESSION['person_id']) && is_array($coordinator_ids)) {
        $isAssignedCoordinator = in_array($_SESSION['person_id'], $coordinator_ids);
    }
    if (!$isAssignedCoordinator) {
        header('Location: login.php');
        echo 'bad access level';
        die();
    }
}
    require_once('include/output.php');

    // get animal data from database for form
    // Connect to database
    include_once('database/dbinfo.php');
    $con = connect();
    /*$sql = "SELECT * FROM `dbLocations`";
    $all_locations = mysqli_query($con,$sql);
    $sql = "SELECT * FROM `dbServices`";
    $all_services = mysqli_query($con,$sql);

    // get current selected services for event
    $current_services = get_services($id);
    */
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Fredericksburg SPCA | Edit Event</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Edit Event</h1>
        <main class="date">
        <?php if ($errors) : ?>
            <div class="error-toast"><?php echo $errors ?></div>
        <?php endif ?>
            <h2>Event Details</h2>
            <form id="new-event-form" method="post">
                <label for="name">* Event Name </label>
                <input type="hidden" name="id" value="<?php echo $id ?>"/> 
                <input type="text" id="name" name="name" value="<?php echo $event['name'] ?>" required placeholder="Enter name"> 
                <!--
                <label for="name">Abbreviated Name</label>
                <input type="text" id="abbrev-name" name="abbrev-name" value="<//?php echo $event['abbrevName'] ?>" maxlength="11"  required placeholder="Enter name that will appear on calendar">
                --->
                <label for="name">* Date </label>
                <input type="date" id="date" name="date" value="<?php echo $event['date'] ?>" min="<?php echo date('Y-m-d'); ?>" required>
                <label for="name">* Start Time </label>
                <input type="text" id="start-time" name="start-time" value="<?php echo time24hto12h($event['startTime']) ?>" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter start time. Ex. 12:00 PM">
                <label for="name">* End Time </label>
                <input type="text" id="end-time" name="end-time" value="<?php echo time24hto12h($event['endTime']) ?>" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter end time. Ex. 12:00 PM">
                <label for="type">* Event Type </label>
                <select id="type" name="type" required>
                    <?php
                        $options = array("Outreach", "Festival", "Fundraiser", "Youth Program", "Womxns Program", "Silver Pride Program",
                            "Game Night Program", "Youth Reading Program", "Adult Reading Program", "Other");
                        // Check if current event type is valid
                        $selected_type = isset($event['type']) ? $event['type'] : '';
                        $is_valid_type = in_array($selected_type, $options);
                        // Default "Select Event Type" option
                        echo '<option value="" ' . (!$is_valid_type ? 'selected' : '') . '>Select Event Type</option>';
                        // Generate all event type options
                        foreach ($options as $option) {
                            $selected = ($is_valid_type && $option == $selected_type) ? 'selected' : '';
                            echo "<option value=\"{$option}\" {$selected}>{$option}</option>";
                        }
                        ?>
                </select>
                <label for="name">* Description </label>
                <input type="text" id="description" name="description" value="<?php echo $event['description'] ?>" required placeholder="Enter description">
                <label for="name">Location </label>
                <input type="text" id="location" name="location" value="<?php echo $event['location'] ?>" placeholder="Enter location">
                <label for="name">Capacity </label>
                <input type="number" id="capacity" name="capacity" value="<?php echo $event['capacity'] ?>" placeholder="Enter capacity (e.g. 1-99)">
                <label for="volunteer-coordinator">Assigned Volunteer Coordinators:</label>
                    <?php if (empty($volunteerCoord)) : ?>
                        <p>No available volunteer coordinators.</p>
                    <?php else : ?>
                        <div class="coordinator-checkboxes">
                            <?php foreach ($volunteerCoord as $vc) : ?>
                                <?php
                                    $checked = in_array($vc['person_id'], $assignedCoordinators) ? 'checked' : '';
                                ?>
                                <label>
                                    <input type="checkbox"
                                        name="volunteer-coordinator[]"
                                        value="<?= htmlspecialchars($vc['person_id']) ?>"
                                        <?= $checked ?>>
                                    <?= htmlspecialchars($vc['fullname']) ?>
                                </label><br>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <!--<fieldset>
                    <label for="name">* Service </label>
                    </?php 
                        // fetch data from the $all_services variable
                        // and individually display as an option
                        echo '<ul>';
                        while ($service = mysqli_fetch_array(
                                $all_services, MYSQLI_ASSOC)):; 
                            $shouldCheck = false;
                            foreach($current_services as $current_serv) {
                                if ($service['id'] == $current_serv['id']) {
                                    $shouldCheck = true;
                                }
                            }
                            if ($shouldCheck) {
                                echo '<li><input class="checkboxes" type="checkbox" name="service[]" value="' . $service['id'] . '" checked required/> ' . $service['name'];
                            } else {
                                echo '<li><input class="checkboxes" type="checkbox" name="service[]" value="' . $service['id'] . '" required/> ' . $service['name'];
                            }
                        endwhile;
                    ?>
                </fieldset> --->
                <!--<label for="name">Location </label>
                <select for="name" id="location" name="location" required>
                    </?php 
                        // fetch data from the $all_locations variable
                        // and individually display as an option
                        while ($location = mysqli_fetch_array(
                                $all_locations, MYSQLI_ASSOC)):; 
                    
                            if ($event['locationID'] == $location['id']) {
                                echo '<option selected value="' . $location['id']. '">';
                            } else {
                                echo '<option value="' . $location['id']. '">';
                            }
                            echo $location['name'];
                            echo '</option>';
                        
                        endwhile; 
                        // terminate while loop
                    ?>
                </select>---><p></p>
                <input type="submit" value="Update Event">
                <a class="button cancel" href="event.php?id=<?php echo htmlspecialchars($_GET['id']) ?>" style="margin-top: .5rem">Cancel</a>
            </form>

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