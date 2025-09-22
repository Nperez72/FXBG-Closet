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

// Restrict admins from accessing the volunteer view
if ($accessLevel > 1) {
    header('Location: index.php');
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Group Members</title>
    <link href="css/normal_tw.css" rel="stylesheet">
    <?php require('header.php'); ?>
    <style>
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

        .btn {
            padding: 8px 16px;
            margin-top: 10px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            background-color: #3b82f6;
            color: white;
            display: inline-block;
        }

        .btn:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>

<header class="hero-header">
    <div class="center-header">
        <h1>View Group Members</h1>
    </div>
</header>

<main>
    <div class="main-content-box">
        <?php
        require_once('database/dbGroups.php');

        $selected_group = $_GET['group_name'] ?? '';

        if ($selected_group):
            echo "<h2 class='text-xl font-bold mb-4'>Group: " . htmlspecialchars($selected_group) . "</h2>";

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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <div class="mt-6">
            <a href="volunteerViewGroup.php" class="btn">Back to Your Groups</a>
            <a href="index.php" class="btn">Return to Dashboard</a>
        </div>
    </div>
</main>

</body>
</html>
