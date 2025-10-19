<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    require_once('include/api.php');

    ini_set("display_errors",1);
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
    if($loggedIn) {
        $accountType = get_account_type($userID);
        // 0: volunteer, 1: coordinator/board memeber, 2: admin
        if ($accountType !== null && $accountType >= 2) {
            $isAdmin = true;
        }
    }

    if(!$isAdmin) {
        header('Location: login.php');
        die();
    }
    if($isAdmin) {
        $accounts = get_all_accounts();
    }

    // $forced = false;
    // if (isset($_SESSION['change-password']) && $_SESSION['change-password']) {
    //     $forced = true;
    // } else 
    
    if (!$loggedIn) {
        header('Location: login.php');
        die();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once('include/input-validation.php');
        // require_once('domain/Person.php');
        // require_once('database/dbPersons.php');

        // target account (admin can choose any account to change password for)
        $targetUser = $userID;
        if (isset($_POST['target']) && $_POST['target'] !== '') {
            $targetUser = $_POST['target'];
        }

        // if ($forced) {
        if (!wereRequiredFieldsSubmitted($_POST, array('new-password'))) {
            echo "Args missing";
            die();
        }

        $password = $_POST['password'];
        $newPassword = $_POST['new-password'];
        $securePassword = isSecurePassword($newPassword);

        if (!verify_account_password($targetUser, $password)) {
            $error1 = true; // incorrect old password for selected account
        } 
        else if ($password == $newPassword) {
            $error2 = true; // new is same as old
        } 
        else if (!$securePassword) {
            $error3 = true; // password isn't secure
        } 
        else {
            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            if(!change_account_password($targetUser, $hash)) {
                echo "Failed to update password.";
                die();
            }

            // If admin changed their own password, destroy session and force re-login
            // If admin changed another account password, stay logged in and show success message
            if ($targetUser === $userID) {
                session_destroy();
                header('Location: login.php?success=1');
                die();
            } 
            else {
                header('Location: changePassword.php?success=1');
                die();
            }

        }
    }
            // if ($userID == 'vmsroot') {
            //     $_SESSION['access_level'] = 3;
            // } else {
            //     // $user = retrieve_person($userID);
            //     // $_SESSION['access_level'] = $user->get_access_level();

            //     // This might not work
            //     $accountType = get_account_type($userID);
            //     switch($accountType){
            //         // volunteer
            //         case 0:
            //             $_SESSION['access_level'] = 1;
            //             break;
            //         // coordinator/board member
            //         case 1:
            //             $_SESSION['access_level'] = 2;
            //             break;
            //         // admin
            //         case 2:
            //             $_SESSION['access_level'] = 3;
            //             break;
            //         }
            // }

            // If admin changed their own password, have them login back in using it
        //     if ($targetUser === $userID || $forced) {
        //         session_destroy();
        //         header('Location: login.php?success=1');
        //         die();
        //     } else {
        //         // Admin changed other account password, just display status message
        //         header('Location: changePasswords.php?success=1');
        //         die();
        //     }


        //     if ($targetUser === $userID) {
        //         if ($userID == 'vmsroot') {
        //             $_SESSION['access_level'] = 3;
        //         } else {
        //             $accountType = get_account_type($userID);
        //             switch($accountType){
        //                 // volunteer
        //                 case 0:
        //                     $_SESSION['access_level'] = 1;
        //                     break;
        //                 // coordinator/board member    
        //                 case 1:
        //                     $_SESSION['access_level'] = 2;
        //                     break;
        //                 // admin
        //                 case 2:
        //                     $_SESSION['access_level'] = 3;
        //                     break;
        //             }
        //         }
        //     }

        //     $_SESSION['logged_in'] = true;
        //     unset($_SESSION['change-password']);
        //     session_destroy();
        //     header('Location: login.php?success=1');
        //     die();
        // } 
        // else {
        //     if (!wereRequiredFieldsSubmitted($_POST, array('password', 'new-password'))) {
        //         echo "Args missing";
        //         die();
        //     }
        //     $password = $_POST['password'];
        //     $newPassword = $_POST['new-password'];
        //     $securePassword = isSecurePassword($_POST['new-password']);
        //     // $user = retrieve_person($userID);
        //     // if (!password_verify($password, $user->get_password())) {
        //         if (!verify_account_password($userID, $password)) {
        //             $error1 = true;
        //         } else if($password == $newPassword) {     // old password is same as new one
        //             $error2 = true;
        //         } else if (!$securePassword) {
        //             $error3 = true;
        //         } else {
        //             $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        //             // change_password($userID, $hash);
        //             change_account_password($userID, $hash);
        //             session_destroy();
        //             header('Location: login.php?success=1');
        //             die();
        //     }
        // }
    // }
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
        <h1>Change Password</h1>
        <main class="login">
            <?php if (isset($error1)): ?>
                <p class="error-toast">Your entry for Current Password was incorrect.</p>
            <?php elseif (isset($error2)): ?>
                <p class="error-toast">New password must be different from current password.</p>
            <?php elseif (isset($error3)): ?>
                <p class="error-toast">Your new password must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number.</p> 
            <?php elseif (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <p class="text-center">Password successfully changed.</p>
            </div>
            <?php endif ?>
            <form id="password-change" method="post">
                <?php if ($isAdmin): ?>
                    <label for="target">Account to Change</label>
                    <select id="target" name="target">
                        <?php foreach ($accounts as $acct): ?>
                            <option value="<?php echo htmlspecialchars($acct['username']); ?>" <?php if ($acct['username'] === $userID) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($acct['username'] . ' (' . ($acct['type']==2?'admin':($acct['type']==1?'coordinator':'volunteer')) . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif ?>
                <!-- <?php if (!$forced): ?> -->
                    <label for="password">Account Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password for selected account" required>
                <!-- <?php else: ?>
                    <p>You must change your password before continuing.</p>
                <?php endif ?> -->
                <label for="new-password">New Password</label>
                <input type="password" id="new-password" name="new-password" placeholder="Enter new password" required>
                 <p id="password-error" class="error hidden">Password needs to be at least 8 characters long, contain at least one number, one uppercase letter, and one lowercase letter!</p>
                <label for="reenter-new-password">New Password</label>
                <input type="password" id="new-password-reenter" placeholder="Re-enter new password" required>
                <p id="password-match-error" class="error hidden">Passwords must match!</p>
                <input type="submit" id="submit" name="submit" value="Change Password">
                <!-- <?php if (!$forced): ?> -->
                <a class="button cancel" href="index.php">Cancel</a>
                <!-- <?php endif ?> -->
            </form>
        </main>
    </body>
</html>