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

if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] == 0) {
    header('Location: login.php');
    die();
}

include 'infoBox.php';

$boardMembers = getBoardMembers();
foreach ($boardMembers as &$bm) {
    $bm['role'] = 'Board Member';
}
unset($bm);

$coordinators = getVolunteerCoordinators();
foreach ($coordinators as &$c) {
    $c['role'] = 'Volunteer Coordinator';
}
unset($c);

$allCoordinators = array_merge($boardMembers, $coordinators);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Select Your Role</title>
    <link href="css/normal_tw.css" rel="stylesheet">
    <style>
        /* This ensures column widths are respected when scrolling */
        #results-table {
            table-layout: fixed;
            max-width: 680px;
        }

        /* Set a max height and enable vertical scrolling for the container */
        .scrollable-table-container {
            max-height: 400px; /* Adjust this value for desired fixed height */
            overflow-y: auto;
            border: 1px solid var(--border-color, #ccc); /* Optional: Adds a border around the scroll area */
        }
    </style>
</head>
<body>

<header class="hero-header">
    <div class="center-header">
        <h1>Select Your Identity</h1>
    </div>
</header>

<main class="w-[90%] max-w-6xl mx-auto">
    <?php if (isset($_SESSION['access_level'])) : ?>
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

            <div class="scrollable-table-container">
                <table class="w-full" id="results-table">
                    <thead style="color: var(--text-color); background-color: var(--main-color, #e8c4b8);">
                        <tr>
                            <th class="text-left p-2 w-[40%]">Name</th>
                            <th class="text-left p-2 w-[40%]">Role</th>
                            <th class="text-left p-2 w-[20%]">Action</th>
                        </tr>
                    </thead>
                    <tbody id="search-results">
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
const allCoordinatorData = <?php echo json_encode($allCoordinators); ?>;

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
    
function renderUsers(data) {
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

        let input1 = document.createElement("input");
        input1.type = "hidden";
        input1.name = "person_id";
        input1.value = user.person_id;

        let input2 = document.createElement("input");
        input2.type = "hidden";
        input2.name = "role";
        input2.value = user.role;

        let button = document.createElement("button");
        button.type = "submit";
        button.className = "blue-button";
        button.textContent = "Select";

        form.appendChild(input1);
        form.appendChild(input2);
        form.appendChild(button);
        actionCell.appendChild(form);

        row.appendChild(fullnameCell);
        row.appendChild(roleCell);
        row.appendChild(actionCell);

        resultsList.appendChild(row);
    });
}

document.getElementById("search-box").addEventListener("input", function () {
    let query = this.value.trim();
    if (!query || query.length === 0) {
        renderUsers(allCoordinatorData);
        return;
    }

    const filtered = allCoordinatorData.filter(user =>
        user.fullname.toLowerCase().includes(query.toLowerCase())
    );
    renderUsers(filtered);
});

window.addEventListener('DOMContentLoaded', function() {
    renderUsers(allCoordinatorData);
})

</script>

</body>
</html>