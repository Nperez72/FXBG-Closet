<?php
session_cache_expire(30);
session_start();

ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once('include/input-validation.php');
require_once('database/dbEvents.php');
require_once('database/dbPersons.php');
require_once('database/dbEventCoordinators.php');
require_once('database/dbEventReports.php');

$errors = '';

// Check if user is logged in and has access (admin or assigned coordinator)
if (!isset($_SESSION['_id']) || $_SESSION['access_level'] < 3) {
    header('Location: login.php');
    die();
}

$accessLevel = $_SESSION['access_level'];
$userID = $_SESSION['_id'];

$args = sanitize($_GET);
if (!isset($args['id'])) {
    echo "Missing event ID";
    die();
}

$id = $args['id'];
$event = fetch_event_by_id($id);
if (!$event) {
    echo "Event not found";
    die();
}

// Check if coordinator is assigned (for accessLevel 3)
$coordinator_ids = get_event_coordinators($id);
$isAssignedCoordinator = in_array($_SESSION['person_id'] ?? 0, $coordinator_ids);

if ($accessLevel == 3 && !$isAssignedCoordinator) {
    echo "Unauthorized access";
    die();
}

// Fetch existing report if it exists
$existingReport = fetch_event_report($id);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post = sanitize($_POST, null);

    $required = ['event_id', 'total_attendance'];
    if (!wereRequiredFieldsSubmitted($post, $required)) {
        $errors .= "<p>Required fields missing.</p>";
    }

    if (!$errors) {
        // Prepare report data
        $reportData = [
            'event_id' => $post['event_id'],
            'total_attendance' => intval($post['total_attendance']),
            'age_under_18' => intval($post['age_under_18'] ?? 0),
            'age_18_24' => intval($post['age_18_24'] ?? 0),
            'age_25_34' => intval($post['age_25_34'] ?? 0),
            'age_35_44' => intval($post['age_35_44'] ?? 0),
            'age_45_54' => intval($post['age_45_54'] ?? 0),
            'age_55_64' => intval($post['age_55_64'] ?? 0),
            'age_65_plus' => intval($post['age_65_plus'] ?? 0),
            'ethnicity_american_indian' => intval($post['ethnicity_american_indian'] ?? 0),
            'ethnicity_asian' => intval($post['ethnicity_asian'] ?? 0),
            'ethnicity_black' => intval($post['ethnicity_black'] ?? 0),
            'ethnicity_hispanic' => intval($post['ethnicity_hispanic'] ?? 0),
            'ethnicity_pacific_islander' => intval($post['ethnicity_pacific_islander'] ?? 0),
            'ethnicity_white' => intval($post['ethnicity_white'] ?? 0),
            'event_cost' => floatval($post['event_cost'] ?? 0),
            'reimbursement_cost' => floatval($post['reimbursement_cost'] ?? 0),
        ];

        $success = add_event_report($reportData);
        if (!$success) {
            echo "Error submitting report!";
            die();
        }

        header('Location: event.php?id=' . $id . '&editSuccess');
        die();
    }
}

require_once('include/output.php');
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc'); ?>
    <title>Fredericksburg SPCA | Complete Event Report</title>
</head>
<body>
<?php require_once('header.php'); ?>

<h1>Complete Event Report</h1>
<main class="date">
    <?php if ($errors) : ?>
        <div class="error-toast"><?php echo $errors; ?></div>
    <?php endif; ?>

    <form id="new-event-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="event_id" value="<?= htmlspecialchars($id) ?>">

        <h3>* Total Attendance</h3>
        <input type="number" id="total-attendance" name="total_attendance" required
               value="<?= htmlspecialchars($existingReport['total_attendance'] ?? '') ?>"
               placeholder="Enter total attendance number">

        <h3>Age Ranges</h3>
        <?php
        $ageRanges = [
            'age_under_18' => 'Under 18',
            'age_18_24' => '18–24',
            'age_25_34' => '25–34',
            'age_35_44' => '35–44',
            'age_45_54' => '45–54',
            'age_55_64' => '55–64',
            'age_65_plus' => '65 or older'
        ];
        foreach ($ageRanges as $key => $label) {
            $value = $existingReport[$key] ?? 0;
            echo "<label for='$key'>$label</label>";
            echo "<input type='number' id='$key' name='$key' min='0' value='" . htmlspecialchars($value) . "'>";
        }
        ?>

        <h3>Ethnicity</h3>
        <?php
        $ethnicities = [
            'ethnicity_american_indian' => 'American Indian or Alaska Native',
            'ethnicity_asian' => 'Asian',
            'ethnicity_black' => 'Black or African American',
            'ethnicity_hispanic' => 'Hispanic or Latino',
            'ethnicity_pacific_islander' => 'Native Hawaiian or Other Pacific Islander',
            'ethnicity_white' => 'White'
        ];
        foreach ($ethnicities as $key => $label) {
            $value = $existingReport[$key] ?? 0;
            echo "<label for='$key'>$label</label>";
            echo "<input type='number' id='$key' name='$key' min='0' value='" . htmlspecialchars($value) . "'>";
        }
        ?>

        <h3>Financials</h3>
        <label for="event-cost">* Event Cost</label>
        <input type="number" step="0.01" id="event-cost" name="event_cost" required
               value="<?= htmlspecialchars($existingReport['event_cost'] ?? '') ?>"
               placeholder="Enter event cost">

        <label for="reimbursement-cost">Reimbursement Cost (If Needed)</label>
        <input type="number" step="0.01" id="reimbursement-cost" name="reimbursement_cost"
               value="<?= htmlspecialchars($existingReport['reimbursement_cost'] ?? '') ?>"
               placeholder="Enter reimbursement cost">

        <input type="submit" value="Submit Report">
        <a class="button cancel" href="event.php?id=<?= htmlspecialchars($id) ?>">Cancel</a>
    </form>
</main>
</body>
</html>
