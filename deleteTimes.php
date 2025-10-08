<?php

    session_cache_expire(30);
    session_start();
    //date_default_timezone_set("America/New_York");

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    $user = null;

    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }
    if ($accessLevel < 1) {
        header('Location: login.php');
        die();
    }
    if ($accessLevel == 1) {
        $user = $_SESSION['_id'];
    }

    require_once('include/input-validation.php');
    require_once('database/dbPersons.php');

	if ($accessLevel == 1) {
		$userID = $_SESSION['_id'];
	} else {
		if (isset($_GET['userID'])) {
			$userID = $_GET['userID'];
		} else {
			header('Location: login.php');
			die();
		}
	}

	if (isset($_GET['eventID'])) {
		$eventID = $_GET['eventID'];
	} else {
		header('Location: index.php');
		die();
	}

	if (isset($_GET['start_time'])) {
		$start_time = htmlspecialchars($_GET['start_time']);
	} else {
		header('Location: index.php');
		die();
	}

	if (isset($_GET['end_time'])) {
		$end_time = htmlspecialchars($_GET['end_time']);
	} else {
		header('Location: index.php');
		die();
	}

	delete_check_in($userID, $eventID, $start_time, $end_time);

	if ($accessLevel == 1) {
		header('Location: eventList.php');
	} else {
		header('Location: eventList.php?username=' . $userID);
	}

 ?>