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

if ($loggedIn) {
    // 0: volunteer, 1: coordinator/board memeber, 2: admin
    $accountType = get_account_type($userID);
    if ($accountType !== null && $accountType >= 2) {
        $isAdmin = true;
        $accounts = get_all_accounts();
    }
}

if (!$isAdmin) {
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
        } else {
            $code = trim($_POST['verification-code'] ?? '');

            if ($code !== $pending['code']) {
                $errors[] = "Invalid verification code. Please try again.";
                $showVerifyForm = true;
            } elseif (time() > $pending['expires']) {
                $errors[] = "Verification code has expired. Please start over.";
                unset($_SESSION['pwd_change_pending']);
                $showVerifyForm = false;
            } else {
                // code is valid and not expired, complete password change
                $targetUser = $pending['target'];
                $hash = $pending['hash'];
                unset($_SESSION['pwd_change_pending']);

                if (!change_account_password($targetUser, $hash)) {
                    $errors[] = "Failed to update password.";
                    $showVerifyForm = false;
                }
                // If admin changed their own password, destroy session and force re-login
                elseif ($targetUser === $userID) {
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
    } else {
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
        } elseif ($password == $newPassword) {
            $errors[] = "New password must be different from current password.";
        } elseif (!$securePassword) {
            $errors[] = "Your new password must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number.";
        } else {
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

            $when = date('Y-m-d g:i:s A');
            $subject = 'FXBG Closet: Password Change Verification Code';
            $body = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
            <meta charset='UTF-8'>
            <meta name='x-apple-disable-message-reformatting'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Password Change Verification</title>
            <style>
                @media only screen and (max-width:600px) {
                .container { width:100% !important; }
                .inner { padding:24px 20px !important; }
                .p-sm { padding:16px 8px !important; }
                .h1 { font-size:24px !important; line-height:30px !important; }
                .subtitle { font-size:15px !important; }
                .text { font-size:15px !important; line-height:22px !important; }
                .code-box { padding:20px 16px !important; }
                .row-label { display:block !important; width:100% !important; padding:16px 0 6px 0 !important; }
                .row-value { display:block !important; width:100% !important; padding:0 0 20px 0 !important; }
                .card { padding:18px !important; }
                }
            </style>
            </head>
            <body style='margin:0; padding:0; background:#f8f9fa; font-family:Arial, Helvetica, sans-serif;'>
            <div style='display:none; max-height:0; overflow:hidden; mso-hide:all;'>
                Password change verification code for FXBG Closet
            </div>
            <center role='article' aria-roledescription='email' lang='en'>
                <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%' style='background:#f8f9fa;'>
                <tr>
                    <td align='center' class='p-sm' style='padding:32px 16px;'>
                    <!--[if mso]>
                    <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='600'>
                    <tr><td>
                    <![endif]-->
                    <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%' class='container' style='max-width:600px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.08);'>
                        
                        <!-- Alert header bar -->
                        <tr>
                        <td style='padding:0;'>
                            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0'>
                            <tr>
                                <td style='background:#dc2626; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#ea580c; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#f59e0b; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                            </tr>
                            </table>
                        </td>
                        </tr>

                        <!-- Header -->
                        <tr>
                        <td align='center' style='padding:40px 28px 12px 28px; background:linear-gradient(180deg, #fef2f2 0%, #ffffff 100%); background-color:#fef2f2;'>
                            <div style='display:inline-block; background:#dc2626; color:#ffffff; padding:8px 16px; border-radius:20px; font-size:12px; font-weight:600; letter-spacing:0.5px; text-transform:uppercase; margin-bottom:16px;'>
                            Security Alert
                            </div>
                            <h1 class='h1' style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:28px; line-height:36px; color:#1a1a1a; font-weight:700;'>
                            Password Change Requested
                            </h1>
                            <p class='subtitle' style='margin:12px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:24px; color:#6b7280;'>
                            Use the verification code below to proceed
                            </p>
                        </td>
                        </tr>

                        <!-- Content -->
                        <tr>
                        <td class='inner' style='padding:32px 28px;'>
                            
                            <!-- Warning message -->
                            <div style='background:#fef2f2; border-left:4px solid #dc2626; padding:16px 20px; margin-bottom:24px; border-radius:8px;'>
                            <p style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; color:#991b1b; font-weight:600;'>
                                If you did not request this change, please contact your system administrator immediately.
                            </p>
                            </div>
                            
                            <!-- Details card -->
                            <div class='card' style='background:#f8f9fa; border-radius:12px; padding:24px; margin-bottom:24px;'>
                            <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%' style='border-collapse:collapse;'>
                                
                                <!-- Initiated by -->
                                <tr>
                                <td class='row-label' valign='top' width='140' style='padding:0 20px 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:24px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                                    Initiated By
                                </td>
                                <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#374151; font-weight:600;'>
                                    {$userID}
                                </td>
                                </tr>

                                <!-- Spacer -->
                                <tr><td colspan='2' style='padding:12px 0;'></td></tr>

                                <!-- Target account -->
                                <tr>
                                <td class='row-label' valign='top' width='140' style='padding:0 20px 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:24px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                                    Target Account
                                </td>
                                <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#374151; font-weight:600;'>
                                    {$targetUser}
                                </td>
                                </tr>

                                <!-- Spacer -->
                                <tr><td colspan='2' style='padding:12px 0;'></td></tr>

                                <!-- Time -->
                                <tr>
                                <td class='row-label' valign='top' width='140' style='padding:0 20px 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:24px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                                    Time
                                </td>
                                <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#374151; font-weight:600;'>
                                    {$when}
                                </td>
                                </tr>
                                
                            </table>
                            </div>
                            
                            <!-- Verification code -->
                            <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%'>
                            <tr>
                                <td align='center' style='padding:0 0 16px 0;'>
                                <p style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#6b7280; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                                    Your Verification Code
                                </p>
                                </td>
                            </tr>
                            <tr>
                                <td align='center' class='code-box' style='padding:28px 24px; background:#ffffff; border:2px solid #e5e7eb; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.05);'>
                                <div style='font-family:Courier New, Courier, monospace; font-size:32px; font-weight:700; color:#1a1a1a; letter-spacing:8px;'>
                                    {$code}
                                </div>
                                </td>
                            </tr>
                            <tr>
                                <td align='center' style='padding:16px 0 0 0;'>
                                <p style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#ef4444; font-weight:600;'>
                                    Expires in 10 minutes
                                </p>
                                </td>
                            </tr>
                            </table>
                            
                        </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                        <td align='center' style='padding:32px 28px; background:#f9fafb; border-top:1px solid #e5e7eb;'>
                            <p class='text' style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:20px; color:#9ca3af;'>
                            Automatically generated by FXBG Closet Security System
                            </p>
                            <p class='text' style='margin:6px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:18px; color:#d1d5db;'>
                            {$whenFormatted}
                            </p>
                        </td>
                        </tr>

                        <!-- Alert footer bar -->
                        <tr>
                        <td style='padding:0;'>
                            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0'>
                            <tr>
                                <td style='background:#dc2626; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#ea580c; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#f59e0b; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                            </tr>
                            </table>
                        </td>
                        </tr>

                    </table>
                    <!--[if mso]></td></tr></table><![endif]-->
                    </td>
                </tr>
                </table>
            </center>
            </body>
            </html>
            ";
            
            emailAdmins('security', $subject, $body);
            header('Location: changePassword.php');
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
        <h1>Change Password</h1>
        <main class="login">
            <?php foreach ($errors as $error) : ?>
                <p class="error-toast"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>

            <?php if (isset($_GET['success']) && $_GET['success'] == 1 && empty($errors)) : ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <p class="text-center">Password successfully changed.</p>
                </div>
            <?php endif ?>

            <?php if ($showVerifyForm) : ?>
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
            <?php else : ?>
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