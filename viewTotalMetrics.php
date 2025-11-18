<?php
    session_start();
    require_once('database/dbActivity.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>FXBG Pride | Volunteer Metrics</title>
    <link href="css/normal_tw.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
        margin-top: 30px;
    }
</style>
</head>
<body>

<header class="hero-header"> 
    <div class="center-header">
        <h1>View Total Volunteer Hours</h1>
    </div>
</header>

<main>
  <div class="main-content-box w-full max-w-5xl p-8 mb-8">
    <h2 class="mb-4">Total Volunteer Hours</h2>
    <p class="mb-4">Weekly aggregated volunteer hours across all volunteers.</p>
    
    <div class="chart-container">
        <canvas id="hoursChart"></canvas>
    </div>
  </div>
</main>

<?php
    $end_date = date('Y-m-d');
    $start_date = date('Y-m-d', strtotime('-3 months'));

    $weekly_data = get_weekly_volunteer_hours($start_date, $end_date);

    $labels = array();
    $data = array();

foreach ($weekly_data as $week) {
    $labels[] = $week['week_start'];
    $data[] = $week['total_hours'];
}

    $labels_json = json_encode($labels);
    $data_json = json_encode($data);
?>

<script>
    const ctx = document.getElementById('hoursChart').getContext('2d');
    const hoursChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $labels_json; ?>,
            datasets: [{
                label: 'Total Volunteer Hours',
                data: <?php echo $data_json; ?>,
                backgroundColor: 'rgba(41, 72, 119, 0.8)',
                borderColor: 'rgba(41, 72, 119, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    min: 1,
                    title: {
                        display: true,
                        text: 'Hours'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Week Starting'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Hours: ' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>
