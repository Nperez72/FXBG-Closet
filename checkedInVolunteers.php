<?php
session_cache_expire(30);
session_start();
$loggedIn = false;
$accessLevel = 0;
$userID = null;
if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}
if ($accessLevel < 5) {
    header('Location: index.php');
    die();
}
include_once "database/dbPersons.php";
include_once "database/dbShifts.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FXBG Closet | Checked In Volunteers</title>
    <script src="js/theme-presets-init.js"></script>
    <link href="css/normal_tw.css" rel="stylesheet">
    <link rel="stylesheet" href="css/accessibility-settings.css">
    <link rel="stylesheet" href="css/theme-presets.css">
<?php
$tailwind_mode = true;
require_once('header.php');
?>
<style>
    /* Mobile responsive improvements */
    @media (max-width: 768px) {
        .hero-header {
            height: calc(var(--spacing) * 25) !important;
        }
        
        main {
            padding-inline: calc(var(--spacing) * 2) !important;
        }
        
        .main-content-box {
            padding: 1rem !important;
        }
        
        table {
            font-size: 14px;
        }
        
        th, td {
            padding-inline: calc(var(--spacing) * 2) !important;
            padding-block: calc(var(--spacing) * 2) !important;
        }
        
        .blue-button {
            padding-inline: calc(var(--spacing) * 3) !important;
            padding-block: calc(var(--spacing) * 1.5) !important;
            font-size: 14px;
        }
        
        #bulk-actions {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        #bulk-actions span {
            margin-bottom: 0.5rem;
        }
    }
    
    /* Ensure checkboxes are visible and properly sized */
    input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    @media (max-width: 640px) {
        input[type="checkbox"] {
            width: 20px;
            height: 20px;
        }
    }
</style>
</head>
<body>

    <div class="hero-header">
        <div class="center-header">
            <h1>View Checked In Volunteers</h1>
        </div>
    </div>

    <main>
        <div class="main-content-box w-full max-w-3xl p-6">

            <div class="flex justify-end mb-4">
                <div id="bulk-actions" class="hidden space-x-4">
                    <span class="font-semibold">With Selected:</span>
                    <button type="button" onclick="bulkClockOut()" class="blue-button">Check-Out</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll" class="w-4 h-4"></th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Check-In Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $date = date('Y-m-d');
                        $checkedInPersons = [];
                        $all_volunteers = getall_volunteers();

                        foreach ($all_volunteers as $volunteer) {
                            $volunteer_id = $volunteer->get_id();
                            $shift_id = get_open_shift($volunteer_id, $date);
                            if ($shift_id) {
                                $check_in_info = get_checkin_info_from_shift_id($shift_id);
                                $checkedInPersons[] = $check_in_info;
                            }
                        }

                        if (empty($checkedInPersons)) {
                            echo "<tr><td colspan='5' class='text-center py-6'>No volunteers are currently checked in.</td></tr>";
                        } else {
                            foreach ($checkedInPersons as $check_in_info) {
                                $volunteer = retrieve_person($check_in_info['person_id']);
                                if ($volunteer) {
                                    $full_name = $volunteer->get_first_name() . " " . $volunteer->get_last_name();
                                    $rowValue = htmlspecialchars($check_in_info['shift_id']) . '|' . htmlspecialchars($full_name);

                                    echo "<tr>";
                                    echo "<td><input type='checkbox' class='rowCheckbox w-4 h-4' value='$rowValue'></td>";
                                    echo "<td>" . htmlspecialchars($volunteer->get_first_name()) . "</td>";
                                    echo "<td>" . htmlspecialchars($volunteer->get_last_name()) . "</td>";
                                    echo "<td>" . htmlspecialchars($check_in_info['startTime']) . "</td>";
                                    echo "<td><button type='button' onclick=\"clockOut('{$check_in_info['shift_id']}')\" class='blue-button'>Check-Out</button></td>";
                                    echo "</tr>";
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>


        </div>

            <div class="flex justify-center mt-6">
                <a href="index.php" class="return-button">Return to Dashboard</a>
            </div>
    <div class="info-section">
        <div class="blue-div"></div>
        <p class="info-text">
            Use this tool to filter and search for volunteers or participants by their role, event involvement, and status. Mailing list support is built in.
        </p>
    </div>
    </main>

    <script>
        function bulkClockOut() {
            const selectedCheckboxes = document.querySelectorAll('.rowCheckbox:checked');
            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one volunteer to clock out.');
                return;
            }

            const description = prompt("Please enter a work description for the selected volunteers:");
            if (!description) return;

            const shiftIds = Array.from(selectedCheckboxes).map(cb => cb.value.split('|')[0]);
            const formData = new FormData();
            formData.append('description', description);
            shiftIds.forEach(id => formData.append('shift_ids[]', id));

            fetch('clockOutBulk.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Selected volunteers successfully clocked out!');
                    location.reload();
                } else {
                    alert('Failed to clock out: ' + data.error);
                }
            })
            .catch(error => {
                alert('Error occurred: ' + error.message);
            });
        }

        function clockOut(shiftID) {
            const description = prompt("Please enter a description for clocking out:");
            if (description !== null && description.trim() !== "") {
                fetch('clockOut.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `shiftID=${encodeURIComponent(shiftID)}&description=${encodeURIComponent(description)}`
                })
                .then(response => response.text())
                .then(data => {
                    alert("Clocked out successfully!");
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("There was an error clocking out.");
                });
            } else {
                alert("Clock out cancelled. Description is required.");
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.rowCheckbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                toggleBulkActions();
            });

            document.querySelectorAll('.rowCheckbox').forEach(cb => {
                cb.addEventListener('change', toggleBulkActions);
            });

            function toggleBulkActions() {
                const anyChecked = [...document.querySelectorAll('.rowCheckbox')].some(cb => cb.checked);
                document.getElementById('bulk-actions').style.display = anyChecked ? 'flex' : 'none';
            }
        });
    </script>
    <script src="js/accessibility-settings.js"></script>
    <script src="js/theme-presets.js"></script>

</body>
</html>

