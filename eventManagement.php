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
  <title>Event Management | FXBG Pride</title>
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
      <h1>Event Management</h1>
      <p class="management-hero-subtitle">
        Create, organize, and manage events and trainings for your volunteer community
      </p>
    </div>
  </section>

  <main class="management-grid">
    <div class="card-grid">
      <a href="addEvent.php" class="action-card">
        <div class="card-icon-wrapper">
          <img src="images/plus-solid.svg" alt="Create Event Icon">
        </div>
        <div class="action-card-content">
          <h3 class="action-card-title">Create Event</h3>
          <p class="action-card-description">
            Schedule new events and training sessions for volunteers
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

      <a href="viewAllEvents.php" class="action-card">
        <div class="card-icon-wrapper">
          <img src="images/new-event.svg" alt="View Events Icon">
        </div>
        <div class="action-card-content">
          <h3 class="action-card-title">View Events</h3>
          <p class="action-card-description">
            Browse all upcoming and past events in the system
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

      <a href="editHours.php" class="action-card">
        <div class="card-icon-wrapper">
          <img src="images/list-solid.svg" alt="Hours Icon">
        </div>
        <div class="action-card-content">
          <h3 class="action-card-title">Change Event Hours</h3>
          <p class="action-card-description">
            Adjust volunteer hours and time tracking for events
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

      <a href="viewAllEventSignUps.php" class="action-card">
        <div class="card-icon-wrapper">
          <img src="images/clock-regular.svg" alt="Sign-Ups Icon">
        </div>
        <div class="action-card-content">
          <h3 class="action-card-title">Pending Sign-Ups</h3>
          <p class="action-card-description">
            Review and approve volunteer event registrations
          </p>
        </div>
        <div class="action-card-footer">
          <?php
            require_once('database/dbEvents.php');
            require_once('database/dbPersons.php');
            $pendingsignups = all_pending_names();
            if (sizeof($pendingsignups) > 0) {
                echo '<span class="action-card-badge urgent">' . sizeof($pendingsignups) . ' Pending</span>';
            }
            ?>
          <span class="card-arrow">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
          </span>
        </div>
      </a>

      <a href="adminViewingEvents.php" class="action-card">
        <div class="card-icon-wrapper">
          <img src="images/users-solid.svg" alt="Edit Icon">
        </div>
        <div class="action-card-content">
          <h3 class="action-card-title">Edit Event</h3>
          <p class="action-card-description">
            Modify existing event details and attendee lists
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
        <h3 class="info-card-title">About Event Management</h3>
        <p class="info-card-text">
          Organize impactful events and trainings that engage your volunteer community. Create new events, 
          manage registrations, track volunteer hours, and keep everything running smoothly. This hub gives 
          you complete control over your event lifecycle from planning to completion.
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

