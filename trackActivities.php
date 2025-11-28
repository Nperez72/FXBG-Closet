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

$role_names = [
   -1 => 'coordinator not specified',
    0 => 'not logged in',
    1 => 'volunteer',
    2 => 'board member',
    3 => 'volunteer coordinator',
    4 => 'admin'
];
$role = $role_names[$_SESSION['access_level']] ?? 'not logged in';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ignoreList = array();
    $args = sanitize($_POST, $ignoreList);
    
    $required = array(
        'event_id',
        'hours_spent',
        'person_name'
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

    $person_name = trim($args['person_name'] ?? '');        
    if ($person_name === '') {
        set_flash('error', ['Name field cannot be empty. Please try again.']);
        header('Location: trackActivities.php');
        die();
    }

    $date = get_event_date_by_id($event_id);
    $email = isset($args['email']) && !empty($args['email']) ? $args['email'] : null;

    $new_activity_id = add_activity($person_name, $role, $date, $hours_spent, $event_id, $email);
    if (!$new_activity_id) {
        set_flash('error', ['Failed to log activity. Please try again.']);
        header('Location: trackActivities.php');
        die();
    }

    if ($role === 'volunteer' || $role === 'board member') {
        $age_data = [
            '0_12' => (int)($args['age_0_12'] ?? 0),
            '13_17' => (int)($args['age_13_17'] ?? 0),
            '18_24' => (int)($args['age_18_24'] ?? 0),
            '25_54' => (int)($args['age_25_54'] ?? 0),
            '55_plus' => (int)($args['age_55_plus'] ?? 0),
        ];
        
        $ethnicity_data = [
            'white' => (int)($args['ethnicity_white'] ?? 0),
            'black' => (int)($args['ethnicity_black'] ?? 0),
            'hispanic' => (int)($args['ethnicity_hispanic'] ?? 0),
            'asian' => (int)($args['ethnicity_asian'] ?? 0),
            'native' => (int)($args['ethnicity_native'] ?? 0),
            'other' => (int)($args['ethnicity_other'] ?? 0),
        ];
        
        save_interaction_demographics($new_activity_id, $age_data, $ethnicity_data);
    }

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
        $savedFileName = $event_id . "_" . $date . "_" . $base . '.' . $ext;
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
                unset($img);
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

    // Loop through saved files and add them to database
    $uploadErrors = [];
    foreach ($savedFiles as $f) {
        $uploadOk = add_media(null, $event_id, $f['original'], $f['mime'], $f['ext'], null, $f['saved'], $date);
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
        $body = render_email_template(
            __DIR__ . DIRECTORY_SEPARATOR . 'email_templates' . DIRECTORY_SEPARATOR . 'activity_documentation_email.php', 
            [
                'eventName'   => $eventName,
                'person_name' => $person_name,
                'role_title'  => ucwords($role),
                'date'        => $date,
                'hours_spent' => $hours_spent,
                'photoList'   => $photoList,
                'photoCount'  => $photoCount,
            ]
        ); 
        $attachmentPaths = array_column($savedFiles, 'path');

        $emailResults = sendEmails(
            ['mhenry.fxbgpride@gmail.com'],
            'documentation-system',
            $subject,
            $body,
            $attachmentPaths
        );

         // Check for errors
        if (!$emailResults['success']) {
            set_flash('error', ['Activity logged successfully, but email(s) failed to send.', $emailResults['error']]);
            header('Location: trackActivities.php');
            die();
        }
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
<html>
<head>
    <title>FXBG Pride | Track Activities</title>
    <link href="css/normal_tw.css" rel="stylesheet">
<?php $tailwind_mode = true; ?>
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
    <?php require_once('header.php'); ?>
    <?php require_once('activityForm.php'); ?>
</body>
</html>
