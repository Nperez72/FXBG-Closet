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
require('database/dbPersons.php');
if (isset($_SESSION['person_id'])) {
    $person = updated_retrieve_person($_SESSION['person_id']);

    // Check that the function actually returned a result
    if ($person && $person->get_first_name()) {
        $personName = $person->get_first_name() . $person->get_last_name();
    } else {
        $personName = "";
    }
}
if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}

    require_once('database/dbAccounts.php');
    $accessGrant = false;
    $accounts = array();
if ($loggedIn) {
    $accountType = get_account_type($userID);
    // 0: volunteer, 1: coordinator/board memeber, 2: admin
    if ($accountType !== null && $accountType >= 1) {
        $accessGrant = true;
    }
}

if (!$accessGrant) {
    header('Location: login.php');
    die();
}

if ($accessGrant) {
    $persons = getall_coordinator_names();
}

if (!$loggedIn) {
    header('Location: login.php');
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once('include/input-validation.php');


    if (!wereRequiredFieldsSubmitted($_POST, array('name', 'amount'))) {
        echo "Args missing";
        die();
    }

    $name = $_POST['name'];
    $amount = $_POST['amount'];

    header('Location: createFinancialLog.php?success=1');
    die();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <script src="https://cdn.tailwindcss.com"></script>
        <title>Fredericksburg Pride | Create Financial Log</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Create Financial Log</h1>
        <main class="login">
            <?php if (isset($error1)) : ?>
                <p class="error-toast">Passwords do not match.</p>
            <?php elseif (isset($error2)) : ?>
                <p class="error-toast">Your new password must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number.</p>
            <?php elseif (isset($error3)) : ?>
                <p class="error-toast">Username already exists. Please choose a different username.</p>
            <?php elseif (isset($_GET['success']) && $_GET['success'] == 1) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <p class="text-center">Log created successfully.</p>
            </div>
            <?php endif ?>
            <form id="password-change" method="post">
                <?php if ($accessGrant) : ?>
                    <label for="name">Name</label>
                    <select id="name" name="name">
                        <?php foreach ($persons as $person) : ?>
                            <option value="<?php echo ($person); ?>"<?php if ($personName = $person) echo "selected"?>> <?php
                                echo $person;
                             ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif ?>
                <label for="amount">Dollar Amount:</label>
                <input type="text" id="amount" name="amount" placeholder = "Enter dollar amount" pattern="^\$?\d{1,3}(?:,\d{3})*(?:\.\d{2})?$" required >
                <p id="money-error" class="error hidden">Please write amount in the form of "xx.xx"!</p>
                <label for="type">Description:</label>
                <input type="text" id="description" name="description" placeholder = "Enter description">
                <input type="submit" id="submit" name="submit" value="Create Financial Log">
                <a class="button cancel" href="index.php">Cancel</a>
            </form>
        </main>
    </body>
</html>