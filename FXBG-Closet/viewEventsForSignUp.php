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
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <link rel="stylesheet" href="css/messages.css"></link>
        <script src="js/messages.js"></script>
        <title>Fredericksburg SPCA Volunteer System | Events Open for Sign-Up</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Events</h1>
        <main class="general">
            <h2>Open for Sign-Up</h2>
            <?php 
                require_once('database/dbMessages.php');
                $messages = get_user_messages($userID);
                if (count($messages) > 0): ?>
                <div class="table-wrapper">
                    <table class="general">
                        <thead>
                            <tr>
                                <th style="width:1px">Restricted</th>
                                <th>Title</th>
                                <th style="width:1px">Date</th>
                                <th style="width:1px"></th>
                            </tr>
                        </thead>
                        <tbody class="standout">
                            <?php 
                                require_once('database/dbPersons.php');
                                require_once('include/output.php');
                                $id_to_name_hash = [];
                                foreach ($messages as $message) {
                                    $sender = $message['senderID'];
                                    if (isset($id_to_name_hash[$sender])) {
                                        $sender = $id_to_name_hash[$sender];
                                    } else {
                                        $lookup = get_name_from_id($sender);
                                        $id_to_name_hash[$sender] = $lookup;
                                        $sender = $lookup;
                                    }
                                    $messageID = $message['id'];
                                    $title = $message['title'];
                                    $timePacked = $message['time'];
                                    $pieces = explode('-', $timePacked);
                                    $year = $pieces[0];
                                    $month = $pieces[1];
                                    $day = $pieces[2];
                                    $time = time24hto12h($pieces[3]);
                                    $class = 'message';
                                    if (!$message['wasRead']) {
                                        $class .= ' unread';
                                    }
                                    echo "
                                        <tr class='$class' data-message-id='$messageID'>
                                            <td>$sender</td>
                                            <td>$title</td>
                                            <td>$month/$day/$year $time</td>
                                            <td> <a class='button sign-up' href='eventSignUp.php'>Sign Up</a> </td>
                                        </tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-events standout">There are currently no events available for sign-up.</p>
            <?php endif ?>
            <a class="button cancel" href="index.php">Return to Dashboard</a>
        </main>
    </body>
</html>