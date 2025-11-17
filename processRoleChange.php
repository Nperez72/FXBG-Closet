<?php

session_start();
include 'database/dbPersons.php';

if (!isset($_SESSION['_id'])) {
    die("Error: User not logged in.");
}

$_SESSION['person_id'] = $_POST['person_id'];

if ($_POST['role'] == "Board Member") {
    $_SESSION['access_level'] = 2;
} else if ($_POST['role'] == "Volunteer Coordinator") {
    $_SESSION['access_level'] = 3;
}

// For now, just direct to the homepage
header("Location: index.php");
exit();
