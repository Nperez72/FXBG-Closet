<?php 

    session_cache_expire(30);
    session_start();

    // Ensure user is logged in
    if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
        header('Location: login.php');
        die();
    }
    require_once('include/input-validation.php');
    $args = sanitize($_GET);
    if (isset($args["id"])) {
        $id = $args["id"];
    } else {
        header('Location: calendar.php');
        die();
  	}
  	
  	include_once('database/dbEvents.php');
  	
    // We need to check for a bad ID here before we query the db
    // otherwise we may be vulnerable to SQL injection(!)
  	$event_info = fetch_event_by_id($id);
    if ($event_info == NULL) {
        // TODO: Need to create error page for no event found
        // header('Location: calendar.php');

        // Lauren: changing this to a more specific error message for testing
        echo 'bad event ID';
        die();
    }

    include_once('database/dbPersons.php');
    $access_level = $_SESSION['access_level'];
    $user = retrieve_person($_SESSION['_id']);
    $active = $user->get_status() == 'Active';

    ini_set("display_errors",1);
    error_reporting(E_ALL);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $args = sanitize($_POST);
        $get = sanitize($_GET);
        if (isset($_POST['attach-post-media-submit'])) {
            if ($access_level < 2) {
                echo 'forbidden';
                die();
            }
            $required = [
                'url', 'description', 'format', 'id'
            ];
            if (!wereRequiredFieldsSubmitted($args, $required)) {
                echo "dude, args missing";
                die();
            }
            $type = 'post';
            $format = $args['format'];
            $url = $args['url'];
            if ($format == 'video') {
                $url = convertYouTubeURLToEmbedLink($url);
                if (!$url) {
                    echo "bad video link";
                    die();
                }
            } else if (!validateURL($url)) {
                echo "bad url";
                die();
            }
            $eid = $args['id'];
            $description = $args['description'];
            if (!valueConstrainedTo($format, ['link', 'video', 'picture'])) {
                echo "dude, bad format";
                die();
            }
            attach_post_event_media($eid, $url, $format, $description);
            header('Location: event.php?id=' . $id . '&attachSuccess');
            die();
        }
        if (isset($_POST['attach-training-media-submit'])) {
            if ($access_level < 2) {
                echo 'forbidden';
                die();
            }
            $required = [
                'url', 'description', 'format', 'id'
            ];
            if (!wereRequiredFieldsSubmitted($args, $required)) {
                echo "dude, args missing";
                die();
            }
            $type = 'post';
            $format = $args['format'];
            $url = $args['url'];
            if ($format == 'video') {
                $url = convertYouTubeURLToEmbedLink($url);
                if (!$url) {
                    echo "bad video link";
                    die();
                }
            } else if (!validateURL($url)) {
                echo "bad url";
                die();
            }
            $eid = $args['id'];
            $description = $args['description'];
            if (!valueConstrainedTo($format, ['link', 'video', 'picture'])) {
                echo "dude, bad format";
                die();
            }
            attach_event_training_media($eid, $url, $format, $description);
            header('Location: event.php?id=' . $id . '&attachSuccess');
            die();
        }
    } else {
        if (isset($args["request_type"])) {
            //if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request_type = $args['request_type'];
            if (!valueConstrainedTo($request_type, 
                    array('add self', 'add another', 'remove'))) {
                echo "Bad request";
                die();
            }
            $eventID = $args["id"];
    
            // Check if Get request from user is from an organization member
            // (volunteer, admin/super admin)
            if ($request_type == 'add self' && $access_level >= 1) {
                if (!$active) {
                    echo 'forbidden';
                    die();
                }
                $volunteerID = $args['selected_id'];
                $person = retrieve_person($volunteerID);
                $name = $person->get_first_name() . ' ' . $person->get_last_name();
                $name = htmlspecialchars_decode($name);
                require_once('database/dbMessages.php');
                require_once('include/output.php');
                $event = fetch_event_by_id($eventID);
                
                $eventName = htmlspecialchars_decode($event['name']);
                $eventDate = date('l, F j, Y', strtotime($event['date']));
                $eventStart = time24hto12h($event['start-time']);
                $eventEnd = time24hto12h($event['end-time']);
                system_message_all_admins("$name signed up for an event!", "Exciting news!\r\n\r\n$name signed up for the [$eventName](event: $eventID) event from $eventStart to $eventEnd on $eventDate.");
                // Check if GET request from user is from an admin/super admin
            // (Only admins and super admins can add another user)
            } else if ($request_type == 'add another' && $access_level > 1) {
                $volunteerID = strtolower($args['selected_id']);
                if ($volunteerID == 'vmsroot') {
                    echo 'invalid user id';
                    die();
                }
                require_once('database/dbMessages.php');
                require_once('include/output.php');
                $event = fetch_event_by_id($eventID);
                $eventName = htmlspecialchars_decode($event['name']);
                $eventDate = date('l, F j, Y', strtotime($event['date']));
                $eventStart = time24hto12h($event['startTime']);
                $eventEnd = time24hto12h($event['endTime']);
                send_system_message($volunteerID, 'You were assigned to an event!', "Hello,\r\n\r\nYou were assigned to the [$eventName](event: $eventID) event from $eventStart to $eventEnd on $eventDate.");
            } else {
                header('Location: event.php?id='.$eventID);
                die();
            }
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <?php 
        require_once('universal.inc');
    ?>
    <title>Fredericksburg SPCA | View Event: <?php echo $event_info['name'] ?></title>
    <link rel="stylesheet" href="css/event.css" type="text/css" />
    <?php if ($access_level >= 2) : ?>
        <script src="js/event.js"></script>
    <?php endif ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php require_once('header.php') ?>
    <h1>View Event</h1>
    <main class="event-info">
        <!-- Success notifications -->
        <?php if (isset($_GET['createSuccess'])): ?>
            <div class="happy-toast">Event created successfully!</div>
        <?php endif ?>
        <?php if (isset($_GET['editSuccess'])): ?>
            <div class="happy-toast">Event details updated successfully!</div>
        <?php endif ?>
        <?php if (isset($_GET['cancelSuccess'])): ?>
            <div class="happy-toast">Sign-up canceled successfully!</div>
        <?php endif ?>
        <!-- Facebook share button -->
        <div id="fb-root"></div>
        <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v22.0"></script>
        <!--@@@ Thomas: if user clicked check in/out-->
        <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['checking_in'])) {
                    $personID = $_POST['personID'];
                    $eventID = $_POST['eventID'];
                    $timestamp = $_POST['timestamp'];
                    check_in($personID, $eventID, $timestamp);
                    echo "<div class='happy-toast'>You've checked in!</div>";
                }
                else if (isset($_POST['checking_out'])) {
                    $personID = $_POST['personID'];
                    $eventID = $_POST['eventID'];
                    $timestamp = $_POST['timestamp'];
                    check_out($personID, $eventID, $timestamp);
                    echo "<div class='happy-toast'>You've checked out!</div>";
                }
                else if (isset($_POST['archiving'])) {
                    $eventID = $_POST['eventID'];
                    archive_event($eventID);
                    echo "<div class='happy-toast'>Event has been archived!</div>";
                }
                else if (isset($_POST['unarchiving'])) {
                    $eventID = $_POST['eventID'];
                    unarchive_event($eventID);
                    echo "<div class='happy-toast'>Event has been unarchived!</div>";
                }
            }
        ?>
        <!---->
        
        <?php
            require_once('include/output.php');
            $event_name = $event_info['name'];
            $event_date = date('l, F j, Y', strtotime($event_info['date']));
            $event_startTime = time24hto12h($event_info['startTime']);
            $event_endTime = time24hto12h($event_info['endTime']);
            $event_description = $event_info['description'];
            $event_location = $event_info['location'];
            $event_capacity = $event_info['capacity'];
            $event_training_level = $event_info['training_level_required'];
            require_once('include/time.php');
        ?>

        <!-- Event Information Table -->
        <h2 style="font-size: 2.25em; font-weight: 700; color: black;">
            <?php echo htmlspecialchars_decode($event_name); ?>
            <?php if ($access_level >= 2): ?>
                <a href="editEvent.php?id=<?= $id ?>" title="Edit Event" class="edit-icon">
                    <i class="fas fa-pencil-alt"></i>
                <a href="deleteEvent.php?id=<?= $id ?>" title="Delete Event" class="delete-icon" 
                    onclick="return confirmDelete(<?= htmlspecialchars($id) ?>);">
                        <i class="fas fa-trash"></i>
                </a>
            <?php endif; ?>
        </h2>

        <script>
            function confirmDelete() {
                if (confirm("Are you sure you want to delete this event?")) {
                    // If the user clicks "OK", navigate to the link
                    window.location.href = 'deleteEvent.php?eventID=' + eventID;
                }
            }
        </script>

                <div id="table-wrapper">
            <table>
                <tr>  
                    <td class="label">Date</td>
                    <td><?php echo $event_date; ?></td>
                </tr>
                <tr>
                    <td class="label">Time</td>
                    <td><?php echo $event_startTime . " - " . $event_endTime; ?></td>
                </tr>
                <tr>
                    <td class="label">Location</td>
                    <td>
                        <?php echo wordwrap($event_location, 50, "<br />\n"); ?>
                    </td>
                </tr>
                
                <tr>
                    <td class="label">Description</td>
                    <td>
                        <?php echo wordwrap($event_description, 50, "<br />\n"); ?>
                    </td>
                </tr>
                <tr>
                    <td class="label">Capacity</td>
                    <td id="description-cell"><?php echo $event_capacity; ?></td>
                </tr>
                <tr>
                    <td class="label">Training Required</td>
                    <td><?php if($event_training_level == null) {
                        $event_training_level = "N/A";
                    }
                    echo $event_training_level; ?></td>
                </tr>
            </table>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">

            <!--@@@ Check-In and Check-Out Buttons by Thomas -->
            <?php if (can_check_in($user->get_id(), $event_info))  : ?>
                <form method="POST" action="">
                    <input type="hidden" name="checking_in" value="1">
                    <input type="hidden" name="personID" value="<?php echo $user->get_id(); ?>">
                    <input type="hidden" name="eventID" value="<?php echo $event_info['id']; ?>">
                    <input type="hidden" name="timestamp" value="<?php echo date("Y-m-d H:i:s", time()); ?>">
                    <input type="hidden" name="id" value="<?php echo $event_info['id']; ?>">
                    <button type="submit" class="button success">Check-In</button>
                </form>
            <?php endif ?>

            <?php if (can_check_out($user->get_id(), $event_info))  : ?>
                <form method="POST" action="">
                    <input type="hidden" name="checking_out" value="1">
                    <input type="hidden" name="personID" value="<?php echo $user->get_id(); ?>">
                    <input type="hidden" name="eventID" value="<?php echo $event_info['id']; ?>">
                    <input type="hidden" name="timestamp" value="<?php echo date("Y-m-d H:i:s", time()); ?>">
                    <input type="hidden" name="id" value="<?php echo $event_info['id']; ?>">
                    <button type="submit" class="button danger">Check-Out</button>
                </form>
            <?php endif ?>

            <!-- end of Thomas's work-->

            <?php /*if ($access_level < 2) : ?>
                <?php if ($event_info["completed"] == "no") : ?>
                    <button onclick="showCancelConfirmation()" class="button danger">Cancel My Sign-Up</button>
                <?php endif ?>
            <?php endif*/ ?>

            <?php if ($access_level >= 2) : ?>

                <a href="viewEventSignUps.php?id=<?php echo $id; ?>"class = "button signup">View Event Signups</a>

                <!-- Archive and Unarchive buttons by Thomas -->

                <?php if (is_archived($event_info['id']))  : ?>
                    <form method="POST" action="" onsubmit="return confirmAction('unarchive')">
                        <input type="hidden" name="unarchiving" value="1">
                        <input type="hidden" name="eventID" value="<?php echo $event_info['id']; ?>">
                        <input type="hidden" name="id" value="<?php echo $event_info['id']; ?>">
                        <button type="submit" class="button">Unarchive</button>
                    </form>

                <?php else : ?>
                    <form method="POST" action="" onsubmit="return confirmAction('archive')">
                        <input type="hidden" name="archiving" value="1">
                        <input type="hidden" name="eventID" value="<?php echo $event_info['id']; ?>">
                        <input type="hidden" name="id" value="<?php echo $event_info['id']; ?>">
                        <button type="submit" class="button">Archive</button>
                    </form>

                <?php endif ?>

                <!-- end of Thomas's work -->


                <!-- <a href="editEvent.php?id=<?= $id ?>" class="button cancel">Edit Event Details</a> -->
                

            <?php endif ?>

            <a href="calendar.php?month=<?= substr($event_info['date'], 0, 7) ?>" class="button cancel">Return to Calendar</a>
            <a href="viewAllEvents.php" class="button cancel">Return to All Events</a>

        </div>

         <!-- Share Event on Facebook Button -->
            <?php
                $page_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            ?>
            <meta property="og:image" content="https://jenniferp160.sg-host.com/images/FredSPCAlogo.png">
            <div class="fb-share-button" data-href= $page_link data-layout="" data-size=""><a target="_blank" 
                href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Flocalhost%2FfredSPCA%2FviewAllEvents.php&amp;src=sdkpreparse" 
                class="fb-xfbml-parse-ignore">Share</a>
            </div>

        <!-- Confirmation Modals -->
        <?php if ($access_level >= 2) : ?>
            <div id="delete-confirmation-wrapper" class="modal hidden">
                <div class="modal-content">
                    <p>Are you sure you want to delete this event?</p>
                    <p>This action cannot be undone.</p>
                    <form method="post" action="deleteEvent.php">
                        <input type="submit" value="Delete Event" class="button danger">
                        <input type="hidden" name="id" value="<?= $id ?>">
                    </form>
                    <button id="delete-cancel" class="button cancel">Cancel</button>
                </div>
            </div>

            <div id="complete-confirmation-wrapper" class="modal hidden">
                <div class="modal-content">
                    <p>Are you sure you want to complete this event?</p>
                    <p>This action cannot be undone.</p>
                    <form method="post" action="completeEvent.php">
                        <input type="submit" value="Archive Event" class="button">
                        <input type="hidden" name="id" value="<?= $id ?>">
                    </form>
                    <button id="complete-cancel" class="button cancel">Cancel</button>

                </div>
            </div>
            <?php endif ?>


            <?php if ($access_level < 2) : ?>
                <div id="cancel-confirmation-wrapper" class="modal hidden">
                <div class="modal-content">
                    <p>Are you sure you want to cancel your sign-up for this event?</p>
                    <p>This action cannot be undone.</p>
                   <form method="post" action="cancelEvent.php">
                        <input type="submit" value="Cancel Sign-Up" class="button danger">
                        <input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>">
                        <input type="hidden" name="user_id" value="<?= $_REQUEST['user_id'] ?>">
                    </form>
                    <button onclick="document.getElementById('cancel-confirmation-wrapper').classList.add('hidden')" id="cancel-cancel" class="button cancel">Cancel</button>
                </div>
            </div>
            <?php
        ?>
            <?php endif ?>

            

        <!-- Scripts for Modal Controls -->
        <script>
            function showDeleteConfirmation() {
                document.getElementById('delete-confirmation-wrapper').classList.remove('hidden');
            }
            function showCancelConfirmation() {
                document.getElementById('cancel-confirmation-wrapper').classList.remove('hidden');
            }
            function showCompleteConfirmation() {
                document.getElementById('complete-confirmation-wrapper').classList.remove('hidden');
            }
            document.getElementById('delete-cancel').onclick = function() {
                document.getElementById('delete-confirmation-wrapper').classList.add('hidden');
            };
            document.getElementById('cancel-cancel').onclick = function() {
                document.getElementById('cancel-confirmation-wrapper').classList.add('hidden');
            }
            document.getElementById('complete-cancel').onclick = function() {
                document.getElementById('complete-confirmation-wrapper').classList.add('hidden');
            };
        </script>
    </main>
</body>
</html>