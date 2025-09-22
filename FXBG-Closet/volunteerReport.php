<?php
    session_cache_expire(30);
    session_start();
    ini_set("display_errors",1);
    error_reporting(E_ALL);

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;

    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }

    if (!$loggedIn) {
        header('Location: login.php');
        die();
    }

    $isAdmin = $accessLevel >= 2;
    require_once('database/dbPersons.php');

    if ($isAdmin && isset($_GET['id'])) {
        require_once('include/input-validation.php');
        $args = sanitize($_GET);
        $id = $args['id'];
        $viewingSelf = $id == $userID;
    } else {
        $id = $_SESSION['_id'];
        $viewingSelf = true;
    }

    $events = get_events_attended_by($id);
    $volunteer = retrieve_person($id);
     require_once('include/output.php'); // <- defines floatPrecision()

    // Get total_hours_volunteered from DB
    $total_logged_hours = "N/A";
    $con = connect();
    $escaped_id = mysqli_real_escape_string($con, $id);
    $query = "SELECT total_hours_volunteered FROM dbpersons WHERE id = '$escaped_id'";
    $result = mysqli_query($con, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $total_logged_hours = floatPrecision($row['total_hours_volunteered'], 2);
    }
    mysqli_close($con);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Fredericksburg SPCA | Volunteer History</title>
        <link rel="stylesheet" href="css/hours-report.css">
        <style>
            .volunteer-stat {
                background-color: #f8f9fc;
                border: 2px solid #294877;
                padding: 15px 20px;
                border-radius: 10px;
                width: fit-content;
                font-family: 'Quicksand', sans-serif;
                margin: 20px 0;
            }
            .volunteer-stat h3 {
                color: #294877;
                margin-bottom: 5px;
                font-size: 20px;
            }
            .volunteer-stat p {
                font-size: 18px;
                font-weight: bold;
                margin: 0;
            }
        </style>
    </head>
    <body>
        <?php require_once('header.php'); ?>
        <h1>Volunteer History Report</h1>
        <main class="hours-report">
            <?php if (!$volunteer): ?>
                <p class="error-toast">That volunteer does not exist!</p>
            <?php elseif ($viewingSelf): ?>
                <h2 class="no-print">Your Volunteer Hours</h2>
            <?php else: ?>
                <h2 class="no-print">Hours Volunteered by <?php echo $volunteer->get_first_name() . ' ' . $volunteer->get_last_name(); ?></h2>
            <?php endif ?>

            <h2 class="print-only">Hours Volunteered by <?php echo $volunteer->get_first_name() . ' ' . $volunteer->get_last_name(); ?></h2>

            <!-- Show total logged hours from DB -->
            <div class="volunteer-stat no-print">
                <h3>Total Hours (Cumulative):</h3>
                <p><?php echo $total_logged_hours; ?> hours</p>
            </div>

            <?php if (count($events) > 0): ?>
                <div class="table-wrapper"><table class="general">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Event</th>
                            <th></th>
                            <th class="align-right">Hours</th>
                        </tr>
                    </thead>
                    <tbody class="standout">
                        <?php 
                            require_once('include/output.php');
                            $total_hours = 0;
                            foreach ($events as $event) {
                                $time = fetch_volunteering_hours($id, $event['id']);
                                $hours = ($time / 60) / 60;
                                if ($time == -1) {
                                    continue;
                                }
                                $total_hours += $hours;
                                $date = date('m/d/Y', strtotime($event['date']));
                                echo '<tr>
                                    <td>' . $date . '</td>
                                    <td>' . $event["name"] . '</td>
                                    <td></td>
                                    <td class="align-right">' . floatPrecision($hours, 2) . '</td>
                                </tr>';
                            }
                            echo "<tr class='total-hours'><td></td><td></td><td class='total-hours'>Total Hours</td><td class='align-right'>" . floatPrecision($total_hours, 2) . "</td></tr>";
                        ?>
                    </tbody></table>
                    <p class="print-only">I hereby certify that this volunteer has contributed the above volunteer hours to the Fredericksburg SPCA organization.</p>
                    <table id="signature-table" class="print-only">
                        <tbody>
                            <tr><td>Admin Signature:  ______________________________________ Date: <?php echo date('m/d/Y') ?></td></tr>
                            <tr><td>Print Admin Name: _____________________________________</td></tr>
                        </tbody>
                    </table></div>
                    <button class="no-print" onclick="window.print()" style="margin-bottom: -.5rem">Print</button>
            <?php endif; ?> <!-- ✅ ADD THIS LINE -->
            <?php if (count($events) === 0): ?>
            <p>This volunteer has logged a total of <?php echo $total_logged_hours; ?> hours, but no event-specific records are available.</p>
            <?php endif; ?>
                <?php if ($viewingSelf): ?>
                    <a class="button cancel no-print" href="viewProfile.php">Return to Profile</a>
                <?php else: ?>
                    <a class="button cancel no-print" href="viewProfile.php?id=<?php echo htmlspecialchars($_GET['id']) ?>">Return to Profile</a>
                <?php endif ?>
        </main>
    </body>
</html>
