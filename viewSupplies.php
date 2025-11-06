<?php
    session_start();
    require_once('database/dbSupplies.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>FXBG Pride | View Supply Requests</title>
    <link href="css/normal_tw.css" rel="stylesheet">
<?php
$tailwind_mode = true;
require_once('header.php');
?>
<style>
    .date-box {
        background: #274471;
        padding: 7px 30px;
        border-radius: 50px;
        box-shadow: -4px 4px 4px rgba(0, 0, 0, 0.25) inset;
        color: white;
        font-size: 24px;
        font-weight: 700;
        text-align: center;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #294877;
        color: white;
        font-weight: bold;
    }
    tr:hover {
        background-color: #f5f5f5;
    }
</style>
</head>
<body>

<header class="hero-header"> 
    <div class="center-header">
        <h1>Material Supply Requests</h1>
    </div>
</header>

<main>
  <div class="main-content-box w-full max-w-5xl p-8 mb-8">
    <h2 class="mb-4">All Supply Requests</h2>
    
    <?php
        $supplies = get_all_supply_requests();

    if (empty($supplies)) {
        echo '<p>No supply requests found.</p>';
    } else {
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Date Submitted</th>';
        echo '<th>Item Type</th>';
        echo '<th>Quantity</th>';
        echo '<th>Description</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($supplies as $supply) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($supply['date_submitted']) . '</td>';
            echo '<td>' . htmlspecialchars($supply['item_type']) . '</td>';
            echo '<td>' . htmlspecialchars($supply['quantity']) . '</td>';
            echo '<td>' . htmlspecialchars($supply['description']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }
    ?>
  </div>
</main>

</body>
</html>
