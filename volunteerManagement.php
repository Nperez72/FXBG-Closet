<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}
    // admin-only access
if ($accessLevel < 2) {
    header('Location: index.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Volunteer Management | FXBG Pride</title>
  <link href="css/modern-management.css" rel="stylesheet">
  <link href="css/mobile-nav.css" rel="stylesheet">

<!-- BANDAID FIX FOR HEADER BEING WEIRD -->
<?php
$tailwind_mode = true;
require_once('header.php');
?>
<!-- BANDAID END, REMOVE ONCE SOME GENIUS FIXES -->

</head>

<body class="management-page">

  <section class="management-hero">
    <div class="management-hero-content">
      <h1>Volunteer Management</h1>
      <p class="management-hero-subtitle">
        Manage volunteers, track participation, and recognize outstanding contributions to your organization
      </p>
    </div>
  </section>

  <!-- Main Content -->
  <main class="management-grid">
    <div class="card-grid">
      <!-- Register Volunteer Card -->
      <a href="VolunteerRegister.php" class="action-card">
        <div class="card-icon-wrapper">
          <img src="images/add-person.svg" alt="Register Icon">
        </div>
        <div class="action-card-content">
          <h3 class="action-card-title">Register Volunteer</h3>
          <p class="action-card-description">
            Add new volunteers to the system and set up their profiles
          </p>
        </div>
        <div class="action-card-footer">
          <span class="card-arrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </span>
        </div>
      </a>

      <a href="personSearch.php" class="action-card">
        <div class="card-icon-wrapper">
          <img src="images/person-search.svg" alt="Search Icon">
        </div>
        <div class="action-card-content">
          <h3 class="action-card-title">Search Volunteers</h3>
          <p class="action-card-description">
            Find and manage volunteer information quickly
          </p>
        </div>
        <div class="action-card-footer">
          <span class="card-arrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </span>
        </div>
      </a>
      <a href="checkedInVolunteers.php" class="action-card">
        <div class="card-icon-wrapper">
          <img src="images/clipboard-regular.svg" alt="Check-In Icon">
        </div>
        <div class="action-card-content">
          <h3 class="action-card-title">Checked-In Volunteers</h3>
          <p class="action-card-description">
            View currently active volunteers and manage check-ins
          </p>
        </div>
        <div class="action-card-footer">
          <span class="action-card-badge">Live</span>
          <span class="card-arrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </span>
        </div>
      </a>

      <a href="selectVOTM.php" class="action-card">
        <div class="card-icon-wrapper">
          <img src="images/star-icon.svg" alt="Star Icon">
        </div>
        <div class="action-card-content">
          <h3 class="action-card-title">Volunteer of the Month</h3>
          <p class="action-card-description">
            Select and recognize outstanding volunteer contributions
          </p>
        </div>
        <div class="action-card-footer">
          <span class="card-arrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </span>
        </div>
      </a>

      <a href="leaderboard.php" class="action-card">
        <div class="card-icon-wrapper">
          <img src="images/crown.svg.png" alt="Crown Icon">
        </div>
        <div class="action-card-content">
          <h3 class="action-card-title">Leaderboard</h3>
          <p class="action-card-description">
            View volunteer rankings and top contributors
          </p>
        </div>
        <div class="action-card-footer">
          <span class="card-arrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </span>
        </div>
      </a>
    </div>

    <section class="management-info">
      <div class="info-card">
        <h3 class="info-card-title">About Volunteer Management</h3>
        <p class="info-card-text">
          This hub provides comprehensive tools to manage your volunteer workforce. Register new volunteers, 
          track their participation, monitor active check-ins, and recognize outstanding contributions. 
          All the tools you need to build and maintain a thriving volunteer community.
        </p>
      </div>
    </section>

    <div class="return-button-wrapper">
      <a href="index.php" class="return-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Return to Dashboard
      </a>
    </div>
  </main>
</body>
</html>

