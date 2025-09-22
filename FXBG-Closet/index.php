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
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <title>Frederickburg SPCA Volunteer Management | Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Quicksand, sans-serif;
        }

        h2 {
        	font-weight: normal;
            font-size: 30px;
        }

        .full-width-bar {
            width: 100%;
            background: #294877;
            padding: 17px 5%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .full-width-bar-sub {
            width: 100%;
            background: white;
            padding: 17px 5%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
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
            background: white;
            border-radius: 5px;
            border-bottom-right-radius: 50px;
            border: 0.5px solid #828282;
        }

        .content-box-sub img {
            width: 105%;
            height: auto;
            background: white;
            border-radius: 5px;
            border-bottom-right-radius: 50px;
            border: 1px solid #828282;
        }

        .small-text {
            position: absolute;
            top: 20px;
            left: 30px;
            font-size: 14px;
            font-weight: 700;
            color: #294877;
        }

        .large-text {
            position: absolute;
            top: 40px;
            left: 30px;
            font-size: 22px;
            font-weight: 700;
            color: black;
            max-width: 90%;
        }

        .large-text-sub {
            position: absolute;
            /*top: 120px;*/
            top: 60%;
            left: 10%;
            font-size: 22px;
            font-weight: 700;
            color: black;
            max-width: 90%;
        }

        .graph-text {
            position: absolute;
            top: 75%;
            left: 10%;
            font-size: 14px;
            font-weight: 700;
            color: #294877;
            max-width: 90%;
        }

        /* Navbar Container */
        .navbar {
            width: 100%;
            height: 95px;
            position: fixed;
            top: 0;
            left: 0;
            background: white;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            padding: 0 20px;
            z-index: 1000;
        }

        /* Left Section: Logo & Nav Links */
        .left-section {
            display: flex;
            align-items: center;
            gap: 30px; /* Space between logo and links */
        }

        /* Logo */
        .logo-container {
            background: #294877;
            padding: 10px 20px;
            border-radius: 50px;
            box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25) inset;
        }

        .logo-container img {
            width: 128px;
            height: 52px;
            display: block;
        }

        /* Navigation Links */
        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links div {
            font-size: 24px;
            font-weight: 700;
            color: black;
            cursor: pointer;
        }

        /* Right Section: Date & Icon */
        .right-section {
            margin-left: auto; /* Pushes right section to the end */
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .date-box {
            background: #274471;
            padding: 10px 30px;
            border-radius: 50px;
            box-shadow: -4px 4px 4px rgba(0, 0, 0, 0.25) inset;
            color: white;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
        }

        .icon {
            width: 47px;
            height: 47px;
            /*background: #292D32;*/
            border-radius: 50%;

        }

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
        font-size: 20px;
        font-family: Quicksand, sans-serif;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .circle {
        width: 30px;
        height: 30px;
        background-color: #294877; /* Blue color */
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        transition: transform 0.3s ease;
    }

    .circle-arrow-button:hover .circle {
        transform: translateX(5px); /* Moves the circle slightly on hover */
    }
.colored-box {
    display: inline-block; /* Ensures it wraps tightly around the text */
    background-color: #294877; /* Change to any color */
    color: white; /* Text color */
    padding: 1px 5px; /* Adds space inside the box */
    border-radius: 5px; /* Optional: Rounds the corners */
    font-weight: bold; /* Optional: Makes text bold */
}


        /* Footer */
        .footer {
            width: 100%;
            background: #294877;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 30px 50px;
            flex-wrap: wrap;
        }

        /* Left Section */
        .footer-left {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .footer-logo {
            width: 150px; /* Adjust logo size */
            margin-bottom: 15px;
        }

        /* Social Media Icons */
        .social-icons {
            display: flex;
            gap: 15px;
        }

        .social-icons a {
            color: white;
            font-size: 20px;
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: #dcdcdc;
        }

        /* Right Section */
        .footer-right {
            display: flex;
            gap: 50px;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .footer-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 10px;
            color: white;
            font-family: Inter, sans-serif;
            font-size: 16px;
            font-weight: 500;
        }

        .footer-topic {
            font-size: 18px;
            font-weight: bold;
        }

        .footer a {
            color: white;
            text-decoration: none;
            transition: background 0.2s ease, color 0.2s ease;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .footer a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #dcdcdc;
        }

        /* Icon Overlay */
        .background-image {
            width: 100%;
            border-radius: 10px;
        }

        .icon-overlay {
            position: absolute;
            top: 40px; /* Adjust as needed */
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.8); /* Optional background for better visibility */
            padding: 10px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .icon-overlay img {
            width: 40px; /* Adjust size as needed */
            height: 40px;
            opacity: 0.9;
        }

        .content-box-test:hover .icon-overlay img {
            transform: scale(1.1) rotate(5deg);
            transition: transform 0.5s ease, fill 0.5s ease;
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
        <img src="images/VolM.png" />
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
        <img src="images/EvM.png" />
        <div class="small-text">Let’s have some fun!</div>
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
        <img src="images/GrM.png" />
        <div class="small-text">Our team makes this all possible.</div>
        <div class="large-text">Group Management</div>
<button class="circle-arrow-button" onclick="window.location.href='groupManagement.php'">
    <span class="button-text">Go</span>
    <div class="circle">&gt;</div>
</button>
    </div>

</div>

    <div style="margin-top: 50px; padding: 0px 80px;">
        <h2><b>Admin Dashboard</h2>
    </div>
    <div class="full-width-bar-sub">
        <div class="content-box-test" onclick="window.location.href='calendar.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/view-calendar.svg" alt="Calendar Icon">
            </div>
            <img class="background-image" src="images/blank-white-background.jpg" />
            <div class="large-text-sub">Calendar</div>
            <div class="graph-text">See upcoming events/trainings.</div>
            <button class="arrow-button">→</button>
        </div>


        <div class="content-box-test" onclick="window.location.href='resources.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/file-regular.svg" alt="Document Icon">
            </div>
            <img class="background-image" src="images/blank-white-background.jpg" />
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
            <img class="background-image" src="images/blank-white-background.jpg" />
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
            <img class="background-image" src="images/blank-white-background.jpg" />
            <div class="large-text-sub">Generate Report</div>
            <div class="graph-text">From this quarter or annual.</div>
            <button class="arrow-button">→</button>
        </div>
    <div class="content-box-test" onclick="window.location.href='generateEmailList.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/clipboard-regular.svg" alt="Report Icon">
            </div>
            <img class="background-image" src="images/blank-white-background.jpg" />
            <div class="large-text-sub">Generate Email List</div>
            <div class="graph-text">Volunteer Emails</div>
            <button class="arrow-button">→</button>
        </div>

        <div class="content-box-test" onclick="window.location.href='viewDiscussions.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/clipboard-regular.svg" alt="Report Icon">
            </div>
            <img class="background-image" src="images/blank-white-background.jpg" />
            <div class="large-text-sub">Discussions</div>
            <div class="graph-text">See the latest.</div>
            <button class="arrow-button">→</button>
        </div>
    </div>


    

<div style="width: 90%; /* Stops before page ends */
            height: 100%;
            outline: 1px #828282 solid;
            outline-offset: -0.5px;
            margin: 70px auto; /* Adds vertical space and centers */
            padding: 1px 0;"> <!-- Adds spacing inside the div -->
</div>


    <footer class="footer" style="margin-top: 100px;">
        <!-- Left Side: Logo & Socials -->
        <div class="footer-left">
            <img src="images/actual_log.png" alt="Logo" class="footer-logo">
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>

        <!-- Right Side: Page Links -->
        <div class="footer-right">
            <div class="footer-section">
                <div class="footer-topic">Connect</div>
                <a href="https://www.facebook.com/share/g/15X2tqwFkA/">Facebook</a>
                <a href="https://www.instagram.com/fredspca/?hl=en">Instagram</a>
                <a href="https://fredspca.org">Main Website</a>
            </div>
            <div class="footer-section">
                <div class="footer-topic">Contact Us</div>
                <a href="mailto:volunteer@fredspca.org">volunteer@fredspca.org</a>
                <a href="tel:5408981500">540-898-1500 (ext 117)</a>
            </div>
        </div>
    </footer>
    <p>_</p>

    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/yourkit.js" crossorigin="anonymous"></script>


</body>
<?php endif ?>

<!-- ONLY VOLUNTEERS WILL SEE THIS -->
<?php if ($notRoot) : ?>
<body>
<?php require 'header.php';?>

  

  <!-- Icon Container -->
<div style="position: absolute; top: 110px; right: 30px; z-index: 999; display: flex; flex-direction: row; gap: 30px; align-items: center; text-align: center;">

    <!-- Volunteer of the Month Icon -->
    <a href="selectVOTM.php" style="text-decoration: none;">
        <div style="font-size: 12px; font-weight: bold; color: #294877; margin-bottom: 5px;">
            🎖 Volunteer of the Month
        </div>
        <img src="images/star-icon.svg" alt="Volunteer of the Month Icon" style="width: 55px; height: auto; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
    </a>

    <!-- Leaderboard Icon -->
    <a href="leaderboard.php" style="text-decoration: none;">
        <div style="font-size: 12px; font-weight: bold; color: #294877; margin-bottom: 5px;">
            👑 Leaderboard
        </div>
        <img src="images/crown.png" alt="Leaderboard Icon" style="width: 55px; height: auto; transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
    </a>

</div>



    <!-- Dummy content to enable scrolling -->
    <div style="margin-top: 0px; padding: 30px 20px;">
        <h2><b>Welcome <?php echo $person->get_first_name() ?>!</b> Let's get started.</h2>
    </div>

    <div class="full-width-bar">
    <div class="content-box">
        <img src="images/VolM.png" />
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
        <img src="images/EvM.png" />
        <div class="small-text">Let’s have some fun!</div>
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
        <img src="images/GrM.png" />
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

    <div style="margin-top: 50px; padding: 0px 80px;">
        <h2><b>Your Dashboard</h2>
    </div>
    <div class="full-width-bar-sub">
        <div class="content-box-test" onclick="window.location.href='calendar.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/view-calendar.svg" alt="Calendar Icon">
            </div>
            <img class="background-image" src="images/blank-white-background.jpg" />
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
            <img class="background-image" src="images/blank-white-background.jpg" />
            <div class="large-text-sub">Documents</div>
            <div class="graph-text">View documents & the volunteer handbook.</div>
            <button class="arrow-button">→</button>
        </div>

        <div class="content-box-test" onclick="window.location.href='viewDiscussions.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/clipboard-regular.svg" alt="Report Icon">
            </div>
            <img class="background-image" src="images/blank-white-background.jpg" />
            <div class="large-text-sub">Discussions</div>
            <div class="graph-text">See the latest.</div>
            <button class="arrow-button">→</button>
        </div>

        <div class="content-box-test" onclick="window.location.href='inbox.php'">
            <div class="icon-overlay">
                <img style="border-radius: 5px;" src="images/<?php echo $inboxIcon ?>" alt="Notification Icon">
            </div>
            <img class="background-image" src="images/blank-white-background.jpg" />
            <div class="large-text-sub">Notifications</div>
            <div class="graph-text">Stay up to date.</div>
            <button class="arrow-button">→</button>
        </div>

    </div>

<div style="width: 90%; /* Stops before page ends */
            height: 100%;
            outline: 1px #828282 solid;
            outline-offset: -0.5px;
            margin: 70px auto; /* Adds vertical space and centers */
            padding: 1px 0;"> <!-- Adds spacing inside the div -->
</div>

    <footer class="footer" style="margin-top: 100px;">
        <!-- Left Side: Logo & Socials -->
        <div class="footer-left">
            <img src="images/actual_log.png" alt="Logo" class="footer-logo">
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>

        <!-- Right Side: Page Links -->
        <div class="footer-right">
            <div class="footer-section">
                <div class="footer-topic">Connect</div>
                <a href="https://www.facebook.com/share/g/15X2tqwFkA/">Facebook</a>
                <a href="https://www.instagram.com/fredspca/?hl=en">Instagram</a>
                <a href="https://fredspca.org">Main Website</a>
            </div>
            <div class="footer-section">
                <div class="footer-topic">Contact Us</div>
                <a href="mailto:volunteer@fredspca.org">volunteer@fredspca.org</a>
                <a href="tel:5408981500">540-898-1500 (ext 117)</a>
            </div>
        </div>
    </footer>
    <p>_</p>

    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/yourkit.js" crossorigin="anonymous"></script>

</body>
<?php endif ?>
</html>
