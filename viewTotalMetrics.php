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
        <h1>Volunteer Hours Metrics</h1>
    </div>
</header>
<main>
<?php
    // Define date range (last 6 months)
    $end_date = date('Y-m-d');
    $start_date = date('Y-m-d', strtotime('-6 months'));

    // Get selected email filter (if any)
    $selected_email = isset($_GET['email_filter']) && !empty($_GET['email_filter']) ? $_GET['email_filter'] : 'all';

    // Fetch monthly data based on filter
if ($selected_email === 'all') {
    $monthly_data = get_monthly_volunteer_hours($start_date, $end_date);
} else {
    $monthly_data = get_monthly_volunteer_hours_by_email($start_date, $end_date, $selected_email);
}

    // Get all unique emails for dropdown
    $all_emails = get_all_activity_emails();

    $labels = array();
    $data = array();

foreach ($monthly_data as $month) {
    // Use MIN(date) result for labels, formatted as "Nov 2025"
    $labels[] = date('M Y', strtotime($month['month_start']));
    $data[] = $month['total_hours'];
}

    $labels_json = json_encode($labels);
    $data_json = json_encode($data);
?>
  <div class="main-content-box w-full max-w-5xl p-8 mb-8">
    <h2 class="mb-4">Total Volunteer Hours (Last 6 Months)</h2>
    <p class="mb-4">Monthly aggregated volunteer hours across all volunteers.</p>
    
    <form method="get" style="margin-bottom: 20px;">
        <label for="email_filter" style="font-weight: bold; margin-right: 10px;">Filter by Email:</label>
        <input list="email_list" id="email_filter" name="email_filter" 
               value="<?php echo ($selected_email === 'all') ? 'all' : htmlspecialchars($selected_email); ?>" 
               placeholder="Type 'all' or select email..."
               style="padding: 8px; border: 1px solid #ccc; border-radius: 5px; width: 300px;">
        <datalist id="email_list">
            <option value="all">All Volunteers</option>
            <?php foreach ($all_emails as $email) : ?>
                <?php if (!empty($email)) : ?>
                    <option value="<?php echo htmlspecialchars($email); ?>">
                <?php endif; ?>
            <?php endforeach; ?>
        </datalist>
        <button type="submit" style="padding: 8px 16px; background-color: #294877; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Filter</button>
    </form>
    
    <div class="chart-container">
        <canvas id="hoursChart"></canvas>
    </div>
    
    <?php
        $total_hours = 0;
    foreach ($monthly_data as $month) {
        $total_hours += $month['total_hours'];
    }
    ?>
    
    <div style="text-align: center; margin-top: 30px; font-size: 24px; font-weight: bold; color: #294877;">
        Total Hours (6 Months): <?php echo number_format($total_hours, 2); ?>
    </div>
  </div>
</main>
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
                    min : 1,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hours'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Month'
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