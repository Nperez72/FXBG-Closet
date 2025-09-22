<?php
// Show errors while developing
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('header.php');
require_once('universal.inc');
require_once('database/dbPersons.php');

if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
    header('Location: index.php');
    exit;
}

$con = connect();
$isAdmin = $_SESSION['access_level'] >= 2;

// STEP 1: Get top 10 volunteers with most hours
$query = "
    SELECT id, first_name, last_name, total_hours_volunteered
    FROM dbpersons
    WHERE total_hours_volunteered > 0
    ORDER BY total_hours_volunteered DESC
    LIMIT 10
";

$result = mysqli_query($con, $query);
if (!$result) {
    die("❌ Error fetching top volunteers: " . mysqli_error($con));
}

$topVolunteers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $topVolunteers[] = $row;
}

// STEP 2: If admin, update VOTM to be first person in list
if ($isAdmin && !empty($topVolunteers)) {
    $winner = $topVolunteers[0];
    $winner_id = $winner['id'];

    // Clear previous VOTM
    mysqli_query($con, "UPDATE dbpersons SET volunteer_of_the_month = 0");

    // Set new VOTM
    mysqli_query($con, "
        UPDATE dbpersons 
        SET volunteer_of_the_month = 1, votm_awarded_month = CURDATE()
        WHERE id = '$winner_id'
    ");
} else {
    // STEP 3: Otherwise just look up current VOTM
    $winner_query = mysqli_query($con, "
        SELECT id, first_name, last_name, total_hours_volunteered 
        FROM dbpersons 
        WHERE volunteer_of_the_month = 1 
        LIMIT 1
    ");

    if (!$winner_query) {
        die("❌ Error checking current VOTM: " . mysqli_error($con));
    }

    $winner = mysqli_fetch_assoc($winner_query);
    
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Volunteer of the Month</title>
    <style>
        .votm-container {
            max-width: 800px;
            margin: 80px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .votm-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .votm-table th, .votm-table td {
            border: 1px solid #ccc;
            padding: 10px;
        }
        .votm-table th {
            background-color: #f2f2f2;
        }
        .winner-highlight {
            background-color: #fff9c4;
            font-weight: bold;
        }
        .back-button {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background-color: #004f71;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #00374f;
        }
    </style>
</head>
<body>
    <div class="votm-container">
        <h1 class="title">🏆 Volunteer of the Month</h1>

        <?php if (!empty($winner)): ?>
            <div class="successbox" style="margin-bottom: 20px;">
                <p>
                    Congratulations to <strong><?= htmlspecialchars($winner['first_name'] . ' ' . $winner['last_name']) ?></strong> 
                    for volunteering the most hours this month with 
                    <strong><?= number_format($winner['total_hours_volunteered'], 2) ?></strong> hours!
                </p>
            </div>
        <?php else: ?>
            <div class="error" style="margin-bottom: 20px; color: red;">
                ⚠️ No Volunteer of the Month has been selected yet.
            </div>
        <?php endif; ?>

        <?php if (!empty($topVolunteers)): ?>
            <h2 style="font-size: 1.4em;">📊 Top Volunteers Leaderboard</h2>
            <table class="votm-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Volunteer</th>
                        <th>Total Hours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topVolunteers as $index => $v): ?>
                        <?php
                            $isWinner = isset($winner['id']) && $v['id'] === $winner['id'];
                            $rowClass = $isWinner ? 'winner-highlight' : '';
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($v['first_name'] . ' ' . $v['last_name']) ?></td>
                            <td><?= number_format($v['total_hours_volunteered'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="index.php" class="back-button">← Back to Dashboard</a>
    </div>
</body>
</html>
