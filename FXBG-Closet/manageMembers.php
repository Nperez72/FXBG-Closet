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
    <title>Manage Group Members</title>
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
            background-color: #f44336;
        }
        .btn-remove:hover {
            background-color: #e53935;
        }

        .btn-add {
            background-color: #4CAF50;
        }
        .btn-add:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .main-content-box {
            background: #ffffff;
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
            border: 1px solid #ccc;
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
        <h1>Manage Group Members</h1>
    </div>
</header>

<main>
    <div class="main-content-box">
        <?php
        require_once('database/dbGroups.php');
        require_once('database/dbMessages.php');

        $selected_group = $_GET['group_name'] ?? '';

        if ($selected_group):
            echo "<h2 class='text-xl font-bold mb-4'>Managing: " . htmlspecialchars($selected_group) . "</h2>";

            $members = get_users_in_group($selected_group);
        ?>
            <h3 class="text-lg font-semibold">Current Members</h3>
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
                            $full_name = htmlspecialchars($member['first_name']) . " " . htmlspecialchars($member['last_name'] ?? '');
                            $email = htmlspecialchars($member['email']);
                        ?>
                            <tr>
                                <td><?= $full_name ?></td>
                                <td><?= $email ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="remove_user_id" value="<?= htmlspecialchars($member['id']) ?>">
                                        <input type="hidden" name="remove_group_name" value="<?= htmlspecialchars($selected_group) ?>">
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
                $remove_group_name = $_POST['remove_group_name'];
                require_once('database/dbPersons.php');

                if (!empty($user_id) && !empty($remove_group_name)) {
                    $success = remove_user_from_group($user_id, $remove_group_name);
                    if ($success) {
                        echo "<p class='success'>User removed successfully from $remove_group_name.</p>";
                    } else {
                        echo "<p class='error'>Failed to remove user.</p>";
                    }
                }

                header("Location: manageMembers.php?group_name=" . urlencode($remove_group_name));
                exit();
            }

            // ADD USER SECTION
            $users_not_in_group = get_users_not_in_group($selected_group);
            ?>
            <h3 class="text-lg font-semibold mt-6">Add a User to this Group</h3>
            <?php if (empty($users_not_in_group)): ?>
                <p>No available users to add.</p>
            <?php else: ?>
                <form method="POST" action="manageMembers.php?group_name=<?= urlencode($selected_group) ?>">
                    <select name="add_user_id" required>
                        <option value="" disabled selected>Select a user to add</option>
                        <?php foreach ($users_not_in_group as $user): ?>
                            <option value="<?= htmlspecialchars($user['id']) ?>">
                                <?= htmlspecialchars($user['first_name']) . " " . htmlspecialchars($user['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="add_group_name" value="<?= htmlspecialchars($selected_group) ?>">
                    <button type="submit" name="add_member" style="margin-bottom: 10px;" class="btn btn-add">Add</button>
                </form>
            <?php endif; ?>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {
                $user_id = $_POST['add_user_id'];
                $group_name = $_POST['add_group_name'];
                require_once('database/dbPersons.php');

                if (!empty($user_id) && !empty($group_name)) {
                    $success = add_user_to_group($user_id, $group_name);
                    if ($success) {
                        //message user that got added
                        $title = 'You have been added to a group. View under Groups page.';
                        $body = 'You have been added to ' . $group_name;
                        send_system_message($user_id, $title, $body);
                        echo "<p class='success'>User added successfully to $group_name.</p>";
                    } else {
                        echo "<p class='error'>Failed to add user.</p>";
                    }
                }
                header("Location: manageMembers.php?group_name=" . urlencode($group_name));
                exit();
            }
            ?>
        <?php endif; ?>

        <div class="mt-6">
            <a href="showGroups.php" class="btn btn-add">Back to Groups</a>
        </div>
    </div>
</main>
</body>
</html>
<?php ob_end_flush(); ?>
