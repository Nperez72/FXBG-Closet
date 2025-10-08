<?php
    session_cache_expire(30);
    session_start();

    include 'database/dbShifts.php';
    include 'database/dbPersons.php';

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    $today = date('Y-m-d');

    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }  

	if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 2) {
    		header('Location: login.php');
    		die();
	}

    if ($loggedIn) {
        $existingShift = get_shift_today($userID, $today);
    }

include 'infoBox.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Volunteer Check-In</title>
    <link href="css/normal_tw.css" rel="stylesheet">
</head>
<body>

<header class="hero-header">
    <div class="center-header">
        <h1>Volunteer Check-In</h1>
    </div>
</header>

<main>
    <div class="main-content-box w-[80%] p-8">
        <div class="text-center mb-8">
            <h2>Find Your Account to Check In/Out</h2>
            <p class="sub-text">Start typing your username below.</p>
        </div>

        <div class="space-y-6">
            <input type="text" id="search-box" placeholder="Search by Username..." class="form-input w-full">

            <div class="overflow-x-auto">
                <table class="w-full" id="results-table">
                    <thead class="bg-blue-400 text-white">
                        <tr>
                            <th class="text-left p-2">Username</th>
                            <th class="text-left p-2">Action</th>
                        </tr>
                    </thead>
                    <tbody id="search-results">
                        <!-- Results injected here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="blue-div"></div>
        <p class="info-text">
            Use this tool to find your volunteer account and securely check in or out.
        </p>
    </div>
</main>

<script>
// Intercept all link clicks
document.addEventListener('click', function (e) {
    if (e.target.tagName === 'A') {
        e.preventDefault(); // Stop the normal link behavior
        const userConfirmed = confirm('Only an admin should perform this action. Are you sure you want to continue?');
        if (userConfirmed) {
            window.location.href = 'logout.php'; // Force to your page
        }
        // else: do nothing, stay on page
    }
});

// Intercept back/forward navigation
window.addEventListener('popstate', function (e) {
    const userConfirmed = confirm('Only an admin should perform this action. Are you sure you want to continue?');
    if (userConfirmed) {
        window.location.href = 'logout.php'; // Force to your page
    } else {
        history.pushState(null, '', location.href); // Cancel back
    }
});

// Lock the current history state
window.history.pushState(null, '', window.location.href);


document.getElementById("search-box").addEventListener("input", function () {
    let query = this.value.trim();

    if (query.length < 1) {
        document.getElementById("search-results").innerHTML = ""; // Clear results
        return;
    }

    fetch(`getVolunteers.php?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            let resultsList = document.getElementById("search-results");
            resultsList.innerHTML = ""; // Clear previous

            data.forEach(username => {
                let row = document.createElement("tr");

                let usernameCell = document.createElement("td");
                usernameCell.className = "p-2";
                usernameCell.textContent = username;

                let actionCell = document.createElement("td");
                actionCell.className = "p-2";

                let form = document.createElement("form");
                form.method = "POST";
                form.action = "processCheckIn.php";

                let input = document.createElement("input");
                input.type = "hidden";
                input.name = "user_id";
                input.value = username;

                let button = document.createElement("button");
                button.type = "submit";
                button.className = "blue-button";
                button.textContent = "Check In/Out";

                form.appendChild(input);
                form.appendChild(button);
                actionCell.appendChild(form);

                row.appendChild(usernameCell);
                row.appendChild(actionCell);

                resultsList.appendChild(row);
            });
        })
        .catch(error => console.error('Error fetching results:', error));
});

</script>

</body>
</html>

