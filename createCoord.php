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
    require_once('database/dbCoordBoard.php');


    if (!wereRequiredFieldsSubmitted($_POST, array('type', 'first_name', 'last_name', 'email', 'phone1', 'phone1type'))) {
        echo "Args missing";
        die();
    }


    $type = $_POST['type'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone1 = $_POST['phone1'];
    $phone1type = $_POST['phone1type'];

    $emergency_first_name = isset($_POST['emergency_first_name']) ? $_POST['emergency_first_name'] : "";
    $emergency_last_name = isset($_POST['emergency_last_name']) ? $_POST['emergency_last_name'] : "";
    $emergency_phone = isset($_POST['emergency_phone']) ? $_POST['emergency_phone'] : "";
    $emergency_phone_type = isset($_POST['emergency_phone_type']) ? $_POST['emergency_phone_type'] : "";
    $emergency_relation = isset($_POST['emergency_relation']) ? $_POST['emergency_relation'] : "";
    $phone1 = preg_replace("/[^0-9 ]/",'',$phone1);
    $emergency_phone = preg_replace("/[^0-9 ]/",'',$emergency_phone);

    $result = create_coordinator_board($type, $first_name, $last_name, $phone1, $phone1type, $emergency_phone, $emergency_phone_type, $email, $emergency_first_name, $emergency_last_name, $emergency_relation);
    if ($result === 'duplicate') {
        $error3 = true;
    } elseif (!$result) {
         $error3 = true;
    } else {
        header('Location: createCoord.php?success=1');
        die();
    }

}


?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <script src="https://cdn.tailwindcss.com"></script>
        <title>Fredericksburg SPCA | Create Coordinator/Board Member</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1></h1>
        <h1></h1>
        <h1>Create Volunteer Coordinator/Board Member</h1>
        <main class="login">
            <?php if (isset($error3)) : ?>
                <p class="error-toast">Name already exists. Please choose a different name.</p>
            <?php elseif (isset($_GET['success']) && $_GET['success'] == 1) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <p class="text-center">Account created successfully.</p>
            </div>
            <?php endif ?>
            <form id="coordinator_board_add" method="post">
                <label for="type"><em style="color:red;">* </em>Account type</label>
                <select id="type" name="type">
                    <option value="coord">Volunteer Coordinator</option>
                    <option value="board">Board Member</option>
                </select>
                <label for="first_name"><em style="color:red;">* </em>First name</label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter first name" required>
                <label for="last_name"><em style="color:red;">* </em>Last name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter last name" required>
                <label for="email"><em style="color:red;">* </em>Email Address</label>
                <input type="text" id="email" name="email" placeholder="Enter email address" required>
                <label for="phone1"><em style="color:red;">* </em>Phone Number</label>
                <input type="text" id="phone1" name="phone1" placeholder="Enter phone number" required>
                <label for="phone1type"><em style="color:red;">* </em>Phone Type</label>
                <select id="phone1type" name="phone1type">
                    <option value="home">Home</option>
                    <option value="cellphone">Cellphone</option>
                    <option value="mobile">Mobile</option>
                    <option value="work">Work</option>
                </select>
                <label for="emergency_first_name">Emergency First name</label>
                <input type="text" id="emergency_first_name" name="emergency_first_name" placeholder="Enter first name">
                <label for="emergency_last_name">Emergency Last name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter last name">
                <label for="emergency_phone">Emergency Phone Number</label>
                <input type="text" id="emergency_phone" name="emergency_phone" placeholder="Enter phone number">
                <select id="emergency_phone_type" name="emergency_phone_type">
                    <option value="home">Home</option>
                    <option value="cellphone">Cellphone</option>
                    <option value="mobile">Mobile</option>
                    <option value="work">Work</option>
                </select>
                <label for="emergency_relation">Emergency Relation</label>
                <input type="text" id="emergency_relation" name="emergency_relation" placeholder="Enter relation">
                <input type="submit" id="submit" name="submit" value="Create Account">
                <a class="button cancel" href="index.php">Cancel</a>
            </form>
        </main>
    </body>
</html>
