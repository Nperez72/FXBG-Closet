<!-- Modern Header Navigation -->
<?php
date_default_timezone_set('America/New_York');/*
* Copyright 2013 by Allen Tucker. 
* This program is part of RMHP-Homebase, which is free software.  It comes with 
* absolutely no warranty. You can redistribute and/or modify it under the terms 
* of the GNU General Public License as published by the Free Software Foundation
* (see <http://www.gnu.org/licenses/ for more information).
* 
if (date("H:i:s") > "18:19:59") {
 require_once 'database/dbShifts.php';
 auto_checkout_missing_shifts();
}
*/

// check if we are in locked mode, if so,
// user cannot access anything else without 
// logging back in
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/theme-toggle.css">
</head>

<header>
    <?php
    //Log-in security
    $showing_login = false;
    if (!isset($_SESSION['logged_in'])) {
        // Not logged in - simple navbar
        echo('
        <nav class="modern-navbar">
            <div class="nav-container">
                <div class="nav-brand">
                    <a href="index.php" class="home-icon-btn" title="Home">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </a>
                    <span class="nav-title">Volunteer Management System</span>
                </div>
                <div class="nav-actions">
                    <div class="nav-date">' . date('l, F j, Y') . '</div>
                </div>
            </div>
        </nav>');
    } else if ($_SESSION['logged_in']) {
        // Permission array setup
        $permission_array = array(
            'index.php' => 0, 'about.php' => 0, 'apply.php' => 0, 'logout.php' => 0,
            'volunteerregister.php' => 0, 'leaderboard.php' => 0,
            'help.php' => 1, 'dashboard.php' => 1, 'calendar.php' => 1, 'eventsearch.php' => 1,
            'changepassword.php' => 1, 'editprofile.php' => 1, 'inbox.php' => 1, 'date.php' => 1,
            'event.php' => 1, 'viewprofile.php' => 1, 'viewnotification.php' => 1,
            'volunteerreport.php' => 1, 'viewmyupcomingevents.php' => 1, 'volunteerviewgroup.php' => 1,
            'viewcheckinout.php' => 1, 'viewresources.php' => 1, 'discussionmain.php' => 1,
            'viewdiscussions.php' => 1, 'discussioncontent.php' => 1, 'milestonepoints.php' => 1,
            'selectvotm.php' => 1, 'volunteerviewgroupmembers.php' => 1,
            'viewallevents.php' => 0, 'personsearch.php' => 2, 'personedit.php' => 0,
            'viewschedule.php' => 2, 'addweek.php' => 2, 'log.php' => 2, 'reports.php' => 2,
            'eventedit.php' => 2, 'modifyuserrole.php' => 2, 'addevent.php' => 2, 'editevent.php' => 2,
            'report.php' => 2, 'reportspage.php' => 2, 'resetpassword.php' => 2,
            'eventsuccess.php' => 2, 'viewsignuplist.php' => 2, 'vieweventsignups.php' => 2,
            'viewalleventsignups.php' => 2, 'resources.php' => 2, 'uploadresources.php' => 2,
            'deleteresources.php' => 2, 'creategroup.php' => 2, 'showgroups.php' => 2,
            'groupview.php' => 2, 'managemembers.php' => 2, 'deletegroup.php' => 2,
            'volunteermanagement.php' => 2, 'groupmanagement.php' => 2, 'eventmanagement.php' => 2,
            'creatediscussion.php' => 2, 'checkedinvolunteers.php' => 2, 'deletediscussion.php' => 2,
            'generatereport.php' => 2, 'generateemaillist.php' => 2, 'clockoutbulk.php' => 2,
            'clockout.php' => 2, 'edithours.php' => 2, 'eventlist.php' => 1, 'eventsignup.php' => 1,
            'eventfailure.php' => 1, 'signupsuccess.php' => 1, 'edittimes.php' => 1,
            'adminviewingevents.php' => 2, 'signuppending.php' => 1, 'requestfailed.php' => 1,
            'settimes.php' => 1, 'eventfailurebaddeparturetime.php' => 1
        );

        // Check permissions
        $current_page = strtolower(substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') + 1));
        if(isset($permission_array[$current_page]) && $permission_array[$current_page] > $_SESSION['access_level']){
            echo "<script type=\"text/javascript\">window.location = \"index.php\";</script>";
            die();
        }

        // ADMIN NAVBAR
        if ($_SESSION['access_level'] >= 2) {
            echo('
            <nav class="modern-navbar">
                <div class="nav-container">
                    <!-- Logo and Brand -->
                    <div class="nav-brand">
                        <a href="index.php" class="home-icon-btn" title="Home Dashboard">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </a>
                    </div>

                    <!-- Main Navigation -->
                    <div class="nav-menu" id="navMenu">
                        <a href="viewCheckInOut.php" class="nav-link nav-link-special">
                            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 11l3 3L22 4"></path>
                                <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path>
                            </svg>
                            <span>Check In/Out</span>
                        </a>

                        <div class="nav-dropdown">
                            <button class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"></path>
                                </svg>
                                <span>Volunteers</span>
                                <svg class="dropdown-arrow" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu">
                                <a href="VolunteerRegister.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                                        <circle cx="8.5" cy="7" r="4"></circle>
                                        <line x1="20" y1="8" x2="20" y2="14"></line>
                                        <line x1="23" y1="11" x2="17" y2="11"></line>
                                    </svg>
                                    Register Volunteer
                                </a>
                                <a href="personSearch.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="M21 21l-4.35-4.35"></path>
                                    </svg>
                                    Search Volunteers
                                </a>
                                <a href="checkedInVolunteers.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 11l3 3L22 4"></path>
                                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path>
                                    </svg>
                                    View Check-Ins
                                </a>
                            </div>
                        </div>

                        <div class="nav-dropdown">
                            <button class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span>Events</span>
                                <svg class="dropdown-arrow" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu">
                                <a href="addEvent.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Create Event
                                </a>
                                <a href="viewAllEvents.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="8" y1="6" x2="21" y2="6"></line>
                                        <line x1="8" y1="12" x2="21" y2="12"></line>
                                        <line x1="8" y1="18" x2="21" y2="18"></line>
                                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                    </svg>
                                    View Events
                                </a>
                                <a href="editHours.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    Change Event Hours
                                </a>
                                <a href="viewAllEventSignUps.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"></path>
                                    </svg>
                                    Pending Sign-Ups
                                </a>
                                <a href="adminViewingEvents.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Edit Event
                                </a>
                            </div>
                        </div>

                        <div class="nav-dropdown">
                            <button class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"></path>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                </svg>
                                <span>Groups</span>
                                <svg class="dropdown-arrow" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu">
                                <a href="createGroup.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Create Group
                                </a>
                                <a href="showGroups.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"></path>
                                        <line x1="2" y1="12" x2="22" y2="12"></line>
                                    </svg>
                                    View Groups
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Right Actions -->
                    <div class="nav-actions">
                        <a href="calendar.php" class="nav-action-btn" title="Calendar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </a>
                        
                        <button class="theme-toggle nav-action-btn" aria-label="Toggle theme" title="Toggle dark/light mode">
                            <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                            <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
                            </svg>
                        </button>

                        <div class="nav-date"></div>

                        <div class="nav-dropdown user-dropdown">
                            <button class="nav-action-btn user-btn">
                                <svg class="dropdown-icon user-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <circle cx="12" cy="8" r="4"></circle>
                                    <path d="M16 20c0-2.21-2.686-4-6-4s-6 1.79-6 4"></path>
                                </svg>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="changePassword.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0110 0v4"></path>
                                    </svg>
                                    Change Password
                                </a>
                                <a href="logout.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    Log Out
                                </a>
                            </div>
                        </div>

                        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle mobile menu">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </nav>');
        }

        // VOLUNTEER NAVBAR
        if ($_SESSION['access_level'] <= 1) {
            echo('
            <nav class="modern-navbar">
                <div class="nav-container">
                    <!-- Logo and Brand -->
                    <div class="nav-brand">
                        <a href="index.php" class="home-icon-btn" title="Home Dashboard">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </a>
                        <div class="nav-divider"></div>
                        <a href="index.php"><img src="images/actual_log.png" alt="Logo" class="nav-logo"></a>
                    </div>

                    <!-- Main Navigation -->
                    <div class="nav-menu" id="navMenu">
                        <div class="nav-dropdown">
                            <button class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span>Events</span>
                                <svg class="dropdown-arrow" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu">
                                <a href="viewMyUpcomingEvents.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="8" y1="6" x2="21" y2="6"></line>
                                        <line x1="8" y1="12" x2="21" y2="12"></line>
                                        <line x1="8" y1="18" x2="21" y2="18"></line>
                                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                    </svg>
                                    My Upcoming
                                </a>
                                <a href="viewAllEvents.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Sign-Up
                                </a>
                                <a href="editHours.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    Edit Hours
                                </a>
                            </div>
                        </div>

                        <div class="nav-dropdown">
                            <button class="nav-link">
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"></path>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                </svg>
                                <span>Groups</span>
                                <svg class="dropdown-arrow" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu">
                                <a href="volunteerViewGroup.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"></path>
                                        <line x1="2" y1="12" x2="22" y2="12"></line>
                                    </svg>
                                    My Groups
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Right Actions -->
                    <div class="nav-actions">
                        <a href="calendar.php" class="nav-action-btn" title="Calendar">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </a>
                        
                        <button class="theme-toggle nav-action-btn" aria-label="Toggle theme" title="Toggle dark/light mode">
                            <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                            <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
                            </svg>
                        </button>

                        <div class="nav-date"></div>

                        <div class="nav-dropdown user-dropdown">
                            <button class="nav-action-btn user-btn">
                                <img src="images/usaicon.png" alt="User">
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="viewProfile.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    View Profile
                                </a>
                                <a href="editProfile.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Edit Profile
                                </a>
                                <a href="volunteerReport.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    View Hours
                                </a>
                                <a href="inbox.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                    Notifications
                                </a>
                                <a href="changePassword.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0110 0v4"></path>
                                    </svg>
                                    Change Password
                                </a>
                                <a href="logout.php" class="dropdown-item">
                                    <svg class="dropdown-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    Log Out
                                </a>
                            </div>
                        </div>

                        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle mobile menu">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </nav>');
        }
    }
    ?>

    <script>
    function updateNavDate() {
        const now = new Date();
        const width = window.innerWidth;
        let formatted = "";

        if (width > 1400) {
            formatted = now.toLocaleDateString("en-US", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric"
            });
        } else if (width >= 1000) {
            formatted = now.toLocaleDateString("en-US", {
                month: "short",
                day: "numeric",
                year: "numeric"
            });
        } else {
            formatted = now.toLocaleDateString("en-US", {
                month: "short",
                day: "numeric"
            });
        }

        document.querySelectorAll(".nav-date").forEach(el => {
            if (width < 900) {
                el.style.display = "none";
            } else {
                el.style.display = "";
                el.textContent = formatted;
            }
        });
    }

    // Dropdown functionality
    document.addEventListener("DOMContentLoaded", function() {
        const dropdowns = document.querySelectorAll(".nav-dropdown");
        
        dropdowns.forEach(dropdown => {
            const button = dropdown.querySelector("button");
            const menu = dropdown.querySelector(".dropdown-menu");
            
            if (button && menu) {
                button.addEventListener("click", function(e) {
                    e.stopPropagation();
                    
                    // Close other dropdowns
                    dropdowns.forEach(other => {
                        if (other !== dropdown) {
                            other.classList.remove("active");
                        }
                    });
                    
                    // Toggle current dropdown
                    dropdown.classList.toggle("active");
                });
            }
        });

        // Close dropdowns when clicking outside
        document.addEventListener("click", function() {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove("active");
            });
        });

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById("mobileMenuBtn");
        const navMenu = document.getElementById("navMenu");
        
        if (mobileMenuBtn && navMenu) {
            mobileMenuBtn.addEventListener("click", function(e) {
                e.stopPropagation();
                this.classList.toggle("active");
                navMenu.classList.toggle("active");
                document.body.classList.toggle("mobile-menu-open");
            });
        }

        // Update date on load and resize
        updateNavDate();
        window.addEventListener("resize", updateNavDate);
    });
    </script>
    <script src="js/theme-toggle.js"></script>

    <?php
    // Mobile Bottom Navigation (PWA-optimized)
    if ($_SESSION['logged_in']) {
        if ($_SESSION['access_level'] >= 2) {
            // ADMIN MOBILE NAV
            echo('
            <nav class="mobile-bottom-nav">
                <a href="index.php" class="mobile-nav-item" data-page="index">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>Home</span>
                </a>
                <a href="volunteerManagement.php" class="mobile-nav-item" data-page="volunteer">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 00-3-3.87m-4-12a4 4 0 010 7.75"></path>
                    </svg>
                    <span>Volunteers</span>
                </a>
                <a href="eventManagement.php" class="mobile-nav-item" data-page="events">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Events</span>
                </a>
                <a href="calendar.php" class="mobile-nav-item" data-page="calendar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Calendar</span>
                </a>
                <button class="mobile-nav-item mobile-nav-more" id="mobileMoreBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="12" cy="5" r="1"></circle>
                        <circle cx="12" cy="19" r="1"></circle>
                    </svg>
                    <span>More</span>
                </button>
            </nav>
            
            <!-- Mobile More Menu -->
            <div class="mobile-more-menu" id="mobileMoreMenu">
                <div class="mobile-more-header">
                    <h3>More Options</h3>
                    <button class="mobile-more-close" id="mobileMoreClose">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="mobile-more-content">
                    <a href="groupManagement.php" class="mobile-more-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"></path>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                        </svg>
                        <span>Groups</span>
                    </a>
                    <a href="resources.php" class="mobile-more-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"></path>
                            <polyline points="13 2 13 9 20 9"></polyline>
                        </svg>
                        <span>Resources</span>
                    </a>
                    <a href="generateReport.php" class="mobile-more-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span>Reports</span>
                    </a>
                    <a href="inbox.php" class="mobile-more-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <span>Notifications</span>
                    </a>
                    <a href="viewProfile.php" class="mobile-more-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Profile</span>
                    </a>
                    <a href="logout.php" class="mobile-more-item mobile-more-logout">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </div>');
        } else {
            // VOLUNTEER MOBILE NAV
            echo('
            <nav class="mobile-bottom-nav">
                <a href="index.php" class="mobile-nav-item" data-page="index">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>Home</span>
                </a>
                <a href="viewAllEvents.php" class="mobile-nav-item" data-page="events">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Events</span>
                </a>
                <a href="calendar.php" class="mobile-nav-item" data-page="calendar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Calendar</span>
                </a>
                <a href="volunteerViewGroup.php" class="mobile-nav-item" data-page="groups">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"></path>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                    </svg>
                    <span>Groups</span>
                </a>
                <button class="mobile-nav-item mobile-nav-more" id="mobileMoreBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="12" cy="5" r="1"></circle>
                        <circle cx="12" cy="19" r="1"></circle>
                    </svg>
                    <span>More</span>
                </button>
            </nav>
            
            <!-- Mobile More Menu -->
            <div class="mobile-more-menu" id="mobileMoreMenu">
                <div class="mobile-more-header">
                    <h3>More Options</h3>
                    <button class="mobile-more-close" id="mobileMoreClose">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="mobile-more-content">
                    <a href="viewResources.php" class="mobile-more-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"></path>
                            <polyline points="13 2 13 9 20 9"></polyline>
                        </svg>
                        <span>Resources</span>
                    </a>
                    <a href="viewDiscussions.php" class="mobile-more-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"></path>
                        </svg>
                        <span>Discussions</span>
                    </a>
                    <a href="inbox.php" class="mobile-more-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <span>Notifications</span>
                    </a>
                    <a href="viewProfile.php" class="mobile-more-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Profile</span>
                    </a>
                    <a href="volunteerReport.php" class="mobile-more-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>My Hours</span>
                    </a>
                    <a href="logout.php" class="mobile-more-item mobile-more-logout">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Logout</span>
                    </a>
                </div>
            </div>');
        }
    }
    ?>
    
    <script>
    // Mobile Bottom Nav Active State & More Menu
    document.addEventListener('DOMContentLoaded', function() {
        // Set active state based on current page
        const currentPage = window.location.pathname.split('/').pop().replace('.php', '');
        const mobileNavItems = document.querySelectorAll('.mobile-nav-item[data-page]');
        
        mobileNavItems.forEach(item => {
            const page = item.getAttribute('data-page');
            if (currentPage.includes(page) || (currentPage === '' && page === 'index')) {
                item.classList.add('active');
            }
        });
        
        // More menu functionality
        const moreBtn = document.getElementById('mobileMoreBtn');
        const moreMenu = document.getElementById('mobileMoreMenu');
        const moreClose = document.getElementById('mobileMoreClose');
        
        if (moreBtn && moreMenu && moreClose) {
            moreBtn.addEventListener('click', function(e) {
                e.preventDefault();
                moreMenu.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
            
            moreClose.addEventListener('click', function() {
                moreMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
            
            // Close when clicking outside
            moreMenu.addEventListener('click', function(e) {
                if (e.target === moreMenu) {
                    moreMenu.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        }
    });
    </script>
</header>
