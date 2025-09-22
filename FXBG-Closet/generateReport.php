<?php
session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

// Ensure admin authentication
if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 2) {
    header('Location: login.php');
    die();
}

// Get current fiscal year
$currentMonth = date("m");
$currentYear = date("Y");
$fiscalYearStart = ($currentMonth >= 10) ? $currentYear : $currentYear - 1;
$fiscalYearEnd = $fiscalYearStart + 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fredericksburg SPCA | Volunteer Reports</title>
  <link href="css/normal_tw.css" rel="stylesheet">

<!-- BANDAID FIX FOR HEADER BEING WEIRD -->
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

</style>
<!-- BANDAID END, REMOVE ONCE SOME GENIUS FIXES -->
</head>
<body>

    <!-- Hero Section with Title -->
    <header class="hero-header"> 
        <div class="center-header">
            <h1>Generate Volunteer Document</h1>
        </div>
    </header>

    <main>
        <div class="main-content-box w-full max-w-3xl p-8">
            <div class="text-center mb-8">
                <h2>Volunteer Reports</h2>
                <p class="sub-text">Fiscal Year: <?= $fiscalYearStart ?> - <?= $fiscalYearEnd ?></p>
            </div>

            <form method="POST" action="processReport.php" class="space-y-6">
                <!-- Report Type -->
                <div>
                    <label for="reportType" class="font-semibold">Select Report Type:</label>
                    <select name="reportType" id="reportType" onchange="toggleDateFields()">
                        <option value="monthly">Monthly</option>
                        <option value="annually">Annually</option>
                    </select>
                </div>

                <!-- Month (conditionally hidden) -->
                <div id="monthField">
                    <label for="month" class="font-semibold">Select Month:</label>
                    <select name="month" id="month">
                        <?php
                        $months = [
                            '10' => 'October', '11' => 'November', '12' => 'December', '01' => 'January',
                            '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May',
                            '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September'
                        ];
                        foreach ($months as $num => $name) {
                            echo "<option value='$num'>$name</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Format -->
                <div>
                    <label for="format" class="font-semibold">Select File Format:</label>
                    <select name="format" id="format">
                        <option value="excel">Excel (.xls)</option>
                        <option value="csv">CSV (.csv)</option>
                    </select>
                </div>

                <div class="text-center">
                    <input type="submit" value="Generate Report" class="blue-button">
                </div>
            </form>

        <!-- Return Button -->
        </div>
            <div class="text-center mt-6">
                <a href="index.php" class="return-button">Return to Dashboard</a>
            </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="blue-div"></div>
            <p class="info-text">
                Use this tool to generate monthly or annual reports on volunteer activity. Reports are available in Excel or CSV format.
            </p>
        </div>
    </main>

    <script>
        function toggleDateFields() {
            const reportType = document.getElementById("reportType").value;
            const monthField = document.getElementById("monthField");
            monthField.style.display = reportType === "annually" ? "none" : "block";
        }
        document.addEventListener("DOMContentLoaded", toggleDateFields);
    </script>
</body>
</html>

