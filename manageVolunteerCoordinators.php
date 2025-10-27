<?php
ob_start();
session_cache_expire(30);
session_start();

$loggedIn = false;
$accessLevel = 0;
$userID = null;
if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}

if ($accessLevel < 2) {
    header('Location: index.php');
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Volunteer Coordinators</title>
    <link href="css/normal_tw.css" rel="stylesheet">
    <?php require('header.php'); ?>
    <style>
        .btn {
            padding: 6px 12px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-remove {
            background-color: var(--error-color, #d4635a);
        }
        .btn-remove:hover {
            background-color: var(--accent-color, #d4af37);
        }

        .btn-add {
            background-color: var(--main-color, #e8c4b8);
        }
        .btn-add:hover {
            background-color: var(--accent-color, #d4af37);
        }

        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid var(--card-border, #e8c4b8);
            text-align: left;
        }

        th {
            background-color: var(--nav-item-active-bg, #f4ede9);
            font-weight: 600;
        }

        tr:hover {
            background-color: var(--dropdown-hover, rgba(232, 196, 184, 0.1));
        }

        .main-content-box {
            background: var(--card-bg, #ffffff);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin: 0 auto;
            margin-top: 2rem;
            width: 80%;
        }

        select, button {
            padding: 8px 10px;
            margin: 10px 5px 0 0;
            border-radius: 5px;
            border: 1px solid var(--card-border, #e8c4b8);
        }

        .success {
            color: green;
            margin-top: 10px;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

    </style>
</head>
<body>

<header class="hero-header">
    <div class="center-header">
        <h1>Manage Volunteer Coordinators</h1>
    </div>
</header>

<main>
    <div class="main-content-box">
        <?php
        require_once('database/dbGroups.php');
        require_once('database/dbMessages.php');
        require_once('database/dbPersons.php');

        $members = getVolunteerCoordinators();
        ?>
            <h3 class="text-lg font-semibold">Current Volunteer Coordinators</h3>
            <?php if (empty($members)): ?>
                <p>No members in this group.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): 
                            $fullname = htmlspecialchars($member['fullname']);
                            $email = htmlspecialchars($member['email']);
                        ?>
                            <tr>
                                <td><?= $fullname ?></td>
                                <td><?= $email ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="remove_user_id" value="<?= htmlspecialchars($member['person_id']) ?>">
                                        <button type="submit" name="remove_member" class="btn btn-remove">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php
            // REMOVE MEMBER LOGIC
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_member'])) {
                $user_id = $_POST['remove_user_id'];

                if (!empty($user_id)) {
                    $success = removeVolunteerCoordinator($user_id);
                    if ($success) {
                        echo "<p class='success'>User removed successfully from Volunteer Coordinators.</p>";
                    } else {
                        echo "<p class='error'>Failed to remove user.</p>";
                    }
                }

                header("Location: manageVolunteerCoordinators.php");
                exit();
            }

            // ADD USER SECTION
            $users_not_in_group = getNonVolunteerCoordinators();
            ?>
            <h3 class="text-lg font-semibold mt-6">Promote an Existing User to Volunteer Coordinator</h3>
            <?php if (empty($users_not_in_group)): ?>
                <p>No available users to add.</p>
            <?php else: ?>
                <form method="POST" action="manageVolunteerCoordinators.php">
                    <select name="add_user_id" required>
                        <option value="" disabled selected>Select a user to add</option>
                        <?php foreach ($users_not_in_group as $user): ?>
                            <option value="<?= htmlspecialchars($user['person_id']) ?>">
                                <?= htmlspecialchars($user['fullname']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="add_member" style="margin-bottom: 10px;" class="btn btn-add">Add</button>
                </form>
            <?php endif; ?>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {
                $user_id = $_POST['add_user_id'];

                if (!empty($user_id)) {
                    $success = addVolunteerCoordinator($user_id);
                    if ($success) {
                        /* //message user that got added
                        $title = 'You have been added as Volunteer Coordinator.';
                        $body = ' View under Groups page.';
                        send_system_message($user_id, $title, $body); */
                        echo "<p class='success'>User added successfully to Volunteer Coordinators.</p>";
                    } else {
                        echo "<p class='error'>Failed to add user.</p>";
                    }
                }
                header("Location: manageVolunteerCoordinators.php");
                exit();
            }
            ?>

        <div class="mt-6">
            <a href="groupManagement.php" class="btn btn-add">Back to Groups</a>
        </div>
    </div>
</main>
</body>
</html>
<?php ob_end_flush(); ?>
