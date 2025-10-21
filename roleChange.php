<?php
    session_cache_expire(30);
    session_start();

    include 'database/dbPersons.php';

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;

    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }  

	if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 2) {
    	header('Location: login.php');
    	die();
	}

include 'infoBox.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Select Your Role</title>
    <link href="css/normal_tw.css" rel="stylesheet">
</head>
<body>

<header class="hero-header">
    <div class="center-header">
        <h1>Select Your Identity</h1>
    </div>
</header>

<main class="w-[90%] max-w-6xl mx-auto">
    <?php if (isset($_SESSION['access_level'])): ?>
    <div style="display:none" id="debug-access">
        Access level: <?= htmlspecialchars($_SESSION['access_level']) ?>
    </div>
    <?php endif; ?>

    <div class="main-content-box w-full p-8">
        <div class="text-center mb-8">
            <h2>Find Your Name to Update Your Role</h2>
            <p class="sub-text">Start typing your full name below.</p>
        </div>

        <div class="space-y-6">
            <input type="text" id="search-box" placeholder="Search by name..." class="form-input w-full">

            <div class="overflow-x-auto">
                <table class="w-full" id="results-table">
                    <thead style="color: var(--text-color); background-color: var(--main-color, #e8c4b8);">
                        <tr>
                            <th class="text-left p-2">Name</th>
                            <th class="text-left p-2">Role</th>
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
            Use this tool to select your name and gain access to your specific privileges.
        </p>
    </div>
</main>

<script>
// Intercept all link clicks
document.addEventListener('click', function (e) {
    if (e.target.tagName === 'A') {
        e.preventDefault(); // Stop the normal link behavior
        const userConfirmed = confirm('You will be logged out. Are you sure you want to continue?');
        if (userConfirmed) {
            window.location.href = 'logout.php'; // Force to your page
        }
        // else: do nothing, stay on page
    }
});

// Intercept back/forward navigation
window.addEventListener('popstate', function (e) {
    const userConfirmed = confirm('You will be logged out. Are you sure you want to continue?');
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

    fetch(`getUsersByName.php?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            let resultsList = document.getElementById("search-results");
            resultsList.innerHTML = ""; // Clear previous

            data.forEach(user => {
                let row = document.createElement("tr");

                let fullnameCell = document.createElement("td");
                fullnameCell.className = "p-2";
                fullnameCell.textContent = user.fullname;

                let roleCell = document.createElement("td");
                roleCell.className = "p-2";
                roleCell.textContent = user.role;

                let actionCell = document.createElement("td");
                actionCell.className = "p-2";

                let form = document.createElement("form");
                form.method = "POST";
                form.action = "processRoleChange.php";

                let input = document.createElement("input");
                input.type = "hidden";
                input.name = "person_id";
                input.value = user.person_id;

                let button = document.createElement("button");
                button.type = "submit";
                button.className = "blue-button";
                button.textContent = "Select";

                form.appendChild(input);
                form.appendChild(button);
                actionCell.appendChild(form);

                row.appendChild(fullnameCell);
                row.appendChild(roleCell);
                row.appendChild(actionCell);

                resultsList.appendChild(row);
            });
        })
        .catch(error => console.error('Error fetching results:', error));
});

</script>

</body>
</html>