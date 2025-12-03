<?php
session_start();
require_once('database/dbEventReports.php');

// Only admins can view this page
if (!isset($_SESSION['_id']) || $_SESSION['access_level'] < 4) {
    header('Location: login.php');
    die();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>FXBG Pride | Event Attendance Metrics</title>
    <link href="css/normal_tw.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php require_once('header.php'); ?>

<style>
.chart-container {
    position: relative;
    height: 450px;
    width: 100%;
    margin-top: 40px;
}
.metrics-section {
    margin: 0 auto;
    max-width: 1200px;
    padding: 20px;
}
.filter-form {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 25px;
}
.filter-form input[type="date"] {
    width: 180px;
    padding: 6px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.filter-form button {
    padding: 8px 16px;
    background-color: #294877;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.filter-form button:hover {
    background-color: #3b5b99;
}
</style>
</head>

<body>
<header class="hero-header"> 
    <div class="center-header">
        <h1>Event Attendance Metrics</h1>
    </div>
</header>

<main class="metrics-section">
<?php
// Default date range: last 3 months
$end_date = date('Y-m-d');
$start_date = date('Y-m-d', strtotime('-3 months'));

// Override if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : $start_date;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : $end_date;
}

// Fetch all data
$events = get_attendance_per_event($start_date, $end_date);

$labels = [];
$total_data = [];
$age_data = [
    'Under 18' => [],
    '18–24' => [],
    '25–34' => [],
    '35–44' => [],
    '45–54' => [],
    '55–64' => [],
    '65+' => [],
];
$ethnicity_data = [
    'American Indian or Alaska Native' => [],
    'Asian' => [],
    'Black or African American' => [],
    'Hispanic or Latino' => [],
    'Native Hawaiian or Other Pacific Islander' => [],
    'White' => [],
];

foreach ($events as $event) {
    $date_str = date('M j, Y', strtotime($event['date']));
    $label = htmlspecialchars($event['name']) . " ($date_str)";
    $labels[] = $label;
    $total_data[] = intval($event['total_attendance']);

    // Age
    $age_data['Under 18'][] = intval($event['age_under_18']);
    $age_data['18–24'][] = intval($event['age_18_24']);
    $age_data['25–34'][] = intval($event['age_25_34']);
    $age_data['35–44'][] = intval($event['age_35_44']);
    $age_data['45–54'][] = intval($event['age_45_54']);
    $age_data['55–64'][] = intval($event['age_55_64']);
    $age_data['65+'][] = intval($event['age_65_plus']);

    // Ethnicity
    $ethnicity_data['American Indian or Alaska Native'][] = intval($event['ethnicity_american_indian']);
    $ethnicity_data['Asian'][] = intval($event['ethnicity_asian']);
    $ethnicity_data['Black or African American'][] = intval($event['ethnicity_black']);
    $ethnicity_data['Hispanic or Latino'][] = intval($event['ethnicity_hispanic']);
    $ethnicity_data['Native Hawaiian or Other Pacific Islander'][] = intval($event['ethnicity_pacific_islander']);
    $ethnicity_data['White'][] = intval($event['ethnicity_white']);
}

$labels_json = json_encode($labels);
$total_json = json_encode($total_data);
$age_json = json_encode($age_data);
$ethnicity_json = json_encode($ethnicity_data);
?>

<p class="text-center mb-6">Showing events with completed reports from <strong><?php echo $start_date; ?></strong> to <strong><?php echo $end_date; ?></strong>.</p>

<form method="post" class="filter-form">
    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
    <button type="submit">Filter</button>
</form>

<!-- Total Attendance Summary -->
<h3>Total Attendance (All Events): <?php echo array_sum($total_data); ?></h3>

<!-- Charts -->
<div class="chart-container">
    <canvas id="totalChart"></canvas>
</div>

<div class="chart-container">
    <canvas id="ageChart"></canvas>
</div>

<div class="chart-container">
    <canvas id="ethnicityChart"></canvas>
</div>

</main>

<script>
const labels = <?php echo $labels_json; ?>;
const totalData = <?php echo $total_json; ?>;
const ageData = <?php echo $age_json; ?>;
const ethnicityData = <?php echo $ethnicity_json; ?>;

const colorPalette = [
    'rgba(54, 162, 235, 0.8)',   // Blue
    'rgba(255, 99, 132, 0.8)',   // Red
    'rgba(255, 206, 86, 0.8)',   // Yellow
    'rgba(75, 192, 192, 0.8)',   // Teal
    'rgba(153, 102, 255, 0.8)',  // Purple
    'rgba(255, 159, 64, 0.8)',   // Orange
    'rgba(99, 255, 132, 0.8)'    // Green
];

function buildDatasets(dataObj) {
    const keys = Object.keys(dataObj);
    return keys.map((key, i) => ({
        label: key,
        data: dataObj[key],
        backgroundColor: colorPalette[i % colorPalette.length],
        borderColor: 'rgba(255, 255, 255, 0.8)',
        borderWidth: 1
    }));
}

// Total Attendance
new Chart(document.getElementById('totalChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Total Attendance',
            data: totalData,
            backgroundColor: colorPalette[0],
            borderColor: 'rgba(41, 72, 119, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: { display: true, text: 'Total Attendance per Event', font: { size: 22, weight: 'bold' } },
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Attendees' }
            },
            x: {
                ticks: { autoSkip: false }
            }
        }
    }
});

// Age
new Chart(document.getElementById('ageChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: buildDatasets(ageData)
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: { display: true, text: 'Attendance by Age Group', font: { size: 22, weight: 'bold' }},
            legend: { position: 'top' }
        },
        scales: {
            x: { stacked: true },
            y: { stacked: true, beginAtZero: true, title: { display: true, text: 'Attendees' } }
        }
    }
});

// Ethnicity
new Chart(document.getElementById('ethnicityChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: buildDatasets(ethnicityData)
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: { display: true, text: 'Attendance by Ethnicity', font: { size: 22, weight: 'bold' } },
            legend: { position: 'top' }
        },
        scales: {
            x: { stacked: true },
            y: { stacked: true, beginAtZero: true, title: { display: true, text: 'Attendees' } }
        }
    }
});
</script>
</body>
</html>
