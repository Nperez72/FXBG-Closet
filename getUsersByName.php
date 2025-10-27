<?php

require_once "database/dbPersons.php";
session_start();

$query = isset($_GET['query']) ? $_GET['query'] : "";

$users = searchUsersByName($query);

echo json_encode($users);
