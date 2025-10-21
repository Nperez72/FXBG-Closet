<?php
session_start();
include 'database/dbPersons.php';

if (!isset($_SESSION['_id'])) {
    die("Error: User not logged in.");
}

$_SESSION['_id'] = $_POST['person_id'];

// For now, just direct to the homepage
header("Location: index.php");
exit();