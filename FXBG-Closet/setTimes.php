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

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		require_once('database/dbPersons.php');
		require_once('database/dbEvents.php');
		require_once('include/input-validation.php');

		$userID = $_POST['userID'];
    	$eventID = $_POST['eventID'];

		$the_event = retrieve_event2($eventID);
		$date = $the_event['date'];
		$startTime = $_POST['start-time'];
    	$endTime = $_POST['end-time'];

		$formatted_start_time = $date . ' ' . validate12hTimeAndConvertTo24h($startTime);
		$formatted_end_time = $date . ' ' . validate12hTimeAndConvertTo24h($endTime);

		check_in($userID, $eventID, $formatted_start_time);
		check_out($userID, $eventID, $formatted_end_time);

		$success = 1;

        if ($accessLevel == 1) {
            header('Location: eventList.php');
        } else {
            header('Location: eventList.php?username=' . $userID);
        }
	}

    require_once('include/input-validation.php');

	// Fetch event data
	if (isset($_GET['eventID'], $_GET['eventName'])) {
		$eventID = $_GET['eventID'];
		$eventName = htmlspecialchars($_GET['eventName']);
	} else {
		header('Location: login.php');
		die();
	}

	if ($accessLevel == 1) {
		$userID = $_SESSION['_id'];
	} else {
		if (isset($_GET['userID'])) {
			$userID = $_GET['userID'];
		} else {
			header('Location: login.php');
			die();
		}
	}

	require_once('include/input-validation.php');

 ?>

<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Fredericksburg SPCA | Add New Check-In</title>
    </head>
    <body>
	<?php require_once('header.php') ?>
        <h1>Add New Check-In</h1>

		<?php if (isset($success)): ?>
            <div class="happy-toast">Check-In Added Successfully!</div>
        <?php endif ?>

        <main class="date">

			<h2><?php echo $eventName ?></h2>
            
			<form id="new-check-in" method="post">

                <label for="name">* New Check-In Time </label>                
                <input type="text" id="start-time" name="start-time" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter new check-in time. Ex. 12:00 PM">

                <label for="name">* New Check-Out Time </label>
                <input type="text" id="end-time" name="end-time" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter new check-out time. Ex. 12:00 PM">

				<input type="hidden" id="eventID" name="eventID" value="<?php echo $eventID ?>">
				<input type="hidden" id="userID" name="userID" value="<?php echo $userID ?>">

                <input type="submit" value="Confirm New Check-In">
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