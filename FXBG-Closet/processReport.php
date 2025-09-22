<?php
session_cache_expire(30);
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);

if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 2) {
    header('Location: login.php');
    die();
}

require_once('database/dbPersons.php');

// 👉 Add month completeness check function
function is_month_complete($dateFrom) {
    $lastDayOfMonth = date("Y-m-t", strtotime($dateFrom));
    $today = date("Y-m-d");
    return $today > $lastDayOfMonth;
}

// Get user input
$reportType = $_POST['reportType'] ?? 'monthly';
$month = $_POST['month'] ?? '';
$format = $_POST['format'] ?? 'csv';

$currentMonth = date("m");
$currentYear = date("Y");
$fiscalYearStart = ($currentMonth >= 10) ? $currentYear : $currentYear - 1;
$fiscalYearEnd = $fiscalYearStart + 1;

// Define Fiscal Year Months
$fiscalMonths = [
    "10" => "October $fiscalYearStart", "11" => "November $fiscalYearStart", "12" => "December $fiscalYearStart",
    "01" => "January $fiscalYearEnd", "02" => "February $fiscalYearEnd", "03" => "March $fiscalYearEnd",
    "04" => "April $fiscalYearEnd", "05" => "May $fiscalYearEnd", "06" => "June $fiscalYearEnd",
    "07" => "July $fiscalYearEnd", "08" => "August $fiscalYearEnd", "09" => "September $fiscalYearEnd"
];

// Define Quarters
$quarters = [
    "Quarter 1" => ["10", "11", "12"],
    "Quarter 2" => ["01", "02", "03"],
    "Quarter 3" => ["04", "05", "06"],
    "Quarter 4" => ["07", "08", "09"]
];

// Update new volunteer status before fetching report data
update_new_volunteer_status();

// Fetch Data
$reportData = [];
if ($reportType === "monthly" && isset($fiscalMonths[$month])) {
    $monthName = $fiscalMonths[$month];
    $dateFrom = ($month >= 10) ? "$fiscalYearStart-$month-01" : "$fiscalYearEnd-$month-01";

    // ✅ Check if month is complete
    if (!is_month_complete($dateFrom)) {
        echo "<script>alert('The selected month is not yet complete. Please try again later.'); window.history.back();</script>";
        exit();
    }

    $dateTo = date("Y-m-t", strtotime($dateFrom));

    $reportData[$monthName] = [
        "total_volunteers" => get_total_volunteers_count($dateTo),
        "new_volunteers" => get_new_volunteers_count($dateFrom, $dateTo),
        "new_dog_walkers" => get_new_dog_walkers_count($dateFrom, $dateTo),
        "group_volunteers" => get_group_volunteers_count($dateFrom, $dateTo),
        "community_service_volunteers" => get_community_service_volunteers_count($dateFrom, $dateTo),
        "total_volunteer_hours" => get_total_vol_hours($dateFrom, $dateTo)
    ];
} else {
    // Fetch for Full Fiscal Year (Annual Report)
    foreach ($fiscalMonths as $monthNum => $monthName) {
        $dateFrom = ($monthNum >= 10) ? "$fiscalYearStart-$monthNum-01" : "$fiscalYearEnd-$monthNum-01";
        $dateTo = date("Y-m-t", strtotime($dateFrom));

        $reportData[$monthName] = [
            "total_volunteers" => get_total_volunteers_count($dateTo),
            "new_volunteers" => get_new_volunteers_count($dateFrom, $dateTo),
            "new_dog_walkers" => get_new_dog_walkers_count($dateFrom, $dateTo),
            "group_volunteers" => get_group_volunteers_count($dateFrom, $dateTo),
            "community_service_volunteers" => get_community_service_volunteers_count($dateFrom, $dateTo),
            "total_volunteer_hours" => get_total_vol_hours($dateFrom, $dateTo)
        ];
    }
}

// CSV EXPORT
if ($format === 'csv') {
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=volunteer_report_{$reportType}_fy{$fiscalYearEnd}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    $output = fopen('php://output', 'w');
    fputcsv($output, ["Volunteer Report - " . ucfirst($reportType) . " FY{$fiscalYearEnd}"]);

    // Column Headers
    fputcsv($output, ["Month", "Total Volunteers", "New Volunteers", "New Dog Walkers", "Group Volunteers", "Community Service Volunteers", "Total Volunteer Hours"]);

    // Data
    foreach ($reportData as $month => $data) {
        fputcsv($output, [
            $month,
            $data["total_volunteers"],
            $data["new_volunteers"],
            $data["new_dog_walkers"],
            $data["group_volunteers"],
            $data["community_service_volunteers"],
            $data["total_volunteer_hours"]
        ]);
    }

    fclose($output);
    exit();
}

// EXCEL EXPORT
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=volunteer_report_{$reportType}_fy{$fiscalYearEnd}.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<html><head><meta charset='UTF-8'></head><body>";
echo "<table border='1' style='border-collapse: collapse; font-family: Arial, sans-serif; text-align: center;'>";

// Report Title
echo "<tr><th colspan='7' style='font-size: 18px; background-color: #004488; color: white; padding: 10px;'>Volunteer Report - " . ucfirst($reportType) . " FY{$fiscalYearEnd}</th></tr>";

// Column Headers
echo "<tr>
        <th style='background-color: #88CCEE; padding: 5px;'>Month</th>
        <th style='background-color: #AA4499; padding: 5px;'>Total Volunteers</th>
        <th style='background-color: #DDCC77; padding: 5px;'>New Volunteers</th>
        <th style='background-color: #88CCEE; padding: 5px;'>New Dog Walkers</th>
        <th style='background-color: #AA4499; padding: 5px;'>Group Volunteers</th>
        <th style='background-color: #DDCC77; padding: 5px;'>Community Service Volunteers</th>
        <th style='background-color: #88CCEE; padding: 5px;'>Total Volunteer Hours</th>
      </tr>";

// Data Rows
foreach ($reportData as $month => $data) {
    echo "<tr>
            <td style='background-color: #EAEAEA; padding: 5px; text-align: center;'>$month</td>
            <td style='padding: 5px;'>{$data["total_volunteers"]}</td>
            <td style='padding: 5px;'>{$data["new_volunteers"]}</td>
            <td style='padding: 5px;'>{$data["new_dog_walkers"]}</td>
            <td style='padding: 5px;'>{$data["group_volunteers"]}</td>
            <td style='padding: 5px;'>{$data["community_service_volunteers"]}</td>
            <td style='padding: 5px;'>{$data["total_volunteer_hours"]}</td>
          </tr>";
}

echo "</table>";
echo "</body></html>";
exit();
?>
