<?php
session_start();

define('MAX_UPLOAD_FILES', 10);
define('MAX_FILE_SIZE_MB', 10);
define('MAX_TOTAL_SIZE_MB', 50);

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
require_once('database/dbActivity.php');
require_once('database/dbEvents.php');
require_once('email.php');

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
        $errors[] = "Hours spent must be greater than 0.";
    }

    $activity_description = trim($args['activity_description'] ?? '');
    if ($activity_description === '') {
        set_flash('error', ['Activity description cannot be empty. Please try again.']);
        header('Location: trackActivities.php');
        die();
    }
    $person_id = (int)$_SESSION['_id'];
    $date = date("Y-m-d");

    // Create uploads directory if it doesn't exist
    // Permissions: owner can read/write/execute, others can read/execute
    $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $allowedMimes = ['image/jpeg', 'image/png'];

    // Guard for missing files array
    $files = $_FILES['activity_images'] ?? null;
    $fileCount = (is_array($files) && isset($files['name']) && is_array($files['name'])) ? count($files["name"]) : 0;

    if ($fileCount > MAX_UPLOAD_FILES) {
        set_flash('error', ["Too many files. Maximum " . MAX_UPLOAD_FILES . " photos allowed."]);
        header('Location: trackActivities.php');
        die();
    }

    $totalSize = 0;
    $limitPerFileBytes = MAX_FILE_SIZE_MB * 1024 * 1024;
    $totalLimitBytes = MAX_TOTAL_SIZE_MB * 1024 * 1024;

    for ($x = 0; $x < $fileCount; $x++) {
        if (!isset($files['size'][$x])) {
            continue;
        }

        $fsize = (int)$files['size'][$x];

        if ($fsize > $limitPerFileBytes) {
            $name = htmlspecialchars($files['name'][$x] ?? 'Unknown file');
            set_flash('error', ["File too large (" . MAX_FILE_SIZE_MB . "MB max): {$name}"]);
            header('Location: trackActivities.php');
            die();
        }

        if ($totalSize + $fsize > $totalLimitBytes) {
            set_flash('error', ["Total upload size exceeds " . MAX_TOTAL_SIZE_MB . "MB limit. Please reduce file count or sizes."]);
            header('Location: trackActivities.php');
            die();
        }

        $totalSize += $fsize;
    }

    $savedFiles = [];

    // loop based on how many images
    for ($x = 0; $x < $fileCount; $x++) {
        // Break the whole upload loop if any file has an error
        $err = $files['error'][$x] ?? UPLOAD_ERR_NO_FILE;

        // Skip empty files
        if ($err === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($err !== UPLOAD_ERR_OK) {
            $name = basename($files['name'][$x] ?? 'unknown');
            $msgMap = [
                UPLOAD_ERR_INI_SIZE   => 'exceeds php.ini upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE  => 'exceeds form MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL    => 'was only partially uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'missing temporary folder on server',
                UPLOAD_ERR_CANT_WRITE => 'failed to write to disk',
                UPLOAD_ERR_EXTENSION  => 'blocked by a PHP extension',
            ];
            $reason = $msgMap[$err] ?? 'unknown upload error';
            $errors[] = "Upload failed for {$name}: {$reason}";
            break; // stop processing remaining files
        }

        $originalName = basename($files["name"][$x]);
        $ftemp = $files["tmp_name"][$x];
        $fsize = $files["size"][$x];

        // Make sure file was actually uploaded from HTTP POST for security
        if (!is_uploaded_file($ftemp)) {
            $errors[] = "Invalid upload source: " . htmlspecialchars($originalName);
            continue;
        }

        $mime = strtolower(mime_content_type($ftemp) ?: '');
        // normalize aliases
        if ($mime === 'image/jpg' || $mime === 'image/pjpeg') {
            $mime = 'image/jpeg';
        }
        if ($mime === 'image/x-png') {
            $mime = 'image/png';
        }
        if (!in_array($mime, $allowedMimes, true)) {
            $errors[] = "Unsupported image type: " . htmlspecialchars($originalName);
            continue;
        }

        // Determine file extension from MIME type
        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
        };

        // Sanitize filename: remove special characters, keep alphanumeric, dots, underscores, hyphens
        $base = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $savedFileName = $person_id . "_" . $event_id . "_" . $date . "_" . $base . '.' . $ext;
        $destPath = $uploadDir . DIRECTORY_SEPARATOR . $savedFileName;

        // does it already exist?
        if (file_exists($destPath)) {
            $errors[] = "Failed to upload {$originalName}: file already exists.";
            continue;
        }

        $ok = false;
        // If image is small (<= 5MB), just move it without recompressing
        if ($fsize <= 5 * 1024 * 1024) {
            $ok = move_uploaded_file($ftemp, $destPath);
        } else {
            // For large files, compress them
            $img = match ($mime) {
                'image/jpeg' => @imagecreatefromjpeg($ftemp),
                'image/png'  => @imagecreatefrompng($ftemp),
                default      => null,
            };

            if ($img) {
                // Save in original format with compression
                // JPEG at 85% quality
                // PNG at level 6
                $ok = match ($mime) {
                    'image/jpeg' => imagejpeg($img, $destPath, 85),
                    'image/png'  => imagepng($img, $destPath, 6), // compression level 0-9
                    default      => false,
                };
                imagedestroy($img);
            } else {
                $errors[] = "Failed to create image: " . htmlspecialchars($originalName);
            }
        }

        // If image was successfully put in the destination path, add it to $savedFiles
        if ($ok) {
            $savedFiles[] = [
                'original' => $originalName,
                'saved' => $savedFileName,
                'path' => $destPath,
                'mime' => $mime,
                'ext' => $ext,
            ];
        } else {
            $errors[] = "Failed to save: " . htmlspecialchars($originalName);
        }
    }

    // If there are errors, cleanup saved files, and redirect
    if (!empty($errors)) {
        foreach ($savedFiles as $f) {
            @unlink($uploadDir . DIRECTORY_SEPARATOR . $f['saved']);
        }
        $errors[] = "Please try again.";
        set_flash('error', $errors);
        header('Location: trackActivities.php');
        die();
    }

    // If failed, store message and redirect
    $result = add_activity($person_id, $date, $event_id, $hours_spent, $activity_description);
    if (!$result) {
        // cleanup files if DB write fails
        foreach ($savedFiles as $f) {
            @unlink($uploadDir . DIRECTORY_SEPARATOR . $f['saved']);
        }
        set_flash('error', ['Failed to log activity. Please try again.']);
        header('Location: trackActivities.php');
        die();
    }

    // Loop through saved files and add them to database
    $uploadErrors = [];
    foreach ($savedFiles as $f) {
        $uploadOk = add_media(null, $event_id, $f['original'], $f['mime'], $f['ext'], $activity_description, $f['saved'], $date);
        if (!$uploadOk) {
            $uploadErrors[] = $f['original'];
        }
    }

    // If any media failed to save, cleanup files and show error
    if (!empty($uploadErrors)) {
        foreach ($savedFiles as $f) {
            @unlink($uploadDir . DIRECTORY_SEPARATOR . $f['saved']);
        }
        set_flash('error', [
            'Activity created but failed to save media attachments.',
            'Failed files: ' . implode(', ', $uploadErrors)
        ]);
        header('Location: trackActivities.php');
        die();
    }

    // Email photos to designated contact if any were uploaded
    if (!empty($savedFiles)) {
        $eventName = get_event_name_by_id($event_id) ?? "Unknown Event";

        $subject = "Activity Documentation: {$eventName}";

        $photoList = implode(', ', array_column($savedFiles, 'original'));
        $photoCount = count($savedFiles);

        $body = "VOLUNTEER ACTIVITY DOCUMENTATION\n\n";
        $body .= "Event: {$eventName}\n";
        $body .= "Date: {$date}\n";
        $body .= "Hours: {$hours_spent}\n\n";
        $body .= "Description:\n{$activity_description}\n\n";
        $body .= "Photos Attached: {$photoCount}\n";
        $body .= "Files: {$photoList}\n";

        $attachmentPaths = array_column($savedFiles, 'path');

        $emailResults = sendEmails(
            ['mhenry.fxbgpride@gmail.com'],
            'documentation-system',
            $subject,
            $body,
            $attachmentPaths
        );

        // Check for errors
        if (isset($emailResults['error'])) {
            error_log("Email attachment error: {$emailResults['error']}");
            set_flash('error', ['Activity logged successfully, but email failed to send.', $emailResults['error']]);
            header('Location: trackActivities.php');
            die();
        }

        if (!($emailResults['mhenry.fxbgpride@gmail.com'] ?? false)) {
            set_flash('error', ['Activity logged successfully, but email failed to send.', 'Please contact an administrator.']);
            header('Location: trackActivities.php');
            die();
        }

        set_flash('success', ['Activity logged successfully!']);
        header('Location: trackActivities.php');
        die();
    }

    // Success
    set_flash('success', ['Activity logged successfully!']);
    header('Location: trackActivities.php');
    die();
}

