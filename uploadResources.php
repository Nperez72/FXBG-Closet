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

    
    $target_dir = 'uploads/';

    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    // check if the file is actually a pdf
    if(isset($_POST["submit"])) {
        if($_FILES["fileToUpload"]["error"] == 0) {
            // Check if file already exists
            if (file_exists($target_file)) {
                echo "A file with that name already exists."; //TODO Proper error out to user
                $uploadOk = 0;
            }
            
            // Check file size <=5mb
            if ($_FILES["fileToUpload"]["size"] > 5000000) {//TODO adjust to provided doc sizes
                echo "<center>Your file is too large.</center>"; //TODO Proper error out to user
                $uploadOk = 0;
            }


        } else {
            echo "<center>There was an error uploading your document.</center>"; //TODO Proper error out to user
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "<center>Sorry, your file was not uploaded.</center>";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "<center>The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded. </center>"; //TODO Prop
            } else {
                echo "<center>Sorry, there was an error uploading your file.</center>"; //TODO Proper error out to user
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <link rel="stylesheet" href="css/messages.css"></link>
    <script src="js/messages.js"></script>
    <title>Manage Documents</title>
</head>
<body>
    <main class="dashboard">
        <?php require_once('header.php') ?>
        <div id="calendar-footer">
            <a class="button cancel" href="resources.php">Return to Manage Documents</a>
        </div>
    </main>
</body>
</html>