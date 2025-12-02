<?php
session_start();

define('MAX_UPLOAD_FILES', 10);
define('MAX_FILE_SIZE_MB', 10);
define('MAX_TOTAL_SIZE_MB', 50);
define('DOCUMENTATION_EMAIL', 'mhenry.fxbgpride@gmail.com');

require_once('include/input-validation.php');
require_once('include/flash.php');  
require_once('include/api.php');  
require_once('database/dbActivity.php');
require_once('database/dbEvents.php');
require_once('email.php');
require_once('utilities/FileUploader.php');

if (!isset($_SESSION['_id'])) {
    redirect("login.php");
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

        if ($event_id <= 0) throw new Exception("Please select a valid event.");
        if ($hours_spent <= 0) throw new Exception("Hours spent must be greater than 0.");
        if ($person_name === '') throw new Exception("Name field cannot be empty.");

        $email = isset($args['email']) && !empty($args['email']) ? $args['email'] : null;
        $date = get_event_date_by_id($event_id);
        $new_activity_id = add_activity($person_name, $role, $date, $hours_spent, $event_id, $email);

        if (!$new_activity_id) {
            throw new Exception("Database error: Failed to log activity.");
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
        } else if ($emailResults['sent_count'] < $emailResults['total_count']) {
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
        if(!empty($savedFiles)) {
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
