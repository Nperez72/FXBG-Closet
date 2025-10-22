<?php
    require_once('include/input-validation.php');
    session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('database/dbMessages.php'); ?>
    <title>FXBG Pride | Track Activities</title>
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
    .dropdown {
        padding-right: 50px;
    }
</style>
</head>
<body class="relative">
<?php
    require_once('database/dbActivity.php');

    $showPopup = false;
    $popupMessage = '';
    $popupType = 'success';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $ignoreList = array();
        $args = sanitize($_POST, $ignoreList);

        $required = array(
            'event_name',
            'hours_spent',
            'activity_description'
        );

        $errors = false;

        if (!wereRequiredFieldsSubmitted($args, $required)) {
            $errors = true;
            $popupMessage = 'Please fill out all required fields.';
            $popupType = 'error';
        }

        $hours_spent = isset($args['hours_spent']) ? (float)$args['hours_spent'] : 0;
        if ($hours_spent <= 0) {
            echo "<p>Invalid hours spent.</p>";
            $errors = true;
            $popupMessage = 'Hours spent must be greater than 0.';
            $popupType = 'error';
        }

        $activity_description = $args['activity_description'];
        $person_id = $_SESSION['_id'];
        $date = date("Y-m-d");

        if ($errors) {
            echo '<p class="error">Your form submission contained unexpected or invalid input.</p>';
            $showPopup = true;
        } else {
            $result = add_activity($person_id, $date, $hours_spent, $activity_description);
            
            if (!$result) {
                $showPopup = true;
                $popupMessage = 'Failed to log activity. Please try again.';
                $popupType = 'error';
            } else {
                $showPopup = true;
                $popupMessage = 'Activity logged successfully!';
                $popupType = 'success';
            }
        }
    } else {
        require_once('activityForm.php');
    }
?>

<?php if ($showPopup): ?>
<div id="popupMessage" class="absolute left-[40%] top-[20%] z-50 <?php echo $popupType === 'success' ? 'bg-green-600' : 'bg-red-800'; ?> p-4 text-green rounded-xl text-xl shadow-lg">
    <?php echo htmlspecialchars($popupMessage); ?>
</div>
<?php endif; ?>

<script>
</script>

</body>
</html>