// read flash to get all messages
$flash = get_flash();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FXBG Closet | Track Activities</title>
    <script src="js/theme-presets-init.js"></script>
    <link href="css/normal_tw.css" rel="stylesheet">
    <link rel="stylesheet" href="css/accessibility-settings.css">
    <link rel="stylesheet" href="css/theme-presets.css">
<?php $tailwind_mode = true; ?>
<style>
    .flash-wrap { 
        max-width: 768px; 
        margin: 16px auto; 
        padding: 0 12px; 
    }
    .flash-card {
        background: var(--card-bg, #ffffff); 
        color: var(--text-color, #363434);
        border: 1px solid var(--border-color, #e8c4b8); 
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
    .flash-list { 
        margin: 0; 
        flex: 1;
        color: var(--text-color, #363434);
        list-style-position: inside;
    }
    .flash-close {
        margin-left: auto; 
        background: none; 
        border: 0; 
        color: var(--text-muted, #8a8280);
        font-size: 18px; 
        line-height: 1; 
        cursor: pointer;
        transition: color 0.2s ease;
        flex-shrink: 0;
    }
    .flash-close:hover { 
        color: var(--text-color, #363434); 
    }
    
    /* Mobile responsive improvements */
    @media (max-width: 768px) {
        .flash-wrap { 
            margin-top: 12px; 
            padding: 0 8px;
        }
        .flash-body { 
            padding: 10px 12px; 
        }
        
        .hero-header {
            height: calc(var(--spacing) * 25) !important;
        }
        
        main {
            padding-inline: calc(var(--spacing) * 2) !important;
            margin-top: calc(var(--spacing) * -6) !important;
        }
        
        .main-content-box {
            padding: 1rem !important;
        }
        
        h1 {
            font-size: 1.5rem !important;
            padding: 1rem !important;
        }
        
        label {
            font-size: 1rem !important;
        }
        
        input, select, textarea {
            font-size: 16px !important; /* Prevents zoom on iOS */
        }
        
        .blue-button, .return-button {
            width: 100%;
            padding: 0.75rem 1rem !important;
        }
    }
    
    @media (max-width: 480px) {
        .flash-wrap {
            padding: 0 4px;
        }
        
        .flash-list {
            font-size: 14px;
        }
    }
</style>
</head>
<body class="relative">
    <?php require_once('header.php'); ?>
    <?php require_once('activityForm.php'); ?>
    <script src="js/accessibility-settings.js"></script>
    <script src="js/theme-presets.js"></script>
</body>
</html>
