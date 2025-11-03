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
    require_once('database/dbEvents.php');

    $showPopup = false;
    $popupMessage = '';
    $popupType = 'success';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ignoreList = array();
    $args = sanitize($_POST, $ignoreList);

    $required = array(
        'event_id',
        'hours_spent',
        'activity_description'
    );

        $errors = false;

    if (!wereRequiredFieldsSubmitted($args, $required)) {
        $errors = true;
        $popupMessage = 'Please fill out all required fields.';
        $popupType = 'error';
    }



    $event_id = isset($args['event_id']) ? (int)$args['event_id'] : 0;
    if ($event_id <= 0) {
        echo "<p>Invalid event selected.</p>";
        $errors = true;
        $popupMessage = 'Please select a valid event.';
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
    // were photos uploaded?
    if (isset($_FILES["activity_images"]) && !$errors) {
        // try to filter out non-images
        $allowed = array("jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        // loop based on how many images
	for ($x = 0; $x < count($_FILES["activity_images"]["name"]); $x++) {
	    if (is_uploaded_file($_FILES["activity_images"]["tmp_name"][$x])) {
                $fname = basename($_FILES["activity_images"]["name"][$x]);
                $fname = strtolower($fname);
                $ftemp = $_FILES["activity_images"]["tmp_name"][$x];
                $ftype = mime_content_type($ftemp);
                $fsize = $_FILES["activity_images"]["size"][$x];
                

            // only allow the above MIME types
		// TODO security could be better
		
                if (in_array($ftype, $allowed)) {
                    // does it already exist?
                    if (file_exists("uploads/" . $person_id . "_" . $event_id . "_" . $date . "_" . $fname)) {
                        $errors = true;
                        $popupMessage = $fname . " already exists.";
                        break;
                    } else {
                        // max image size in bytes is 10MB
                        $maxsize = 10 * 1024 * 1024;
                        $destination = "uploads/" . $person_id . "_" . $event_id . "_" . $date . "_" . $fname . ".jpeg";
                        // temporary file destination
                        $image = null;
                // enforce file size
                        if ($fsize > $maxsize) {
                            $showPopup = true;
                            $popupMessage = 'Failed to upload photo. Max size is 10MB.';
                            $popupType = 'error';
                            break;
                        }
                // compress images larger than 5MB
                        if ($fsize > $maxsize / 2) {
                            // tmp imagecreate
                                $image = match ($ftype) {
                                    "image/jpeg" => imagecreatefromjpeg($ftemp),
                                    "image/gif" => imagecreatefromgif($ftemp),
                                    "image/png" => imagecreatefrompng($ftemp),
                                };
                    //compress
                        }
                        // move it to the uploads folder.
                        // format: person_id_event_id_date_fname
                        if (imagejpeg($image, $destination)) {
                            $fresult = add_media(null, $event_id, basename($fname), $ftype, $ext, $activity_description, basename($ftemp), $date);
                            if (!$fresult) {
                                $showPopup = true;
                                $popupMessage = 'Failed to upload photo. Please try again.';
                                $popupType = 'error';
                                break;
                            }
                        } else {
                            $errors = true;
                            $showPopup = true;
                            $popupMessage = 'Failed to upload file: ' . htmlspecialchars($fname) . '. Please try again.';
                            $popupType = 'error';
                            break;
                        }
                    }
                } else {
                    $errors = true;
                    // something went wrong with the photo.
                    $popupMessage = "Error: " . $_FILES["activity_images"]["name"][$x];
                    $popupType = 'error';
                    break;
		}
	    }
        }
    }

    if ($errors) {
        echo '<p class="error">Your form submission contained unexpected or invalid input.</p>';
        $showPopup = true;
    } else {
         $result = add_activity($person_id, $date, $event_id, $hours_spent, $activity_description);

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

<?php if ($showPopup) : ?>
<div id="popupMessage" class="absolute left-[40%] top-[20%] z-50 <?php echo $popupType === 'success' ? 'bg-green-600' : 'bg-red-800'; ?> p-4 text-green rounded-xl text-xl shadow-lg">
    <?php echo htmlspecialchars($popupMessage); ?>
</div>
<?php endif; ?>

<script>
</script>

</body>
</html>
