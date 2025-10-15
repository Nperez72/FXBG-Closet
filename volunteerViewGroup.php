<?php
session_cache_expire(30);
session_start();
require 'database/dbGroups.php';

$loggedIn = false;
$accessLevel = 0;
$userID = null;
if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}
// Redirect admins away
if ($accessLevel > 1) {
    header('Location: index.php');
    die();
}

$groups = get_groups_from_user($userID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="css/normal_tw.css" rel="stylesheet">
    <?php 
        $tailwind_mode = true;
        require('header.php'); 
    ?>
    <style>
        .btn-edit {
            padding: 5px 10px;
            margin: 0 5px;
            text-decoration: none;
            color: white;
            border-radius: 3px;
            background-color: var(--main-color, #e8c4b8);
        }
        .btn-edit:hover {
            background-color: var(--accent-color, #d4af37);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-family: Arial, sans-serif;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: var(--nav-item-active-bg, #f4ede9);
        }
        tr:nth-child(even) {
            background-color: var(--bg-color, #f9f6f3);
        }
        tr:hover {
            background-color: var(--dropdown-hover, rgba(232, 196, 184, 0.1));
        }
        .btn {
            padding: 8px 16px;
            margin-top: 10px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            background-color: var(--main-color, #e8c4b8);
            color: white;
            display: inline-block;
        }
        .btn:hover {
            background-color: var(--accent-color, #d4af37);
        }
    </style>
    <title>STEPVA | Your Groups</title>
</head>
<body>
    <header class="hero-header">
        <div class="center-header">
            <h1>Your Groups</h1>
        </div>
    </header>

    <main>
        <div class="main-content-box w-[80%] p-8">
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
                                <td><?php echo htmlspecialchars($group['group_name']); ?></td>
                                <td><?php echo ucfirst($group['color_level']); ?></td>
                                <td>
                                    <a href="volunteerViewGroupMembers.php?group_name=<?php echo urlencode($group['group_name']); ?>" class="btn-edit">View Members</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No groups found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="text-center mt-4">
                <a href="index.php" class="btn">Back to Dashboard</a>
            </div>
        </div>

        <div class="info-section">
            <div class="blue-div"></div>
            <p class="info-text">
                View the groups you're currently assigned to. Click "View Members" to see who else is in each group.
            </p>
        </div>
    </main>
</body>
</html>
