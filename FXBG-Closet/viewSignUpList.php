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

$args = sanitize($_GET);
$id = $args['id'] ?? null;

if (!$id) {
    echo 'Event ID is missing.';
    die();
}

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

    if (remove_user_from_event($event_id, $user_id)) {
        $remove_success = "User $user_id was successfully removed.";
    } else {
        $remove_error = "Failed to remove user $user_id.";
    }
}

// Fetch event details
$event_info = fetch_event_by_id($id);
if (!$event_info) {
    echo 'Invalid event ID.';
    die();
}

// Fetch signups for the event
$signups = fetch_event_signups($id);
$access_level = $_SESSION['access_level'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('universal.inc'); ?>
    <title>View Event Details | <?php echo htmlspecialchars($event_info['name']); ?></title>
    <link rel="stylesheet" href="css/messages.css" />
</head>
<body>
    <?php require_once('header.php'); ?>

    <h1>View Sign-Up List</h1>
    <main class="general">
        <h2><?php echo htmlspecialchars($event_info['name']); ?></h2>

        <?php if (isset($remove_success)): ?>
            <p class="success"><?php echo htmlspecialchars($remove_success); ?></p>
        <?php elseif (isset($remove_error)): ?>
            <p class="error"><?php echo htmlspecialchars($remove_error); ?></p>
        <?php endif; ?>

        <?php if (count($signups) > 0): ?>
            <div class="table-wrapper">
                <table class="general">
                    <thead>
                        <tr>
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
                        <?php foreach ($signups as $signup): 
                            $user_info = retrieve_person($signup['userID']);
                            $position_label = $signup['position'] === 'p' ? 'Participant' : ($signup['position'] === 'v' ? 'Volunteer' : 'Unknown');
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user_info->get_first_name()); ?></td>
                                <td><?php echo htmlspecialchars($user_info->get_last_name()); ?></td>
                                <td><a href="viewProfile.php?id=<?php echo urlencode($signup['userID']); ?>"><?php echo htmlspecialchars($signup['userID']); ?></a></td>
                                <td><?php echo htmlspecialchars($position_label); ?></td>
                                <?php if ($access_level >= 2): ?>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($id); ?>">
                                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($signup['userID']); ?>">
                                            <button type="submit" class="button danger" onclick="return confirm('Are you sure you want to remove this user?');">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No users have signed up for this event yet.</p>
        <?php endif; ?>

        <a class="button cancel" href="index.php">Return to Dashboard</a>
    </main>
</body>
</html>
