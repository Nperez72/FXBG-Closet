<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    require_once('include/api.php');

    ini_set("display_errors", 1);
    error_reporting(E_ALL);

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}

    require_once('database/dbAccounts.php');
    $isAdmin = false;
    $accounts = array();
if ($loggedIn) {
    $accountType = get_account_type($userID);
    // 0: volunteer, 1: coordinator/board memeber, 2: admin
    if ($accountType !== null && $accountType >= 2) {
        $isAdmin = true;
    }
}

if (!$isAdmin) {
    header('Location: login.php');
    die();
}

if ($isAdmin) {
    $accounts = get_all_accounts();
}

if (!$loggedIn) {
    header('Location: login.php');
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once('include/input-validation.php');



    if (!wereRequiredFieldsSubmitted($_POST, array('new-password', 'new-username', 'new-password-reenter', 'type'))) {
        echo "Args missing";
        die();
    }

    $username = $_POST['new-username'];
    $password = $_POST['new-password'];
    $password_reenter = $_POST['new-password-reenter'];
    $type = $_POST['type'];
    $securePassword = isSecurePassword($password);



    if (!$securePassword) {
        $error2 = true; // password isn't secure
    } elseif (!$password == $password_reenter) {
        $error1 = true;
    } else {
        $result = create_account($username, $password, $type);

        if ($result === 'duplicate') {
            $error3 = true; // username already exists
        } elseif (!$result) {
            echo "<p  class='error-toast'>Failed to create account.</p>";
            die();
        } else {
            header('Location: createAccount.php?success=1');
            die();
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <script src="https://cdn.tailwindcss.com"></script>
        <title>Fredericksburg SPCA | Change Password</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Create Account</h1>
        <main class="login">
            <?php if (isset($error1)) : ?>
                <p class="error-toast">Passwords do not match.</p>
            <?php elseif (isset($error2)) : ?>
                <p class="error-toast">Your new password must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number.</p>
            <?php elseif (isset($error3)) : ?>
                <p class="error-toast">Username already exists. Please choose a different username.</p>
            <?php elseif (isset($_GET['success']) && $_GET['success'] == 1) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <p class="text-center">Account created successfully.</p>
            </div>
            <?php endif ?>
            <form id="password-change" method="post">
                <label for="type">Account type</label>
                <select id="type" name="type">
                    <option value="0">Volunteer</option>
                    <option value="1">Coordinator/Board Member</option>
                    <option value="2">Admin</option>
                </select>
                <label for="username">New username</label>
                <input type="text" id="username" name="new-username" placeholder="Enter new username" required>
                <label for="password">New password</label>
                <input type="password" id="password" name="new-password" placeholder="Enter new password" required>
                 <p id="password-error" class="error hidden">Password needs to be at least 8 characters long, contain at least one number, one uppercase letter, and one lowercase letter!</p>
                <label for="reenter-password">New Password</label>
                <input type="password" id="password-reenter" name = 'new-password-reenter' placeholder="Re-enter new password" required>
                <p id="password-match-error" class="error hidden">Passwords must match!</p>
                <input type="submit" id="submit" name="submit" value="Create Account">
                <a class="button cancel" href="index.php">Cancel</a>
            </form>
        </main>
    </body>
</html>