<?php

require_once('dbinfo.php');

function get_account_type($username)
{
    $connection = connect();
    if (!$connection) {
        echo "<script>console.log('Failed to connect to dbAccounts');</script>";
        return null;
    }

    $stmt = mysqli_prepare($connection, "SELECT `type` FROM `dbaccounts` WHERE `username` = ? LIMIT 1");
    if (!$stmt) {
        echo "<script>console.log('Statement prepare failed');</script>";
        mysqli_close($connection);
        return null;
    }

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $type);
    $found = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    if ($found === null || $found === false) {
        echo "<script>console.log('Not Found');</script>";
        return null;
    }
    return intval($type);
}

function verify_account_password($username, $password)
{
    $connection = connect();
    if (!$connection) {
        return false;
    }
    $stmt = mysqli_prepare($connection, "SELECT `password` FROM `dbaccounts` WHERE `username` = ? LIMIT 1");
    if (!$stmt) {
        mysqli_close($connection);
        return false;
    }
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hash);
    $found = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    if (!$found || !$hash) {
        return false;
    }
    return password_verify($password, $hash);
}

function change_account_password($username, $new_hash)
{
    $connection = connect();
    if (!$connection) {
        return false;
    }
    $stmt = mysqli_prepare($connection, "UPDATE `dbaccounts` SET `password` = ? WHERE `username` = ?");
    if (!$stmt) {
        mysqli_close($connection);
        return false;
    }
    mysqli_stmt_bind_param($stmt, "ss", $new_hash, $username);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    return $ok;
}

function get_all_accounts()
{
    $connection = connect();
    if (!$connection) {
        return array();
    }
    $stmt = mysqli_prepare($connection, "SELECT `username`, `type` FROM `dbaccounts` ORDER BY `type` DESC, `username` ASC");
    if (!$stmt) {
        mysqli_close($connection);
        return array();
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $username, $type);
    $rows = array();
    while (mysqli_stmt_fetch($stmt)) {
        $rows[] = array('username' => $username, 'type' => intval($type));
    }
    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    return $rows;
}

function create_account($username, $password, $type)
{
    $connection = connect();
    if (!$connection) {
        echo "<script>console.log('Failed to connect to the database');</script>";
        return false;
    }
    $check_stmt = mysqli_prepare($connection, "SELECT `username` FROM `dbaccounts` WHERE `username` = ? LIMIT 1");
    if (!$check_stmt) {
        mysqli_close($connection);
        return false;
    }

    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        // Username already exists
        mysqli_stmt_close($check_stmt);
        mysqli_close($connection);
        return 'duplicate';
    }

    mysqli_stmt_close($check_stmt);

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($connection, "INSERT INTO `dbaccounts` (`username`, `password`, `type`) VALUES (?, ?, ?)");
    if (!$stmt) {
        mysqli_close($connection);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ssi", $username, $password_hash, $type);
    $success = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($connection);

    return $success;
}
