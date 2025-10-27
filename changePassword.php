<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    require_once('include/api.php');
    require_once('database/dbAccounts.php');
    require_once('email.php');
    require_once('include/input-validation.php');

    ini_set("display_errors", 1);
    error_reporting(E_ALL);
    
    
    $loggedIn = isset($_SESSION['_id']);
    $accessLevel = $loggedIn ? $_SESSION['access_level'] : 0; // 0=not logged in, 1=standard user, 2=manager(Admin), 3=super admin (TBI)
    $userID = $loggedIn ? $_SESSION['_id'] : null;
    $isAdmin = false;
    $accounts = [];
    $errors = [];
    $showVerifyForm = isset($_SESSION['pwd_change_pending']);

    if($loggedIn) {
        // 0: volunteer, 1: coordinator/board memeber, 2: admin
        $accountType = get_account_type($userID);
        if ($accountType !== null && $accountType >= 2) {
            $isAdmin = true;
            $accounts = get_all_accounts();
        }
    }
}

    if(!$isAdmin) {
        header('Location: login.php');
        die();
    }
    
    // clears pending password change on cancel
    if (isset($_GET['cancel']) && $_GET['cancel'] === '1') {
        unset($_SESSION['pwd_change_pending']);
        header('Location: changePassword.php');
        die();
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // verify code and change password
        if (isset($_POST['action']) && $_POST['action'] === 'verify') {
            $pending = $_SESSION['pwd_change_pending'] ?? null;

            if (!$pending) {
                $errors[] = "No pending password change request. Please start over.";
            }
            else {
                $code = trim($_POST['verification-code'] ?? '');

                if ($code !== $pending['code']) {
                    $errors[] = "Invalid verification code. Please try again.";
                    $showVerifyForm = true;
                }
                else if (time() > $pending['expires']) {
                    $errors[] = "Verification code has expired. Please start over.";
                    unset($_SESSION['pwd_change_pending']);
                    $showVerifyForm = false;
                }
                else {
                    // code is valid and not expired, complete password change
                    $targetUser = $pending['target'];
                    $hash = $pending['hash'];
                    unset($_SESSION['pwd_change_pending']);

                    if(!change_account_password($targetUser, $hash)) {
                        $errors[] = "Failed to update password.";
                        $showVerifyForm = false;
                    }
                    // If admin changed their own password, destroy session and force re-login
                    else if ($targetUser === $userID) {
                        session_destroy();
                        header('Location: login.php?success=1');
                        die();
                    }
                    // If admin changed another account password, stay logged in and show success message
                    else {
                        header('Location: changePassword.php?success=1');
                        die();
                    }
                }
            }
        }
        else {
            // target account (admin can choose any account to change password for)
            $targetUser = $userID;
            if (isset($_POST['target']) && $_POST['target'] !== '') {
                $targetUser = $_POST['target'];
            }

            if (!wereRequiredFieldsSubmitted($_POST, array('new-password'))) {
                echo "Args missing";
                die();
            }

            $password = $_POST['password'];
            $newPassword = $_POST['new-password'];
            $securePassword = isSecurePassword($newPassword);

            if (!verify_account_password($targetUser, $password)) {
                $errors[] = "Incorrect current password for selected account.";
            } 
            else if ($password == $newPassword) {
                $errors[] = "New password must be different from current password.";
            }
            else if (!$securePassword) {
                $errors[] = "Your new password must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number.";
            } 
            else {
                $hash = password_hash($newPassword, PASSWORD_BCRYPT);
                $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                // After this is set, it causes the verification form to show
                $_SESSION['pwd_change_pending'] = [
                    'target'    => $targetUser,
                    'hash'      => $hash,
                    'code'      => $code,
                    'expires'   => time() + 10 * 60, // 10 minutes
                    'initiator' => $userID,
                ];

                $when = date('Y-m-d H:i:s');
                $subject = 'FXBG Closet: Password Change Verification Code';
                $body = "A password change has been requested.\n\n" .
                        "Initiated by: {$userID}\n" .
                        "Target account: {$targetUser}\n" .
                        "Time: {$when}\n\n" .
                        "Your verification code (expires in 10 minutes):\n\n" .
                        "{$code}\n\n" .
                        "If you did not request this change, please contact your system administrator immediately.";
                
                emailAdmins('security', $subject, $body);
                header('Location: changePassword.php');
                die();   
            }
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
        <h1>Change Password</h1>
        <main class="login">
            <?php foreach ($errors as $error): ?>
                <p class="error-toast"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>

            <?php if (isset($_GET['success']) && $_GET['success'] == 1 && empty($errors)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <p class="text-center">Password successfully changed.</p>
                </div>
            <?php endif ?>

            <?php if ($showVerifyForm): ?>
                <!-- Verification Code Form -->
                <form id="password-verify" method="post">
                    <input type="hidden" name="action" value="verify">
                    <label for="verification-code">Verification Code *</label>
                    <input type="text" 
                           id="verification-code" 
                           name="verification-code" 
                           placeholder="Enter 6-digit code" 
                           pattern="\d{6}" 
                           maxlength="6"
                           required 
                           autofocus>
                    <p class="info-text">
                        A 6-digit verification code was emailed to all administrators. 
                        The code expires in 10 minutes.
                    </p>
                    <input type="submit" value="Verify and Change Password">
                    <a class="button cancel" href="changePassword.php?cancel=1">Cancel</a>
                </form>
            <?php else: ?>
            <!-- Password Change Form -->
            <form id="password-change" method="post">
                <?php if ($isAdmin) : ?>
                    <label for="target">Account to Change</label>
                    <select id="target" name="target">
                        <?php foreach ($accounts as $acct) : ?>
                            <option value="<?php echo htmlspecialchars($acct['username']); ?>" <?php if ($acct['username'] === $userID) {
                                echo 'selected';
                                           } ?>>
                                <?php echo htmlspecialchars($acct['username'] . ' (' . ($acct['type'] == 2 ? 'admin' : ($acct['type'] == 1 ? 'coordinator' : 'volunteer')) . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif ?>
                <label for="password">Account Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password for selected account" required>
                <label for="new-password">New Password</label>
                <input type="password" id="new-password" name="new-password" placeholder="Enter new password" required>
                 <p id="password-error" class="error hidden">Password needs to be at least 8 characters long, contain at least one number, one uppercase letter, and one lowercase letter!</p>
                <label for="new-password-reenter">New Password</label>
                <input type="password" id="new-password-reenter" name="confirm-password" placeholder="Re-enter new password" required>
                <p id="password-match-error" class="error hidden">Passwords must match!</p>
                <input type="submit" id="submit" name="submit" value="Change Password">
                <a class="button cancel" href="index.php">Cancel</a>
            </form>

            <?php endif; ?>
        </main>
    </body>
</html>