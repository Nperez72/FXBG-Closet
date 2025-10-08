<?php
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

// admin-only access
if ($accessLevel < 2) {
    header('Location: index.php');
    die();
}

require_once 'database/dbGroups.php';
require_once 'domain/Groups.php';

$groups = get_all_groups();
?>

<!DOCTYPE html>
<html lang="en">
<head>
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
            <h1>All Groups</h1>
        </div>
    </header>

    <main>
        <div class="main-content-box w-[80%] p-6">
            <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

            <table>
                <thead>
                    <tr>
                        <th>Group Name</th>
                        <th>Color Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($groups)): ?>
                        <?php foreach ($groups as $group): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($group->get_group_name()); ?></td>
                                <td><?php echo ucfirst($group->get_color_level()); ?></td>
                                <td>
                                    <a href="manageMembers.php?group_name=<?php echo urlencode($group->get_group_name()); ?>" class="blue-button">Manage Members</a>
                                    <a href="deleteGroup.php?group_name=<?php echo urlencode($group->get_group_name()); ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this group?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">No groups found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="text-center mt-6">
                <a href="createGroup.php" class="blue-button">Create a New Group</a>
            </div>

        </div>
        <div class="text-center mt-4">
                <a href="groupManagement.php" class="return-button">Back to Groups</a>
            </div>

        <div class="info-section">
            <div class="blue-div"></div>
            <p class="info-text">
                Manage all volunteer and participant groups here. You can create, delete, and assign members to any group as needed.
            </p>
        </div>
    </main>
</body>
</html>
