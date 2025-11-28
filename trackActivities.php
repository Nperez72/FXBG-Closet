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
    
    echo '<script>console.log("User ID:", ' . json_encode($_SESSION['_id']) . ');</script>';
    echo '<script>console.log("Args:", ' . json_encode($args) . ');</script>';
    echo '<script>console.log("Role:", ' . json_encode($role) . ');</script>';
    
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

        // echo '<script>console.log("Args:", ' . json_encode($person_name) . ');</script>';
        // echo '<script>console.log("Args:", ' . json_encode($age_data) . ');</script>';
        // echo '<script>console.log("Args:", ' . json_encode($ethnicity_data) . ');</script>';
        
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
        $body = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
            <meta charset='UTF-8'>
            <meta name='x-apple-disable-message-reformatting'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Volunteer Activity Documentation</title>
            <style>
                @media only screen and (max-width:600px) {
                .container { width:100% !important; }
                .inner { padding:24px 20px !important; }
                .p-sm { padding:16px 8px !important; }
                .h1 { font-size:24px !important; line-height:30px !important; }
                .subtitle { font-size:15px !important; }
                .text { font-size:15px !important; line-height:22px !important; }
                .badge { padding:8px 14px !important; font-size:13px !important; }
                .row-label { display:block !important; width:100% !important; padding:16px 0 6px 0 !important; }
                .row-value { display:block !important; width:100% !important; padding:0 0 20px 0 !important; }
                .card { padding:18px !important; }
                }
            </style>
            </head>
            <body style='margin:0; padding:0; background:#f8f9fa; font-family:Arial, Helvetica, sans-serif;'>
            <div style='display:none; max-height:0; overflow:hidden; mso-hide:all;'>
                Activity submission received — {$eventName}
            </div>
            <center role='article' aria-roledescription='email' lang='en'>
                <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%' style='background:#f8f9fa;'>
                <tr>
                    <td align='center' class='p-sm' style='padding:32px 16px;'>
                    <!--[if mso]>
                    <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='600'>
                    <tr><td>
                    <![endif]-->
                    <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%' class='container' style='max-width:600px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.08);'>
                        
                        <!-- Rainbow header bar -->
                        <tr>
                        <td style='padding:0;'>
                            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0'>
                            <tr>
                                <td style='background:#e53935; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#fb8c00; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#fdd835; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#43a047; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#1e88e5; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#8e24aa; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                            </tr>
                            </table>
                        </td>
                        </tr>

                        <!-- Header -->
                        <tr>
                        <td align='center' style='padding:40px 28px 12px 28px; background:linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%); background-color:#f8f9fa;'>
                            <div style='display:inline-block; background:linear-gradient(135deg, #e53935 0%, #fb8c00 50%, #fdd835 100%); background-color:#fb8c00; color:#ffffff; padding:8px 16px; border-radius:20px; font-size:12px; font-weight:600; letter-spacing:0.5px; text-transform:uppercase; margin-bottom:16px;'>
                            Documentation
                            </div>
                            <h1 class='h1' style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:28px; line-height:36px; color:#1a1a1a; font-weight:700;'>
                            Volunteer Activity Recorded
                            </h1>
                            <p class='subtitle' style='margin:12px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:24px; color:#6b7280;'>
                            Your submission has been successfully received
                            </p>
                        </td>
                        </tr>

                        <!-- Content -->
                        <tr>
                        <td class='inner' style='padding:32px 28px;'>
                            
                            <!-- Card container -->
                            <div class='card' style='background:#f8f9fa; border-radius:12px; padding:24px; margin-bottom:24px;'>
                            <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%' style='border-collapse:collapse;'>
                                
                                <!-- Event -->
                                <tr>
                                <td class='row-label' valign='top' width='140' style='padding:0 20px 8px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                                    Event
                                </td>
                                <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:17px; line-height:26px; color:#111827; font-weight:600;'>
                                    {$eventName}
                                </td>
                                </tr>
                                
                                <!-- Spacer -->
                                <tr><td colspan='2' style='padding:12px 0;'></td></tr>
                                
                                <!-- Date -->
                                <tr>
                                <td class='row-label' valign='top' width='140' style='padding:0 20px 8px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                                    Date
                                </td>
                                <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#374151; font-weight:600;'>
                                    {$date}
                                </td>
                                </tr>
                                
                                <!-- Spacer -->
                                <tr><td colspan='2' style='padding:12px 0;'></td></tr>
                                
                                <!-- Hours -->
                                <tr>
                                <td class='row-label' valign='top' width='140' style='padding:0 20px 8px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                                    Hours Logged
                                </td>
                                <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#374151; font-weight:600;'>
                                    {$hours_spent} hours
                                </td>
                                </tr>
                                
                                <!-- Spacer -->
                                <tr><td colspan='2' style='padding:12px 0;'></td></tr>
                                
                                <!-- Description -->
                                <tr>
                                <td class='row-label' valign='top' width='140' style='padding:0 20px 8px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                                    Description
                                </td>
                                <td class='row-value' valign='top' style='padding:0 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#374151;'>
                                    {$activity_description}
                                </td>
                                </tr>
                                
                            </table>
                            </div>
                            
                            <!-- Attachments section -->
                            <table role='presentation' cellpadding='0' cellspacing='0' border='0' width='100%'>
                            <tr>
                                <td class='row-label' valign='top' width='140' style='padding:0 20px 12px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; font-weight:600;'>
                                Attachments
                                </td>
                                <td class='row-value' valign='top' style='padding:0 0 0 0;'>
                                <!-- Badge -->
                                <table role='presentation' cellpadding='0' cellspacing='0' border='0' style='margin-bottom:16px;'>
                                    <tr>
                                    <td class='badge' style='background:linear-gradient(135deg, #43a047 0%, #1e88e5 100%); background-color:#43a047; color:#ffffff; padding:10px 20px; font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:600; border-radius:10px; box-shadow:0 2px 8px rgba(67,160,71,0.2);'>
                                        {$photoCount} file(s)
                                    </td>
                                    </tr>
                                </table>
                                <!-- File list container -->
                                <div style='padding:20px; background:#ffffff; border:2px solid #e5e7eb; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.05);'>
                                    <div style='font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; color:#6b7280;'>
                                    {$photoList}
                                    </div>
                                </div>
                                </td>
                            </tr>
                            </table>
                            
                        </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                        <td align='center' style='padding:32px 28px; background:#f9fafb; border-top:1px solid #e5e7eb;'>
                            <p class='text' style='margin:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:20px; color:#9ca3af;'>
                            Automatically generated by the Documentation System
                            </p>
                            <p class='text' style='margin:6px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:18px; color:#d1d5db;'>
                            " . date('F j, Y \a\t g:i A') . "
                            </p>
                        </td>
                        </tr>

                        <!-- Rainbow footer bar -->
                        <tr>
                        <td style='padding:0;'>
                            <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0'>
                            <tr>
                                <td style='background:#e53935; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#fb8c00; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#fdd835; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#43a047; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#1e88e5; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                                <td style='background:#8e24aa; height:6px; font-size:0; line-height:0;'>&nbsp;</td>
                            </tr>
                            </table>
                        </td>
                        </tr>

                    </table>
                    <!--[if mso]></td></tr></table><![endif]-->
                    </td>
                </tr>
                </table>
            </center>
            </body>
            </html>
            ";

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
