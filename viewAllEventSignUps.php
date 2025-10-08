<?php
session_cache_expire(30);
session_start();

if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
    header('Location: login.php');
    die();
}

require_once('include/input-validation.php');
require_once('database/dbEvents.php');
require_once('database/dbPersons.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

/*$args = sanitize($_GET);
$id = $args['id'] ?? null;*/

// Handle user removal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? null;
    $user_id = $_POST['user_id'] ?? null;

    if (!$event_id) {
        echo 'Event ID is missing.';
        die();
    }

    if (!$user_id) {
        echo 'User ID is missing.';
        die();
    }
}

// Fetch event details
$events = fetch_all_pending();

$access_level = $_SESSION['access_level']; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('universal.inc'); ?>
    <link rel="stylesheet" href="css/event.css" type="text/css" />

    <title>View Event Details | <?php /*echo htmlspecialchars($event_info['name']); */ ?></title>
    <link rel="stylesheet" href="css/messages.css" />

    <script>
        function showResolutionConfirmation(ei, ui) {
            document.getElementById('resolution-confirmation-wrapper-' + ei + '-' + ui).classList.remove('hidden');
            return false;
        }
        function showApprove(ei, ui) {
            document.getElementById('resolution-confirmation-wrapper-' + ei + '-' + ui).classList.add('hidden');
            document.getElementById('reject-confirmation-wrapper-' + ei + '-' + ui).classList.add('hidden');
            document.getElementById('approve-confirmation-wrapper-' + ei + '-' + ui).classList.remove('hidden');
            return false;
        }
        function showReject(ei, ui) {
            document.getElementById('resolution-confirmation-wrapper-' + ei + '-' + ui).classList.add('hidden');
            document.getElementById('approve-confirmation-wrapper-' + ei + '-' + ui).classList.add('hidden');
            document.getElementById('reject-confirmation-wrapper-' + ei + '-' + ui).classList.remove('hidden');
            return false;
        }
    </script>

</head>

<body>
    <?php require_once('header.php'); ?>

    <h1>View Pending Sign-Ups List</h1>
    <?php if (isset($_GET['pendingSignupSuccess'])): ?>
        <div class="happy-toast">Sign-up request resolved successfully.</div>
    <?php endif ?>

    <main class="general">

        <?php $event_names = all_pending_names();
        $event_ids = all_pending_ids(); ?>

        <p>
            <?php if (sizeof($event_names) === 0):
                echo "There are 0 pending signups awaiting resolution.";
                ?>
            <?php elseif (sizeof($event_names) === 1):
                echo "There is 1 pending signup awaiting resolution"; ?>
            <?php else: ?>
                <?php echo "There are " . htmlspecialchars(string: sizeof($event_names)) . " pending signups awaiting resolution"; ?>
            <?php endif; ?>
        </p>

        <?php if (count(value: $event_names) > 0): ?>
            <div class="table-wrapper">
                <table class="general">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>User ID</th>
                            <th>Position</th>
                            <?php if ($access_level >= 2): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($x = 0; $x < sizeof($event_names); $x++): ?>
                            <h2><?php $name = $event_names[$x]; ?></h2>

                            <?php
                            $event = $events[$x];
                            $event_id = $event_ids[$x];

                            //foreach ($events as $event): 
                            $user_info = retrieve_person($event['username']);
                            $position_label = $event['role'] === 'p' ? 'Participant' : ($event['role'] === 'v' ? 'Volunteer' : 'Unknown');
                            ?>
                            <tr>
                                <td><a
                                        href="event.php?id=<?php echo urlencode($event['eventname']); ?>"><?php echo htmlspecialchars($name['name']); ?></a>
                                </td>
                                <td><?php echo htmlspecialchars($user_info->get_first_name()); ?></td>
                                <td><?php echo htmlspecialchars($user_info->get_last_name()); ?></td>
                                <td><a
                                        href="viewProfile.php?id=<?php echo urlencode($user_info->get_id()); ?>"><?php echo htmlspecialchars($user_info->get_id()); ?></a>
                                </td>
                                <td><?php echo htmlspecialchars($position_label); ?></td>
                                <?php if ($access_level >= 2): ?>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="event_id" value="<?= $event_id['eventname']; ?>">
                                            <input type="hidden" name="user_id"
                                                value="<?php echo htmlspecialchars($event['username']); ?>">
                                        </form>
                                        <?php $ei = $event['eventname'];
                                        $ui = $event['username']; ?>
                                        <button onclick="showResolutionConfirmation(<?=$ei?>, '<?=$ui?>')" class="button">Resolve</button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <div id="resolution-confirmation-wrapper-<?= $event_id['eventname'] ?>-<?= $event['username'] ?>" class="modal-content hidden" style = "margin:auto">
                                <div class="modal-content">
                                <?php $en = $event_id['eventname'];
                                $un = $event['username']; ?>
                                    <p>Would you like to approve or reject this sign-up request?</p>
                                    <button onclick="showApprove(<?=$en?>, '<?=$un?>')" class="button success">Approve</button>
                                    <button onclick="showReject(<?=$en?>, '<?=$un?>')" class="button danger">Reject</button>
                                    <button
                                        onclick="document.getElementById('resolution-confirmation-wrapper-<?= $event_id['eventname'] ?>-<?= $event['username'] ?>').classList.add('hidden')"
                                        id="cancel-cancel" class="button cancel">Cancel</button>
                                </div>
                            </div>
                            <div id="approve-confirmation-wrapper-<?= $event_id['eventname'] ?>-<?= $event['username'] ?>" class="modal-content hidden" style = "margin:auto">
                                <div class="modal-content">
                                    <p>Are you sure you want to approve this sign-up request?</p>
                                    <p>This action cannot be undone</p>
                                    <form method="post" action="fromPendingApproveSignup.php">
                                        <input type="submit" value="Approve" class="button success">
                                        <input type="hidden" name="id" value="<?= $event_id['eventname'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $event['username'] ?>">
                                        <input type="hidden" name="position" value="<?= $event['role'] ?>">
                                        <input type="hidden" name="notes" value="<?= $event['notes'] ?>">
                                    </form>
                                    <button
                                        onclick="document.getElementById('approve-confirmation-wrapper-<?= $event_id['eventname'] ?>-<?= $event['username'] ?>').classList.add('hidden')"
                                        id="cancel-cancel" class="button cancel">Cancel</button>
                                </div>
                            </div>
                            <div id="reject-confirmation-wrapper-<?= $event_id['eventname'] ?>-<?= $event['username'] ?>" class="modal-content hidden" style = "margin:auto">
                                <div class="modal-content">
                                    <p>Are you sure you want to reject this sign-up request?</p>
                                    <p>This action cannot be undone </p>
                                    <form method="post" action="fromPendingRejectSignup.php">
                                        <input type="submit" value="Reject" class="button danger">
                                        <input type="hidden" name="id" value="<?= $event_id['eventname'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $event['username'] ?>">
                                        <input type="hidden" name="position" value="<?= $event['role'] ?>">
                                        <input type="hidden" name="notes" value="<?= $event['notes'] ?>">
                                    </form>
                                    <button
                                        onclick="document.getElementById('reject-confirmation-wrapper-<?= $event_id['eventname'] ?>-<?= $event['username'] ?>').classList.add('hidden')"
                                        id="cancel-cancel" class="button cancel">Cancel</button>
                                </div>
                            </div>
                        <?php endfor ?>
                    </tbody>
                </table>
            </div>
        <?php endif ?>

        <a class="button cancel" href="index.php">Return to Dashboard</a>
    </main>


</body>

</html>