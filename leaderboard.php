<?php

session_start();

 

if (!isset($_SESSION['_id']) || $_SESSION['access_level'] < 1) {

    header("Location: login.php");

    exit;

}

 

require_once('database/dbinfo.php');

$con = connect();

 

$query = "

    SELECT first_name, last_name, total_hours_volunteered

    FROM dbpersons

    WHERE id != 'vmsroot' AND status = 'Active' AND archived = 0

    ORDER BY total_hours_volunteered DESC

    LIMIT 10

";

$result = mysqli_query($con, $query);

 

$leaders = [];

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $leaders[] = $row;

    }

}

mysqli_close($con);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <?php require_once('universal.inc'); ?>

    <link rel="stylesheet" href="css/base.css" type="text/css" />

    <title>Fredericksburg SPCA | Leaderboard</title>

    <style>

        .leaderboard-container {

            max-width: 700px;

            margin: 40px auto;

            padding: 20px;

            background: white;

            border-radius: 12px;

            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);

        }

 

        h1 {

            text-align: center;

            margin-bottom: 25px;

        }

 

        table {

            width: 100%;

            border-collapse: collapse;

            margin-top: 10px;

        }

 

        th, td {

            padding: 12px;

            text-align: center;

            border-bottom: 1px solid #ddd;

        }

 

        th {

            background-color: #c92a7f;

            color: white;

        }

 

        tr:nth-child(even) {

            background-color: #f9f9f9;

        }

 

        tr:hover {

            background-color: #f1f1f1;

        }

 

        .button-back {

            display: block;

            width: fit-content;

            margin: 20px auto 0;

            padding: 10px 20px;

            background-color: #c92a7f;

            color: white;

            border: none;

            border-radius: 8px;

            text-decoration: none;

            font-weight: bold;

        }

 

        .button-back:hover {

            background-color: #a91d68;

        }

    </style>

</head>

<body>

<?php require_once('header.php'); ?>

 

<div class="leaderboard-container">

    <h1>🏆 Top Volunteers by Hours</h1>

    <table>

        <thead>

            <tr>

                <th>Rank</th>

                <th>Name</th>

                <th>Total Hours</th>

            </tr>

        </thead>

        <tbody>

            <?php foreach ($leaders as $index => $vol): ?>

                <tr>

                    <td><?= $index + 1 ?></td>

                    <td><?= htmlspecialchars($vol['first_name'] . ' ' . $vol['last_name']) ?></td>

                    <td><?= number_format($vol['total_hours_volunteered'], 2) ?></td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

    <a class="button-back" href="index.php">← Back to Dashboard</a>

</div>

 

</body>

</html>
