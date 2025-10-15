<?php

require_once('dbinfo.php');

function get_account_type($username) {
    $query = "SELECT type FROM dbaccounts WHERE username='$username'";
    $connection = connect();
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return null;
    }
    $account = mysqli_fetch_row($result);
    print_r($account);
}