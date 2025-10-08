<?php
    session_cache_expire(30);
    session_start();

    $loggedin = false;
    $accesslevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }
    // admin-only access
    if ($accessLevel < 2) {
        header('Location: index.php');
        die();
    }
    if (!$loggedIn) {
        header('Location: login.php');
        die();
    }

    
    if (isset($_GET['file'])) {
        $fileToDelete = $_GET['file'];
        $target_dir = 'uploads/';

        // Verify target file exists
        $filePath = $target_dir . $fileToDelete;

        if(file_exists($filePath)) {
            // Delete target file
            unlink($filePath);
            header('Location: resources.php');
            exit();
        } else {
            echo "Error: File does not exist.";
        }
    } else {
        echo "Error: No file specified.";
    }
?>