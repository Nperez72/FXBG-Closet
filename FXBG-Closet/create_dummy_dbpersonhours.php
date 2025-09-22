<?php

// create dummy dbpersonhours entry

session_start();

ini_set("display_errors", 1);
error_reporting(E_ALL);

// Check access levels and initialize user data
$loggedIn = false;
$accessLevel = 0;
$userID = null;
if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}

// Require admin privileges
if ($accessLevel < 2) {
    header('Location: login.php');
    die();
}
require_once('database/dbPersons.php');
$con=connect();
$query = "INSERT INTO dbpersonhours (personID, eventID, start_time, end_time) VALUES ('someInfo2',33,'2025-01-01 12:00','2025-01-01 12:01')";
$result = mysqli_query($con,$query);
mysqli_close($con);
header('Location: index.php')

?>