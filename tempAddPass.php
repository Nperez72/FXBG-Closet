<?php

require_once('database/dbAccounts.php');

$volunteerUser = "volunteer";
$hash = password_hash("1", PASSWORD_BCRYPT);

if (change_account_password($volunteerUser, $hash)) {
    echo "<script>console.log('Password for {$volunteerUser} updated.');</script>";
} else {
    echo "<script>console.log('Failed to update password for {$volunteerUser}.');</script>";
}

$coordinatorUser = "coordinator";
$hash = password_hash("2", PASSWORD_BCRYPT);

if (change_account_password($coordinatorUser, $hash)) {
    echo "<script>console.log('Password for {$coordinatorUser} updated.');</script>";
} else {
    echo "<script>console.log('Failed to update password for {$coordinatorUser}.');</script>";
}

$adminUser = "admin";
$hash = password_hash("3", PASSWORD_BCRYPT);

if (change_account_password($adminUser, $hash)) {
    echo "<script>console.log('Password for {$adminUser} updated.');</script>";
} else {
    echo "<script>console.log('Failed to update password for {$adminUser}.');</script>";
}

$vmsrootUser = "vmsroot";
$hash = password_hash("vmsroot", PASSWORD_BCRYPT);

if (change_account_password($vmsrootUser, $hash)) {
    echo "<script>console.log('Password for {$vmsrootUser} updated.');</script>";
} else {
    echo "<script>console.log('Failed to update password for {$vmsrootUser}.');</script>";
}