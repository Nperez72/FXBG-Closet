<?php
    require_once('include/input-validation.php');
    session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('database/dbMessages.php'); ?>
    <title>FXBG Pride | Closet Form</title>
    <link href="css/normal_tw.css" rel="stylesheet">
<?php
$tailwind_mode = true;
require_once('headerCloset.php');
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
    .dropdown {
        padding-right: 50px;
    }
</style>
</head>
<body class="relative">
<?php
    require_once('database/dbCloset.php');

    $showPopup = false;
    $popupMessage = '';
    $popupType = 'success';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ignoreList = array();
    $args = sanitize($_POST, $ignoreList);
    $required = array(
	'first_name',
	'last_name',
        'item_type',
        'quantity',
    );

    $errors = false;

    if (!wereRequiredFieldsSubmitted($args, $required)) {
        $errors = true;
        $popupMessage = 'Please fill out all required fields.';
        $popupType = 'error';
    }

    $first = $args['first_name'];
    $last = $args['last_name'];
    $item_type = $args['item_type'];

    $quantity = isset($args['quantity']) ? (int)$args['quantity'] : 0;
    if ($quantity <= 0) {
        echo "<p>Invalid quantity.</p>";
        $errors = true;
        $popupMessage = 'Quantity must be greater than 0.';
        $popupType = 'error';
    }

    $use_date = date("Y-m-d");

    if ($errors) {
        echo '<p class="error">Your form submission contained unexpected or invalid input.</p>';
        $showPopup = true;
    } else {
        $resultCloset = update_closet($first, $last, $use_date, $item_type, $quantity);
        $resultInv = update_closet_quantity($item_type, $quantity * -1);
	var_dump($resultCloset);
        if (!$resultCloset) {
            $showPopup = true;
            $popupMessage = 'Failed to submit closet use. Please try again.';
            $popupType = 'error';
        } else {
            $showPopup = true;
            $popupMessage = 'Closet use submitted successfully!';
            $popupType = 'success';
        }
        if (!$resultCloset) {
            $showPopup = true;
            $popupMessage = 'Failed to submit closet use. Please try again.';
            $popupType = 'error';
        } else {
            $showPopup = true;
            $popupMessage = 'Closet use submitted successfully!';
            $popupType = 'success';
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    require_once('closetForm.php');
}
?>

<?php if ($showPopup) : ?>
<div id="popupMessage" class="absolute left-[40%] top-[20%] z-50 <?php echo $popupType === 'success' ? 'bg-green-600' : 'bg-red-800'; ?> p-4 text-green rounded-xl text-xl shadow-lg">
    <?php echo htmlspecialchars($popupMessage); ?>
</div>
<?php endif; ?>

<script>
</script>

</body>
</html>
