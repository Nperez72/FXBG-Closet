<?php
    session_start();
    require_once('database/dbSupplies.php');
    
    if (isset($_POST['toggle_status'])) {
        $supply_id = (int)$_POST['supply_id'];
        $current_status = $_POST['current_status'];
        $new_status = ($current_status === 'pending') ? 'fulfilled' : 'pending';
        
        if ($new_status === 'fulfilled') {
            update_supply_quantity($supply_id, 0);
        }
        
        toggle_supply_status($supply_id, $new_status);
        header("Location: viewSupplies.php");
        exit();
    }
    
    if (isset($_POST['update_quantity'])) {
        $supply_id = (int)$_POST['supply_id'];
        $new_quantity = (int)$_POST['quantity'];
        update_supply_quantity($supply_id, $new_quantity);
        header("Location: viewSupplies.php");
        exit();
    }
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
    .status-pending {
        background-color: #fef08a;
    }
    .status-fulfilled {
        background-color: #86efac;
    }
    .toggle-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }
    .toggle-btn:hover {
        opacity: 0.8;
    }
    .quantity-input {
        width: 60px;
        padding: 4px;
        border: 1px solid #ccc;
        border-radius: 3px;
        text-align: center;
    }
    .update-btn {
        padding: 4px 8px;
        background-color: #294877;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        margin-left: 5px;
    }
    .update-btn:hover {
        background-color: #1e3a5f;
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
            echo '<th>Status</th>';
            echo '<th>Action</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            foreach ($supplies as $supply) {
                $status_class = ($supply['status'] === 'fulfilled') ? 'status-fulfilled' : 'status-pending';
                $status_text = ucfirst($supply['status']);
                $button_text = ($supply['status'] === 'pending') ? 'Mark Fulfilled' : 'Mark Pending';
                
                echo '<tr class="' . $status_class . '">';
                echo '<td>' . htmlspecialchars($supply['date_submitted']) . '</td>';
                echo '<td>' . htmlspecialchars($supply['item_type']) . '</td>';
                echo '<td>' . htmlspecialchars($supply['quantity']) . '</td>';
                echo '<td>' . htmlspecialchars($supply['description']) . '</td>';
                echo '<td><strong>' . $status_text . '</strong></td>';
                echo '<td>';
                echo '<form method="post" style="display:inline;">';
                echo '<input type="hidden" name="supply_id" value="' . htmlspecialchars($supply['supply_id']) . '">';
                echo '<input type="hidden" name="current_status" value="' . htmlspecialchars($supply['status']) . '">';
                echo '<button type="submit" name="toggle_status" class="toggle-btn">' . $button_text . '</button>';
                echo '</form>';
                echo '</td>';
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