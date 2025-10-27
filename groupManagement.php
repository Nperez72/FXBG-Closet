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
<html lang="en" style="height: auto;">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Group Management Page</title>
  <link href="css/management_tw.css" rel="stylesheet">

<!-- BANDAID FIX FOR HEADER BEING WEIRD -->
<?php
$tailwind_mode = true;
require_once('header.php');
?>
<style>
  .date-box {
      background: var(--date-box-bg, #e8c4b8);
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
  
  .button-section button {
    display: flex;                    /* Use flexbox for alignment */
    align-items: center;              /* Vertically center content */
    justify-content: center;          /* Horizontally center content */
  }
  .button-section button div {
    text-align: center;               /* Ensures text inside div centers */
  }

</style>
<!-- BANDAID END, REMOVE ONCE SOME GENIUS FIXES -->

</head>

<body style="display: block">

  <!-- Larger Hero Section -->
  <header class="hero-header" style="flex: 0 0 auto; margin-top:0; margin-bottom: 50px; padding:0;"></header>

  <!-- Main Content -->
  <main style="flex: 1 0 auto;">
    <div class="sections">

      <!-- Buttons Section -->
      <div class="button-section">
        <button onclick="window.location.href='manageBoardMembers.php';">
          <div class="button-left-gray"></div>
          <div>Manage Board Members</div>
          <img class="button-icon h-14 w-14" src="images/group.svg" alt="Group Icon">
        </button>

        <button onclick="window.location.href='manageVolunteerCoordinators.php';">
          <div class="button-left-gray"></div>
          <div>Manage Volunteer Coordinators</div>
          <img class="button-icon h-14 w-14" src="images/group.svg" alt="Group Icon">
        </button>

        <div class="text-center mt-6">
            <a href="index.php" class="return-button">Return to Dashboard</a>
        </div>

     </div>

      <!-- Text Section -->
      <div class="text-section">
        <h1>Group Management</h1>
        <div class="div-blue" style="margin-top: 10px; margin-bottom:10px"></div>
        <p>
          Welcome to the group management hub! Use the controls on the left to manage the list of authorized board members and volunteer coordinators. Everything you need to control and configure these roles is just a click away.
        </p>
      </div>

    </div>
  </main>
</body>
</html>

