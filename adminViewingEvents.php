<?php
    // Template for new VMS pages. Base your new page on this one

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
    include 'database/dbEvents.php';
    //include 'domain/Event.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <link rel="stylesheet" href="css/messages.css"></link>
        <script src="js/messages.js"></script>
        <title>Fredericksburg SPCA Volunteer System | Events</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <?php require_once('database/dbEvents.php');?>
        <h1>Events</h1>
        <main class="general">
            <?php 
                //require_once('database/dbMessages.php');
                //$messages = get_user_messages($userID);
                //require_once('database/dbevents.php');
                //require_once('domain/Event.php');
                $events = get_all_events();
                if (sizeof(get_all_events())): ?>
                <div class="table-wrapper">
                    <table class="general">
                        <thead>
                            <tr>
                                <th style="width:1px">Training Required</th>
                                <th>Title</th>
                                <th style="width:1px">Date</th>
                                <th style="width:1px"></th>
                            </tr>
                        </thead>
                        <tbody class="standout">
                            <?php 
                                #require_once('database/dbPersons.php');
                                #require_once('include/output.php');
                                #$id_to_name_hash = [];
                                foreach ($events as $event) {
                                    $eventID = $event->getID();
                                    $title = $event->getName();
                                    $date = $event->getDate();
                                    $startTime = $event->getStartTime();
                                    $endTime = $event->getEndTime();
                                    $description = $event->getDescription();
                                    $capacity = $event->getCapacity();
                                    $completed = $event->getCompleted();
                                    $restricted_signup = $event->getRestrictedSignup();
                                    $training_level_required = $event->getTrainingLevelRequired();
                                     if ($training_level_required == NULL) {
                                         $training_level_required = "None";
                                     }
                                    if($accessLevel < 3) {
                                        echo "
                                        <tr data-event-id='$eventID'>
                                            <td>$training_level_required</td>
                                            <td><a href='event.php?id=$eventID'>$title</a></td> <!-- Link updated here -->
                                            <td>$date</td>
                                            <td><a class='button sign-up' href='eventSignUp.php'>Sign Up</a></td>
                                        </tr>";
                                    } else {
                                        echo "
                                        <tr data-event-id='$eventID'>
                                            <td>$training_level_required</td>
                                            <td><a href='event.php?id=$eventID'>$title</a></td> <!-- Link updated here -->
                                            <td>$date</td>
                                            <td></td>
                                        </tr>";
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="no-events standout">There are currently no events available to view.<a class="button add" href="addEvent.php">Create a New Event</a> </p>
            <?php endif ?>
            <a class="button cancel" href="index.php">Return to Dashboard</a>
        </main>
    </body>
</html>