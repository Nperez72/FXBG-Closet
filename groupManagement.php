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
if ($accessLevel < 4) {
    header('Location: index.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Group Management | FXBG Pride</title>
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
      <h1>Group Management</h1>
      <p class="management-hero-subtitle">
        Manage your leadership team and coordinate roles across the organization
      </p>
    </div>
  </section>

  <main class="management-grid">
    <div class="card-grid">
      <a href="manageBoardMembers.php" class="action-card">
        <div class="card-icon-wrapper">
          <img src="images/group.svg" alt="Board Members Icon">
        </div>
        <div class="action-card-content">
          <h3 class="action-card-title">Manage Board Members</h3>
          <p class="action-card-description">
            View and update the list of authorized board members
          </p>
        </div>
        <div class="action-card-footer">
          <span class="action-card-badge">Leadership</span>
          <span class="card-arrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </span>
        </div>
      </a>

      <a href="manageVolunteerCoordinators.php" class="action-card">
        <div class="card-icon-wrapper">
          <img src="images/group.svg" alt="Coordinators Icon">
        </div>
        <div class="action-card-content">
          <h3 class="action-card-title">Manage Volunteer Coordinators</h3>
          <p class="action-card-description">
            View and update the list of authorized volunteer coordinators
          </p>
        </div>
        <div class="action-card-footer">
          <span class="action-card-badge">Coordinators</span>
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
        <h3 class="info-card-title">About Group Management</h3>
        <p class="info-card-text">
          Manage your organization's leadership structure and coordinate roles effectively. 
          This hub allows you to maintain lists of authorized board members and volunteer 
          coordinators, ensuring smooth operations and clear communication channels across 
          your organization.
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

