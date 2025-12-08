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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FXBG Closet | Manage Volunteer Coordinators</title>
    <script src="js/theme-presets-init.js"></script>
    <link href="css/normal_tw.css" rel="stylesheet">
    <link rel="stylesheet" href="css/accessibility-settings.css">
    <link rel="stylesheet" href="css/theme-presets.css">
    <?php require('header.php'); ?>
    <style>
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-remove {
            background-color: #dc2626;
        }
        .btn-remove:hover {
            background-color: #b91c1c;
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
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin: 0 auto;
            margin-top: 2rem;
            width: 90%;
            max-width: 1200px;
        }

        select, button {
            padding: 10px 14px;
            margin: 10px 5px 0 0;
            border-radius: 8px;
            border: 2px solid var(--card-border, #e8c4b8);
            background: white;
            color: var(--text-color, #363434);
            font-size: 16px;
        }
        
        select:focus, button:focus {
            outline: 2px solid var(--accent-color, #d4af37);
            outline-offset: 2px;
        }

        .inline-form {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        
        .inline-form select {
            flex: 1;
            min-width: 250px;
        }

        .success {
            color: #16a34a;
            background: #dcfce7;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 4px solid #16a34a;
        }

        .error {
            color: #dc2626;
            background: #fee2e2;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 4px solid #dc2626;
        }

        .create-header {
            display: flex;
            justify-content: space-between;
            justify-content: flex-start;
            gap: 12px;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        /* Mobile responsive improvements */
        @media (max-width: 768px) {
            .hero-header {
                height: calc(var(--spacing) * 25) !important;
            }
            
            main {
                padding-inline: calc(var(--spacing) * 2) !important;
            }
            
            .main-content-box {
                width: 100%;
                padding: 1rem !important;
                margin-top: 1rem;
            }
            
            h1 {
                font-size: 1.5rem !important;
                padding: 1rem !important;
            }
            
            h2, h3 {
                font-size: 1.25rem !important;
            }
            
            table {
                font-size: 14px;
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            th, td {
                padding: 8px !important;
                min-width: 100px;
            }
            
            th:first-child, td:first-child {
                position: sticky;
                left: 0;
                background: var(--card-bg, #ffffff);
                z-index: 1;
            }
            
            th:first-child {
                background: var(--nav-item-active-bg, #f4ede9);
            }
            
            .btn {
                padding: 6px 12px;
                font-size: 14px;
            }
            
            .inline-form {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }
            
            .inline-form select {
                width: 100%;
                min-width: unset;
                margin-bottom: 10px;
            }
            
            .inline-form button {
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .main-content-box {
                padding: 0.75rem !important;
                border-radius: 8px;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 6px !important;
                min-width: 80px;
            }
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

            ?>

            <!-- ADD USER SECTION -->
            <div class="create-header">
                <h3 class="text-lg font-semibold mt-6">Create a New Volunteer Coordinator: </h3>
                <a href="createCoord.php" class="btn btn-add">Create</a>
            </div>

        <div class="mt-6">
            <a href="groupManagement.php" class="btn btn-add">Back to Groups</a>
        </div>
    </div>
</main>
<script src="js/accessibility-settings.js"></script>
<script src="js/theme-presets.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>
