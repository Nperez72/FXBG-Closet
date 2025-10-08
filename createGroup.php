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

if ($accessLevel < 2) {
    header('Location: index.php');
    die();
}

require_once 'database/dbGroups.php';
require_once 'domain/Groups.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  	<link href="css/normal_tw.css" rel="stylesheet">
<!-- BANDAID FIX FOR HEADER BEING WEIRD -->
<?php
$tailwind_mode = true;
require_once('header.php');
?>
<style>
        .date-box {
            background: #274471;
            padding: 7px 30px;
            border-radius: 50px;
            box-shadow: -4px 4px 4px rgba(0, 0, 0, 0.25) inset;
            color: white;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
        }   
        .dropdown {
            padding-right: 50px;
        }   

</style>
<!-- BANDAID END, REMOVE ONCE SOME GENIUS FIXES -->

</head>
<body>

<header class="hero-header">
	<div class="center-header">
		<h1>Create a New Group</h1>
	</div>
</header>

    <main>
       <div class="main-content-box w-full max-w-3xl p-8">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $group_name = $_POST['group_name'];
            $color_level = $_POST['color_level'];

            $group = new Group($group_name, $color_level);
            $result = add_group($group);

            if ($result) {
                echo "<p class='text-white bg-green-700 text-center p-2 rounded-lg mb-2'>Group successfully created!</p>";
            } else {
                echo "<p class='error-block'>Error: Group already exists or there was an issue adding the group.</p>";
            }
        }
        ?>
        
        <form method="POST" action="">
            <label for="group_name">Group Name:</label>
            <input type="text" id="group_name" name="group_name" required>

            <label for="color_level">Color Level:</label>
            <select id="color_level" name="color_level" required>
                <option value="green">Green</option>
                <option value="pink">Pink</option>
                <option value="orange">Orange</option>
            </select>

	<div class="text-center mt-6">
            <button type="submit" class="blue-button">Save Group</button>
	</div>
        </form>
	</div>
	<div class="text-center mt-6">
        <a href="groupManagement.php" class="return-button">Back to Group Management</a>
	</div>

<div class="info-section mt-0">
	<div class="blue-div"></div>
	<p class="info-text">Create a new Volunteer Group. 
	</p>
</div>
    </main>
</body>
</html>