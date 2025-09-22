<?php
session_start();

// Toggle lock state
if (!isset($_SESSION['locked'])) {
    $_SESSION['locked'] = false;
}

// Flip the lock state
$_SESSION['locked'] = !$_SESSION['locked'];

// Return the new state as JSON
echo json_encode(['locked' => $_SESSION['locked']]);
?>

