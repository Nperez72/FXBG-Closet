<?php
    session_start();

    // Flash helpers
function set_flash(string $type, array $messages): void
{
    $_SESSION['flash'] = ['type' => $type, 'messages' => $messages];
}

function get_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

if (!isset($_SESSION['_id'])) {
    header("Location: login.php");
    die();
}

    require_once('include/input-validation.php');
    require_once('database/dbMessages.php');
    require_once('database/dbActivity.php');
    require_once('database/dbEvents.php');

    $errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ignoreList = array();
    $args = sanitize($_POST, $ignoreList);
    $required = array(
        'event_id',
        'hours_spent',
        'activity_description'
    );

    if (!wereRequiredFieldsSubmitted($args, $required)) {
        $errors[] = "Please fill out all required fields.";
    }

    $event_id = isset($args['event_id']) ? (int)$args['event_id'] : 0;
    if ($event_id <= 0) {
        $errors[] = "Please select a valid event.";
    }

    $hours_spent = isset($args['hours_spent']) ? (float)$args['hours_spent'] : 0;
    if ($hours_spent <= 0) {
        $errors[] = "Hours spend must be greater than 0.";
    }

    $activity_description = $args['activity_description'];
    $person_id = $_SESSION['_id'];
    $date = date("Y-m-d");

    // Check if at least one file is uploaded
    // UPLOAD_ERR_OK means file was uploaded with no errors
    $hasUploads = false;
    if (isset($_FILES['activity_images'])) {
        foreach ($_FILES['activity_images']['error'] as $e) {
            if ($e === UPLOAD_ERR_OK) {
                $hasUploads = true;
                break;
            }
        }
    }

    if ($hasUploads) {
        // echo "<script>console.log(" . json_encode($_FILES["activity_images"]) . ");</script>";

        // try to filter out non-images
        $allowed = array("jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");

        // loop based on how many images
        for ($x = 0; $x < count($_FILES["activity_images"]["name"]); $x++) {
            $fname = basename($_FILES["activity_images"]["name"][$x]);
            $fname = strtolower($fname);
            $ftype = $_FILES["activity_images"]["type"][$x];
            $ftemp = $_FILES["activity_images"]["tmp_name"][$x];
            $fsize = $_FILES["activity_images"]["size"][$x];
            $ext = pathinfo($fname, PATHINFO_EXTENSION);

            // only allow the above file extensions
            // TODO security could be better
            if (!array_key_exists($ext, $allowed)) {
                $errors[] = 'Invalid photo file type.';
                break;
            }

            // only allow the above MIME types
            // TODO security could be better
            if (in_array($ftype, $allowed)) {
                // does it already exist?
                if (file_exists("uploads/" . $person_id . "_" . $event_id . "_" . $date . "_" . $fname)) {
                    $errors[] = "{$fname} already exists.";
                    break;
                } else {
                    // max image size in bytes is 10MB
                    $maxsize = 10 * 1024 * 1024;
                    $destination = "uploads/" . $person_id . "_" . $event_id . "_" . $date . "_" . $fname . ".jpeg";
                    // temporary file destination
                    $image = null;
                    // enforce file size
                    if ($fsize > $maxsize) {
                        $errors[] = "Failed to upload photo. Max size is 10MB";
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
                            $errors[] = "Failed to upload photo. Please try again.";
                            break;
                        }
                    } else {
                        $errors[] = "Failed to upload file: " . htmlspecialchars($fname) . " please try again";
                        break;
                    }
                }
            } else {
                $errors[] = "Error: " . $_FILES["activity_images"]["error"][$x];
                break;
            }
        }
    }

    // If there are errors, save them and redirect
    if (!empty($errors)) {
        set_flash('error', $errors);
        header('Location: trackActivities.php');
        die();
    }

    $result = add_activity($person_id, $date, $event_id, $hours_spent, $activity_description);

    // If failed, store message and redirect
    if (!$result) {
        set_flash('error', ['Failed to log activity. Please try again.']);
        header('Location: trackActivities.php');
        die();
    }

    // Success
    set_flash('success', ['Activity logged successfully!']);
    header('Location: trackActivities.php');
    die();
}

    // TEMP: demo flash messages (remove after preview)
if (isset($_GET['demo'])) {
    $type = (($_GET['type'] ?? 'error') === 'success') ? 'success' : 'error';
    $samples = [
        'Activity logged successfully!',
        'Uploaded 3 photos.',
        'Please fill out all required fields.',
        'File too large (10MB max): big.png',
        'Please select a valid event.',
        'Hours spent must be greater than 0.',
    ];
    shuffle($samples);
    $messages = array_slice($samples, 0, rand(1, 3));
    set_flash($type, $messages);
}
    // read flash to get all messages
    $flash = get_flash();
    require_once('activityForm.php');
?>

<!DOCTYPE html>
<html>
<head>
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
    .flash-wrap { 
        max-width: 768px; 
        margin: 16px auto; 
        padding: 0 12px; 
    }
    .flash-card {
        background: var(--card-bg); 
        color: var(--text-color);
        border: 1px solid var(--border-color); 
        border-radius: 8px;
        box-shadow: 0 4px 12px var(--card-shadow);
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }
    .flash-card.success { 
        border-left: 4px solid #34d399; 
    }
    .flash-card.error { 
        border-left: 4px solid #f87171; 
    }
    .flash-body { 
        display: flex; 
        gap: 12px; 
        padding: 12px 14px; 
        align-items: flex-start; 
    }
    .flash-dot.success { 
        background: #34d399; 
    }
    .flash-dot.error { 
        background: #f87171; 
    }
    .flash-list { 
        margin: 0; 
        flex: 1;
        color: var(--text-color);
    }
    .flash-close {
        margin-left: auto; 
        background: none; 
        border: 0; 
        color: var(--text-muted);
        font-size: 18px; 
        line-height: 1; 
        cursor: pointer;
        transition: color 0.2s ease;
        flex-shrink: 0;
    }
    .flash-close:hover { 
        color: var(--text-color); 
    }
    @media (max-width: 640px) {
        .flash-wrap { margin-top: 12px; }
        .flash-body { padding: 10px 12px; }
    }
</style>
</head>
<body class="relative">
</body>
</html>
