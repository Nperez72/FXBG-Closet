<?php

session_start();

 

if (!isset($_SESSION['_id']) || $_SESSION['access_level'] < 1) {

    header('Location: login.php');

    exit;

}

 

require_once('database/dbPersons.php');

 

$userID = $_SESSION['_id'];

$user = retrieve_person($userID);

$points = 0;

$con = connect();

$escaped_id = mysqli_real_escape_string($con, $userID);

//testing this area

$query = "SELECT total_hours_volunteered FROM dbpersons WHERE id = '$escaped_id'";

$result = mysqli_query($con, $query);

if ($row = mysqli_fetch_assoc($result)) {

    $points = round($row['total_hours_volunteered']);

}

mysqli_close($con);

//testing the area above

 

$xp_levels = [50, 100, 500, 1000];

$current_level = 0;

$next_level = 50;

 

foreach ($xp_levels as $level) {

    if ($points < $level) {

        $next_level = $level;

        break;

    }

    $current_level = $level;

}

 

$progress = ($points - $current_level) / ($next_level - $current_level);

$progress_percent = min(100, max(0, round($progress * 100)));

?>

<!DOCTYPE html>

<html>

<head>

    <?php require_once('universal.inc'); ?>

    <link rel="stylesheet" href="css/base.css" type="text/css" />

    <title>Fredericksburg SPCA | XP Milestones</title>

    <style>

        .badge-row {

            display: flex;

            justify-content: center;

            flex-wrap: wrap;

            gap: 50px;

            margin: 20px 0 40px;

        }

 

        .badge {

            display: flex;

            flex-direction: column;

            align-items: center;

            opacity: 0.3;

            transition: opacity 0.3s ease;

        }

 

        .badge.unlocked {

            opacity: 1;

        }

 

        .badge img {

            height: 90px;

        }

 

        .badge-label {

            margin-top: 8px;

            font-size: 16px;

            font-weight: 600;

        }

 

        .xp-bar-container {

            background-color: #ddd;

            width: 100%;

            height: 25px;

            border-radius: 10px;

            overflow: hidden;

            margin-top: 5px;

        }

 

        .xp-bar-fill {

            height: 100%;

            background-color: #c92a7f;

            transition: width 0.5s ease-in-out;

        }

 

        .xp-info {

            margin-bottom: 30px;

        }

 

        .field-pair {

            margin-bottom: 15px;

        }

 

        .button {

            margin-top: 30px;

        }

    </style>

</head>

<body>

<?php require_once('header.php'); ?>

 

<h1>Your XP Milestones</h1>

<main class="dashboard">

 

    <!-- BADGES -->

    <div class="badge-row">

        <?php foreach ($xp_levels as $badge):

            $unlocked = $points >= $badge;

            $imgPath = "images/{$badge}.png";

        ?>

            <div class="badge <?php echo $unlocked ? 'unlocked' : ''; ?>">

                <img src="<?php echo $imgPath; ?>" alt="<?php echo $badge; ?> Point Badge">

                <div class="badge-label"><?php echo $badge; ?> pts</div>

            </div>

        <?php endforeach; ?>

    </div>

 

    <!-- XP INFO -->

    <div class="xp-info">

        <div class="field-pair">

            <label><strong>Total Points:</strong></label>

            <p><?php echo $points ?> point<?php echo ($points === 1 ? '' : 's') ?></p>

        </div>

 

        <div class="field-pair">

            <label><strong>Current Level:</strong></label>

            <p>Level <?php echo $current_level ?> → Goal: <?php echo $next_level ?> points</p>

        </div>

 

        <div class="field-pair">

            <label><strong>Progress Toward Next Level:</strong></label>

            <div class="xp-bar-container">

                <div class="xp-bar-fill" style="width: <?php echo $progress_percent ?>%;"></div>

            </div>

            <small><?php echo $points ?> / <?php echo $next_level ?> points</small>

        </div>

    </div>

 

    <a class="button" href="viewProfile.php">Back to Profile</a>

</main>

</body>

</html>