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
    // admin-only access
    if ($accessLevel < 2) {
        header('Location: index.php');
        die();
    }

    /* Fetch events for dropdown options */
    require_once 'database/dbEvents.php';
    require_once 'domain/Event.php';
    // $events = get_all_events_sorted_by_date_not_archived();
    $events = get_all_events_sorted_by_date_and_archived();

    $today = new DateTime(); // Current date

    $oneWeekAgo = (clone $today)->modify('-7 days')->format('Y-m-d');
    $oneMonthFromNow = (clone $today)->modify('+30 days')->format('Y-m-d');

    $filteredEvents = [];

    /* add events within next month and previous week to list */
    foreach ($events as $event) {
        $eventDate = (new DateTime($event->getDate()))->format('Y-m-d');
        if ($eventDate === $oneMonthFromNow) {
            $filteredEvents[] = $event;
        } elseif ($eventDate === $oneWeekAgo) {
            $filteredEvents[] = $event;
        }
    }


?>

<!DOCTYPE html>
<html>
    <head>
        <title>Fredericksburg SPCA | Volunteer Email List</title>
  	<link href="css/normal_tw.css" rel="stylesheet">

<!-- BANDAID FIX FOR HEADER BEING WEIRD -->
<?php
$tailwind_mode = true;
require_once('header.php');
?>
<style>
        .date-box {
            background: #274471;
            padding: 7px 30px;
            border-radius: 50px;
            box-shadow: -4px 4px 4px rgba(0, 0, 0, 0.25) inset;
            color: white;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
        }   
        .dropdown {
            padding-right: 50px;
        }   

</style>
<!-- BANDAID END, REMOVE ONCE SOME GENIUS FIXES -->

    </head>
<body>

<header class="hero-header">
    <div class="center-header">
        <h1>Volunteer/Participant Search</h1>
    </div>
</header>

<main>
    <div class="main-content-box w-[80%] p-8">
        <div class="text-center mb-8">
            <h2>Generate an email list of volunteers or participants</h2>
            <p class="sub-text">Use filters below to find people and create a mailing list.</p>
        </div>

        <form id="person-search" method="get" class="space-y-6">

        <?php
            if (isset($_GET['role']) || isset($_GET['status']) || isset($_GET['event']) || isset($_GET['group_name']) || isset($_GET['event_name'])) {
                require_once('include/input-validation.php');
                require_once('database/dbPersons.php');
                require_once('domain/Person.php');
                require_once('database/dbGroups.php');
                $args = sanitize($_GET);
                $role = $args['role'];
                $status = $args['status'];
                $event = $args['event'];
                $group_name = $args['group'];


                if (!valueConstrainedTo($role, ['admin', 'participant', 'superadmin', 'volunteer', '']) ||
                    !valueConstrainedTo($status, ['Active', 'Inactive', ''])) {
                    echo '<div class="error-block">The system did not understand your request.</div>';
                } else {
                    echo "<h3>Search Results</h3>";

                    $persons = [];
                    $filteredPersons = [];
                    /* fetch persons who match the give role and status criteria */
                    if ($role  == '' && $status == '') { // if looking for all people, or by event / group, start with everyone, then filter
                        // name, id, phone, zip, type, status
                        $persons_active = find_users('', '', '', '', $role, 'Active');
                        $persons_inactive = find_users('', '', '', '', $role, 'Inactive');
                        $persons = array_merge($persons_active, $persons_inactive);

                    } else {
                        $persons = find_users('', '', '', '', $role, $status);
                        $filteredPersons = array_merge($persons, $filteredPersons);
                    }

                    /* if group was set, get members of group */
                    if ($group_name != "") {
                        $members = get_users_in_group($group_name);

                        /* move only people of group */
                        foreach ($persons as $person) { // person objects
                            foreach ($members as $member) { // associate arrays with some person data
                                if ($person->get_id() == $member["id"]) {
                                    $filteredPersons[] = $person;
                                    break; // escape to next $person
                                }
                            }
                        }
                    }


                    require_once('include/output.php');

                    if (count($persons) > 0) {
                        echo '
                        <div class="overflow-x-auto">
                            <table>
                                <thead class="bg-blue-400">
                                    <tr>
                                        <th>First</th>
                                        <th>Last</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Archive Status</th>
                                    </tr>
                                </thead>
                                <tbody>';
                        // Create Mailing List String
                        $mailingList = '';
                        $notFirst = false;
                        foreach ($filteredPersons as $person) {

                            if ($notFirst) {
                                $mailingList .= ', ';
                            } else {
                                $notFirst = true;
                            }
                            $mailingList .= $person->get_email();
                            echo '
                                <tr>
                                    <td>' . $person->get_first_name() . '</td>
                                    <td>' . $person->get_last_name() . '</td>
                                    <td>' . $person->get_id() . '</td>
                                    <td><a href="mailto:' . $person->get_email() . '" class="text-blue-700 underline">' . $person->get_email() . '</a></td>
                                    <td>' . ucfirst($person->get_type()) . '</td>
                                    <td>' . ucfirst($person->get_status()) . '</td>
                                </tr>';
                        }
                        echo '
                                </tbody>
                            </table>
                        </div>';

                        echo '
                        <div class="mt-4">
                            <label>Result Mailing List:</label>
                            <p class="text-gray-700 break-words">' . $mailingList . '</p>
                        </div>';
                    } else {
                        echo '<div class="error-block">Your search returned no results.</div>';
                    }
                    echo '<h3>Search Again</h3>';
                }
            }
        ?>

            <div>
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="">Any</option>
                    <option value="volunteer" <?php if (isset($role) && $role == 'volunteer') echo 'selected' ?>>Volunteer</option>
                    <option value="participant" <?php if (isset($role) && $role == 'participant') echo 'selected' ?>>Participant</option>
                </select>
            </div>

            <div>
                <label for="status">Archive Status</label>
                <select id="status" name="status">
                    <option value="">Any</option>
                    <option value="Active" <?php if (isset($status) && $status == 'Active') echo 'selected' ?>>Active</option>
                    <option value="Inactive" <?php if (isset($status) && $status == 'Inactive') echo 'selected' ?>>Archived</option>
                </select>
            </div>

            <div>
                <label for="event">Event</label>
                <select id="event" name="event">
                    <option value="">Any</option>
                    <?php foreach ($filteredEvents as $event) {
                        echo '<option value="' . $event->get_id() . '">' . htmlspecialchars($event->get_name()) . ' - ' . (new DateTime($event->getDate()))->format('M d, Y') . '</option>';
                    } ?>
                </select>
            </div>

            <div>
                <label for="group">Group</label>
                <select id="group" name="group">
                    <option value="">Any</option>
                    <?php foreach ($groups as $group) {
                        echo '<option value="' . $group->get_group_name() . '">' . htmlspecialchars($group->get_group_name()) . '</option>';
                    } ?>
                </select>
            </div>

            <div class="text-center pt-4">
                <input type="submit" value="Search" class="blue-button">
            </div>
        </form>
    </div>

    <div class="text-center mt-6">
        <a href="index.php" class="return-button">Return to Dashboard</a>
    </div>

    <div class="info-section">
        <div class="blue-div"></div>
        <p class="info-text">
            Use this tool to filter and search for volunteers or participants by their role, event involvement, and status. Mailing list support is built in.
        </p>
    </div>
</main>
</body>
</html>
