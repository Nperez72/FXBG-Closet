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

if ($accessLevel < 4) {
    header('Location: index.php');
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Board Members</title>
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
            color: var(--button-text, #363434);
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

        .inline-form {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .success {
            color: green;
            margin-top: 10px;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .create-header {
            display: flex;
            justify-content: space-between;
            justify-content: flex-start;
            gap: 12px;
            margin-top: 25px;
            margin-bottom: 15px;
        }

    </style>
</head>
<body>

<header class="hero-header">
    <div class="center-header">
        <h1>Manage Board Members</h1>
    </div>
</header>

<main>
    <div class="main-content-box">
        <?php
        require_once('database/dbGroups.php');
        require_once('database/dbMessages.php');
        require_once('database/dbPersons.php');

        $members = getBoardMembers();
        ?>
            <h3 class="text-lg font-semibold">Current Board Members</h3>
            <?php if (empty($members)) : ?>
                <p>No members in this group.</p>
            <?php else : ?>
                <table style="margin-bottom: 25px">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member) :
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
                    $success = removeBoardMember($user_id);
                    if ($success) {
                        echo "<p class='success'>User removed successfully from Board Members.</p>";
                    } else {
                        echo "<p class='error'>Failed to remove user.</p>";
                    }
                }

                header("Location: manageBoardMembers.php");
                exit();
            }
            ?>

            <!-- ADD USER SECTION -->
            <div class="create-header">
                <h3 class="text-lg font-semibold mt-6">Create a New Board Member: </h3>
                <a href="createBoardMem.php" class="btn btn-add">Create</a>
            </div>
            

        <div class="mt-6">
            <a href="groupManagement.php" class="btn btn-add">Back to Groups</a>
        </div>
    </div>
</main>
</body>
</html>
<?php ob_end_flush(); ?>
