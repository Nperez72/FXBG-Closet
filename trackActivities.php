<?php
session_start();

define('DOCUMENTATION_EMAIL', 'mhenry.fxbgpride@gmail.com');

require_once('include/input-validation.php');
require_once('include/flash.php');
require_once('include/api.php');
require_once('database/dbActivity.php');
require_once('database/dbEvents.php');
require_once('email.php');
require_once('utilities/FileUploader.php');
require_once('database/dbPersons.php');

if (!isset($_SESSION['_id'])) {
    redirect("login.php");
}

$allowed_access_levels = [1, 2, 3, 4];
if (!in_array($_SESSION['access_level'], $allowed_access_levels)) {
    redirect('index.php');
}

$role_names = [
   -1 => 'coordinator not specified',
    0 => 'not logged in',
    1 => 'volunteer',
    2 => 'board member',
    3 => 'volunteer coordinator',
    4 => 'admin'
];
$role = $role_names[$_SESSION['access_level']] ?? 'not logged in';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $args = sanitize($_POST);
    $required = ['event_id','hours_spent','person_name'];
    $savedFiles = [];

    try {
        if (!wereRequiredFieldsSubmitted($args, $required)) {
            throw new Exception("Please fill out all required fields.");
        }

        $event_id = isset($args['event_id']) ? (int)$args['event_id'] : 0;
        $hours_spent = isset($args['hours_spent']) ? (float)$args['hours_spent'] : 0;
        $person_name = trim($args['person_name'] ?? '');

        if ($event_id <= 0) {
            throw new Exception("Please select a valid event.");
        }
        if ($hours_spent <= 0) {
            throw new Exception("Hours spent must be greater than 0.");
        }
        if ($person_name === '') {
            throw new Exception("Name field cannot be empty.");
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

            $ageTotal = array_sum($age_data);
            $ethnicityTotal = array_sum($ethnicity_data);
            if ($ageTotal !== $ethnicityTotal) {
                throw new Exception("Age and ethnicity counts must match. Age total: {$ageTotal}, Ethnicity total: {$ethnicityTotal}");
            }
        }

        $email = isset($args['email']) && !empty($args['email']) ? $args['email'] : null;
        $date = get_event_date_by_id($event_id);
        $new_activity_id = add_activity($person_name, $role, $date, $hours_spent, $event_id, $email);

        if (!$new_activity_id) {
            throw new Exception("Database error: Failed to log activity.");
        }

        if ($role === 'volunteer' || $role === 'board member') {
            $demographicsSaved = save_interaction_demographics($new_activity_id, $age_data, $ethnicity_data);
            if (!$demographicsSaved) {
                throw new Exception("Failed to save demographic information for this activity.");
            }
        }

        $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
        $files = $_FILES['activity_images'] ?? null;
        if (!$files || empty(array_filter((array)$files['name']))) {
            set_flash('success', ['Activity logged successfully!']);
            redirect('trackActivities.php');
        }

        // FILE UPLOADING
        $uploader = new FileUploader($uploadDir);
        $savedFiles = $uploader->upload($files, "activity_{$new_activity_id}");

        // Loop through saved files and add them to database
        $uploadErrors = [];
        foreach ($savedFiles as $f) {
            $uploadOk = add_media(null, $new_activity_id, $f['name'], $f['mime'], $f['ext'], null, $f['saved'], $date);
            if (!$uploadOk) {
                $uploadErrors[] = $f['name'];
            }
        }

        if (!empty($uploadErrors)) {
            $filesList = implode(', ', $uploadErrors);
            throw new Exception("Activity created, but failed to save media attachments for the following files: {$filesList}");
        }

        // Email photos to designated contact if any were uploaded
        $eventName = get_event_name_by_id($event_id) ?? "Unknown Event";
        $subject = "Activity Documentation: {$eventName}";
        $photoList = implode(', ', array_column($savedFiles, 'name'));
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
            [DOCUMENTATION_EMAIL],
            'documentation-system',
            $subject,
            $body,
            $attachmentPaths
        );

        if (!$emailResults['success']) {
            // None sent
            set_flash('error', [
                "Activity logged successfully.",
                "However, all email deliveries failed.",
            ]);
            redirect('trackActivities.php');
        } elseif ($emailResults['sent_count'] < $emailResults['total_count']) {
            // Partial success
            $sent = $emailResults['sent_count'];
            $total = $emailResults['total_count'];
            $photoWord = $photoCount === 1 ? 'photo' : 'photos';
            set_flash('warning', [
                "Activity logged successfully, but some emails failed to send.",
                "Sent {$photoCount} {$photoWord} to {$sent} of {$total} contacts."
            ]);
            redirect('trackActivities.php');
        } else {
            // Full success
            $photoWord = $photoCount === 1 ? 'photo' : 'photos';
            set_flash('success', [
                "Activity logged successfully!",
                "Sent {$photoCount} {$photoWord} to the documentation contact."
            ]);
            redirect('trackActivities.php');
        }
    } catch (Exception $error) {
        if (!empty($savedFiles)) {
            foreach ($savedFiles as $file) {
                if (isset($file['path']) && is_file($file['path'])) {
                    unlink($file['path']);
                }
            }
        }
        set_flash('error', [$error->getMessage()]);
        redirect('trackActivities.php');
    }
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
    .flash-card.warning { 
        border-left: 4px solid #fbbf24;
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
