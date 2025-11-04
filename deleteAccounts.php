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

    if (!wereRequiredFieldsSubmitted($_POST, array('target'))) {
        echo "Args missing";
        die();
    }

    $username = $_POST['target'];

        if (!delete_account($username)) {
            echo "<p class='error-toast'>Failed to delete account.</p>";
        } else {
            header('Location: deleteAccounts.php?success=1');
            die();
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
        <h1>Delete Account</h1>
        <main class="login">
            <?php if (isset($_GET['success']) && $_GET['success'] == 1) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <p class="text-center">Account deleted successfully.</p>
            </div>
            <?php endif ?>
            <form id="delte_accounts" method="post">
                <?php if ($isAdmin) : ?>
                    <label for="target">Account to Delete</label>
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
                <input type="submit" id="submit" name="submit" value="Delete Account">
                <a class="button cancel" href="index.php">Cancel</a>
            </form>
        </main>
    </body>
</html>