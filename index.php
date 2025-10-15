<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_cache_expire(30);
    session_start();

    date_default_timezone_set("America/New_York");
    
    if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
        if (isset($_SESSION['change-password'])) {
            header('Location: changePassword.php');
        } else {
            header('Location: login.php');
        }
        die();
    }
        
    include_once('database/dbPersons.php');
    include_once('domain/Person.php');
    // Get date?
    if (isset($_SESSION['_id'])) {
        $person = retrieve_person($_SESSION['_id']);
    }
    $notRoot = $person->get_id() != 'vmsroot';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/theme-toggle.css">
    <title>Frederickburg SPCA Volunteer Management | Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            font-weight: 500;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h2 {
            font-weight: 600;
            font-size: 30px;
            color: var(--text-color);
            transition: color 0.3s ease;
        }

        .full-width-bar {
            width: 100%;
            background: var(--full-width-bar-bg);
            padding: 17px 5%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            transition: background-color 0.3s ease;
        }
        .full-width-bar-sub {
            width: 100%;
            background: var(--full-width-bar-sub-bg);
            padding: 17px 5%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            transition: background-color 0.3s ease;
        }

        .content-box {
            flex: 1 1 280px; /* Adjusts width dynamically */
            max-width: 375px;
            padding: 10px 2px; /* Altered padding to make closer */
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .content-box-sub {
            flex: 1 1 300px; /* Adjusts width dynamically */
            max-width: 470px;
            padding: 10px 10px; /* Altered padding to make closer */
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .content-box img {
            width: 100%;
            height: auto;
            background: var(--content-box-bg);
            border-radius: 5px;
            border-bottom-right-radius: 50px;
            border: 0.5px solid var(--content-box-border);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .content-box-sub img {
            width: 105%;
            height: auto;
            background: var(--content-box-bg);
            border-radius: 5px;
            border-bottom-right-radius: 50px;
            border: 1px solid var(--content-box-border);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .small-text {
            position: absolute;
            top: 20px;
            left: 30px;
            font-size: 14px;
            font-weight: 600;
            color: var(--main-color);
            transition: color 0.3s ease;
            letter-spacing: -0.01em;
        }

        .large-text {
            position: absolute;
            top: 40px;
            left: 30px;
            font-size: 22px;
            font-weight: 600;
            color: var(--text-color);
            max-width: 90%;
            transition: color 0.3s ease;
            letter-spacing: -0.02em;
        }

        .large-text-sub {
            position: absolute;
            /*top: 120px;*/
            top: 60%;
            left: 10%;
            font-size: 22px;
            font-weight: 600;
            color: var(--text-color);
            max-width: 90%;
            transition: color 0.3s ease;
            letter-spacing: -0.02em;
        }

        .graph-text {
            position: absolute;
            top: 75%;
            left: 10%;
            font-size: 14px;
            font-weight: 500;
            color: var(--main-color);
            max-width: 90%;
            transition: color 0.3s ease;
        }

        /* These navbar styles are now handled by theme-toggle.css */

        /* Button Control */
        .arrow-button {
            position: absolute;
            bottom: 30px;
            right: 30px;
            background: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
            transition: transform 0.3s ease;

        }

        .arrow-button:hover {
            transform: translateX(5px); /* Moves the arrow slightly on hover */
        }
    .circle-arrow-button {
        position: absolute;
        bottom: 30px;
        right: 30px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: transparent;
        border: none;
        font-size: 18px;
        font-family: 'Quicksand', sans-serif;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.3s ease;
        letter-spacing: -0.01em;
    }









        /* Responsive Design */
   </style>
<!--BEGIN TEST, UPLOAD AND NOTIFICATIONS CHANGED-->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelector(".extra-info").style.maxHeight = "0px"; // Ensure proper initialization
        });
        function toggleInfo(event) {
            event.stopPropagation(); // Prevents triggering the main button click
            let info = event.target.nextElementSibling;
            let isVisible = info.style.maxHeight !== "0px";
            info.style.maxHeight = isVisible ? "0px" : "100px";
            event.target.innerText = isVisible ? "↓" : "↑";
        }
    </script>
<!--END TEST-->
</head>

<!-- ONLY SUPER ADMIN WILL SEE THIS -->
<?php if ($_SESSION['access_level'] >= 2): ?>
<body>
<?php require 'header.php';?>

    <!-- Dummy content to enable scrolling -->
    <div style="margin-top: 0px; padding: 30px 20px;">
        <h2><b>Welcome <?php echo $person->get_first_name() ?>!</b> Let's get started.</h2>
    </div>

            <?php if (isset($_GET['pcSuccess'])): ?>
                <div class="happy-toast">Password changed successfully!</div>
            <?php elseif (isset($_GET['deleteService'])): ?>
                <div class="happy-toast">Service successfully removed!</div>
            <?php elseif (isset($_GET['serviceAdded'])): ?>
                <div class="happy-toast">Service successfully added!</div>
            <?php elseif (isset($_GET['animalRemoved'])): ?>
                <div class="happy-toast">Animal successfully removed!</div>
            <?php elseif (isset($_GET['locationAdded'])): ?>
                <div class="happy-toast">Location successfully added!</div>
            <?php elseif (isset($_GET['deleteLocation'])): ?>
                <div class="happy-toast">Location successfully removed!</div>
            <?php elseif (isset($_GET['registerSuccess'])): ?>
                <div class="happy-toast">Volunteer registered successfully!</div>
            <?php endif ?>

    <div class="full-width-bar">
      <div class="content-box">
          <div class="small-text">Make a difference.</div>
        <div class="large-text">Volunteer Management</div>
<button class="circle-arrow-button" onclick="window.location.href='volunteerManagement.php'">
    <span class="button-text">Go</span>
    <div class="circle">&gt;</div>
</button>
<!--
        <div class="nav-buttons">
            <button class="nav-button" onclick="window.location.href='personSearch.php'">
                <span>Find</span>
                <span class="arrow"><img src="images/person-search.svg" style="width: 40px; border-radius:5px; border-bottom-right-radius: 20px;"></span>
            </button>
            <button class="nav-button" onclick="window.location.href='VolunteerRegister.php'">
                <span>Register</span>
                <span class="arrow"><img src="images/add-person.svg" style="width: 40px; border-radius:5px; border-bottom-right-radius: 20px;"></span>
            </button>
        </div>
-->
    </div>

      <div class="content-box">
          <div class="small-text">Let's have some fun!</div>
        <div class="large-text">Event Management</div>
<button class="circle-arrow-button" onclick="window.location.href='eventManagement.php'">
    <span class="button-text"><?php 
                        require_once('database/dbEvents.php');
                        require_once('database/dbPersons.php');
                        $pendingsignups = all_pending_names();
                        if (sizeof($pendingsignups) > 0) {
                            echo '<span class="colored-box">' . sizeof($pendingsignups) . '</span>';
                        }   
                    ?> Sign-Ups </span>
    <div class="circle">&gt;</div>
</button>
    </div>

      <div class="content-box">
          <div class="small-text">Our team makes this all possible.</div>
        <div class="large-text">Group Management</div>
<button class="circle-arrow-button" onclick="window.location.href='groupManagement.php'">
    <span class="button-text">Go</span>
    <div class="circle">&gt;</div>
</button>
    </div>

</div>

    <div class="dashboard-title">
        <h2><b>Admin Dashboard</h2>
    </div>
    <div class="full-width-bar-sub">
        <div class="content-box-test" onclick="window.location.href='calendar.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/view-calendar.svg" alt="Calendar Icon">
            </div>
            <div class="background-image"></div>
            <div class="large-text-sub">Calendar</div>
            <div class="graph-text">See upcoming events/trainings.</div>
            <button class="arrow-button">→</button>
        </div>


        <div class="content-box-test" onclick="window.location.href='resources.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/file-regular.svg" alt="Document Icon">
            </div>
            <div class="background-image"></div>
            <div class="large-text-sub">Manage Documents</div>
            <div class="graph-text">Resources for volunteers.</div>
            <button class="arrow-button">→</button>
        </div>
                <?php
                    require_once('database/dbMessages.php');
                    $unreadMessageCount = get_user_unread_count($person->get_id());
                    $inboxIcon = 'inbox.svg';
                    if ($unreadMessageCount) {
                        $inboxIcon = 'inbox-unread.svg';
                    }
                ?>
        <div class="content-box-test" onclick="window.location.href='inbox.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/<?php echo $inboxIcon ?>" alt="Notification Icon">
            </div>
            <div class="background-image"></div>
            <div class="large-text-sub">System Notifications<?php 
                        if ($unreadMessageCount > 0) {
                            echo ' (' . $unreadMessageCount . ')';
                        }
                    ?></div>
            <div class="graph-text">Stay up to date.</div>
            <button class="arrow-button">→</button>
        </div>

        <div class="content-box-test" onclick="window.location.href='generateReport.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/clipboard-regular.svg" alt="Report Icon">
            </div>
            <div class="background-image"></div>
            <div class="large-text-sub">Generate Report</div>
            <div class="graph-text">From this quarter or annual.</div>
            <button class="arrow-button">→</button>
        </div>
    <div class="content-box-test" onclick="window.location.href='generateEmailList.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/clipboard-regular.svg" alt="Report Icon">
            </div>
            <div class="background-image"></div>
            <div class="large-text-sub">Generate Email List</div>
            <div class="graph-text">Volunteer Emails</div>
            <button class="arrow-button">→</button>
        </div>

        <div class="content-box-test" onclick="window.location.href='viewDiscussions.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/clipboard-regular.svg" alt="Report Icon">
            </div>
            <div class="background-image"></div>
            <div class="large-text-sub">Discussions</div>
            <div class="graph-text">See the latest.</div>
            <button class="arrow-button">→</button>
        </div>
    </div>


    

<div class="divider-line"></div>


    <footer class="footer" style="margin-top: 100px;">
        <!-- Left Side: Logo & Info -->
        <div class="footer-left">
            <img src="images/actual_log.png" alt="Logo" class="footer-logo">
            <p style="margin-top: 1rem; color: var(--text-secondary); max-width: 300px; font-size: 0.95rem;">
                Fredericksburg's Resource For The LGBTQIA+ Community
            </p>
            <div class="social-icons" style="margin-top: 1rem;">
                <a href="https://www.facebook.com/fxbgpride" target="_blank" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="https://www.instagram.com/fxbgpride" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>

        <!-- Right Side: Page Links -->
        <div class="footer-right">
            <div class="footer-section">
                <div class="footer-topic">Get Involved</div>
                <a href="eventManagement.php">Programs & Events</a>
                <a href="volunteerManagement.php">Volunteer</a>
                <a href="resources.php">Resources</a>
                <a href="viewProfile.php">My Profile</a>
            </div>
            <div class="footer-section">
                <div class="footer-topic">Contact</div>
                <a href="mailto:info@fxbgpride.org">info@fxbgpride.org</a>
                <p style="margin-top: 0.5rem; font-size: 0.9rem; line-height: 1.5;">4343 Plank Road Suite 110<br>Fredericksburg, VA 22407</p>
            </div>
            <div class="footer-section">
                <div class="footer-topic">24-Hour Emergency</div>
                <a href="tel:8775658860">Trans Lifeline: 877-565-8860</a>
                <a href="tel:5403736876">RACSB: 540-373-6876</a>
                <a href="https://translifeline.org" target="_blank" style="font-size: 0.85rem; margin-top: 0.5rem;">Trans Lifeline Website →</a>
            </div>
        </div>
    </footer>
    
    <!-- Footer Copyright -->
    <div style="background: var(--navbar-bg); padding: 1rem 2rem; text-align: center; border-top: 1px solid var(--border-color);">
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 0;">
            Copyright © <?php echo date('Y'); ?> Fredericksburg Pride | All Rights Reserved
        </p>
    </div>

    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/yourkit.js" crossorigin="anonymous"></script>
    <script src="js/theme-toggle.js"></script>

</body>
<?php endif ?>

<!-- ONLY VOLUNTEERS WILL SEE THIS -->
<?php if ($notRoot) : ?>
<body>
<?php require 'header.php';?>

  

  <!-- Icon Container -->
<div class="icon-container">
    <!-- Volunteer of the Month Icon -->
    <a href="selectVOTM.php">
        <div class="icon-label">
            🎖 Volunteer of the Month
        </div>
        <img src="images/star-icon.svg" alt="Volunteer of the Month Icon" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
    </a>

    <!-- Leaderboard Icon -->
    <a href="leaderboard.php">
        <div class="icon-label">
            👑 Leaderboard
        </div>
        <img src="images/crown.png" alt="Leaderboard Icon" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
    </a>
</div>



    <!-- Dummy content to enable scrolling -->
    <div style="margin-top: 0px; padding: 30px 20px;">
        <h2><b>Welcome <?php echo $person->get_first_name() ?>!</b> Let's get started.</h2>
    </div>

    <div class="full-width-bar">
      <div class="content-box">
          <div class="small-text">Make a difference.</div>
        <div class="large-text">My Profile</div>
        <div class="nav-buttons">
            <button class="nav-button" onclick="window.location.href='viewProfile.php'">
                <span class="arrow"><img src="images/view-profile.svg" style="width: 40px; border-radius:5px; border-bottom-right-radius: 20px;"></span>
                <span class="text">View</span>
            </button>
            <button class="nav-button" onclick="window.location.href='editProfile.php'">
                <span class="arrow"><img src="images/manage-account.svg" style="width: 40px; border-radius:5px; border-bottom-right-radius: 20px;"></span>
                <span class="text">Edit</span>
            </button>
            <button class="nav-button" onclick="window.location.href='volunteerReport.php'">
                <span class="arrow"><img src="images/volunteer-history.svg" style="width: 40px; border-radius:5px; border-bottom-right-radius: 20px;"></span>
                <span class="text">My Hours</span>
            </button>
        </div>
    </div>

      <div class="content-box">
          <div class="small-text">Let's have some fun!</div>
        <div class="large-text">My Events</div>
        <div class="nav-buttons">
            <button class="nav-button" onclick="window.location.href='viewAllEvents.php'">
                <span class="arrow"><img src="images/new-event.svg" style="width: 40px; border-radius:5px; border-bottom-right-radius: 10px;"></span>
                <span class="text">Sign-Up</span>
            </button>
            <button class="nav-button" onclick="window.location.href='viewMyUpcomingEvents.php'">
                <span class="arrow"><img src="images/list-solid.svg" style="width: 40px; border-radius:5px; border-bottom-right-radius: 10px;"></span>
                <span class="text">Upcoming</span>
            </button>
            <button class="nav-button" onclick="window.location.href='editHours.php'">
                <span class="arrow"><img src="images/clock-regular.svg" style="width: 40px; border-radius:5px; border-bottom-right-radius: 10px;"></span>
                <span class="text">Hours</span>
            </button>
        </div>
    </div>

      <div class="content-box">
          <div class="small-text">Our team makes this all possible.</div>
        <div class="large-text">My Group</div>
        <div class="nav-buttons">
            <button class="nav-button" onclick="window.location.href='volunteerViewGroup.php'">
                <span class="arrow"><img src="images/group.svg" style="width: 40px; border-radius:5px; border-bottom-right-radius: 20px;"></span>
                <span class="text">View</span>
            </button>
        </div>
    </div>
    </div>

    <div class="dashboard-title">
        <h2><b>Your Dashboard</h2>
    </div>
    <div class="full-width-bar-sub">
        <div class="content-box-test" onclick="window.location.href='calendar.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/view-calendar.svg" alt="Calendar Icon">
            </div>
            <div class="background-image"></div>
            <div class="large-text-sub">Calendar</div>
            <div class="graph-text">See upcoming events/trainings.</div>
            <button class="arrow-button">→</button>
        </div>

               <?php
                    require_once('database/dbMessages.php');
                    $unreadMessageCount = get_user_unread_count($person->get_id());
                    $inboxIcon = 'inbox.svg';
                    if ($unreadMessageCount) {
                        $inboxIcon = 'inbox-unread.svg';
                    }   
                ?>  

        <div class="content-box-test" onclick="window.location.href='viewResources.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/file-regular.svg" alt="Calendar Icon">
            </div>
            <div class="background-image"></div>
            <div class="large-text-sub">Documents</div>
            <div class="graph-text">View documents & the volunteer handbook.</div>
            <button class="arrow-button">→</button>
        </div>

        <div class="content-box-test" onclick="window.location.href='viewDiscussions.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/clipboard-regular.svg" alt="Report Icon">
            </div>
            <div class="background-image"></div>
            <div class="large-text-sub">Discussions</div>
            <div class="graph-text">See the latest.</div>
            <button class="arrow-button">→</button>
        </div>

        <div class="content-box-test" onclick="window.location.href='inbox.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/<?php echo $inboxIcon ?>" alt="Notification Icon">
            </div>
            <div class="background-image"></div>
            <div class="large-text-sub">Notifications</div>
            <div class="graph-text">Stay up to date.</div>
            <button class="arrow-button">→</button>
        </div>

    </div>

<div class="divider-line"></div>

    <footer class="footer" style="margin-top: 100px;">
        <!-- Left Side: Logo & Info -->
        <div class="footer-left">
            <img src="images/actual_log.png" alt="Logo" class="footer-logo">
            <p style="margin-top: 1rem; color: var(--text-secondary); max-width: 300px; font-size: 0.95rem;">
                Fredericksburg's Resource For The LGBTQIA+ Community
            </p>
            <div class="social-icons" style="margin-top: 1rem;">
                <a href="https://www.facebook.com/fxbgpride" target="_blank" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="https://www.instagram.com/fxbgpride" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>

        <!-- Right Side: Page Links -->
        <div class="footer-right">
            <div class="footer-section">
                <div class="footer-topic">Get Involved</div>
                <a href="viewAllEvents.php">Programs & Events</a>
                <a href="volunteerViewGroup.php">Volunteer</a>
                <a href="viewResources.php">Resources</a>
                <a href="viewProfile.php">My Profile</a>
            </div>
            <div class="footer-section">
                <div class="footer-topic">Contact</div>
                <a href="mailto:info@fxbgpride.org">info@fxbgpride.org</a>
                <p style="margin-top: 0.5rem; font-size: 0.9rem; line-height: 1.5;">4343 Plank Road Suite 110<br>Fredericksburg, VA 22407</p>
            </div>
            <div class="footer-section">
                <div class="footer-topic">24-Hour Emergency</div>
                <a href="tel:8775658860">Trans Lifeline: 877-565-8860</a>
                <a href="tel:5403736876">RACSB: 540-373-6876</a>
                <a href="https://translifeline.org" target="_blank" style="font-size: 0.85rem; margin-top: 0.5rem;">Trans Lifeline Website →</a>
            </div>
        </div>
    </footer>
    
    <!-- Footer Copyright -->
    <div style="background: var(--navbar-bg); padding: 1rem 2rem; text-align: center; border-top: 1px solid var(--border-color);">
        <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 0;">
            Copyright © <?php echo date('Y'); ?> Fredericksburg Pride | All Rights Reserved
        </p>
    </div>

    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/yourkit.js" crossorigin="anonymous"></script>
    <script src="js/theme-toggle.js"></script>

</body>
<?php endif ?>
</html>
