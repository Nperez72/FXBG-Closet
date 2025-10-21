<?php
session_cache_expire(30);
session_start();

// Clear and destroy session
session_unset();
session_destroy();
session_write_close();
?>
<html>
    <head>
        <meta HTTP-EQUIV="REFRESH" content="2; url=index.php">
        <?php require('universal.inc') ?>
        <link rel="stylesheet" href="css/base.css">
        <link rel="stylesheet" href="css/theme-toggle.css">
        <script src="js/theme-toggle.js"></script>
    </head>
    <body style="margin: 0; padding: 0">
        <nav style="flex: 0 0 auto; height:auto; min-height:0;">
            <span id="nav-top">
                <span class="logo">
                    <img src="images/FXBG-PrideLogo.png" alt="Logo" class="logo-lightMode" style="vertical-align: middle; margin-right: 25px">
                    <img src="images/FXBG-PrideWhiteLogo.png" alt="Logo (Dark Mode)" class="logo-darkMode" style="vertical-align: middle; margin-right: 25px">
                        <span id="vms-logo">  Fredericksburg Pride Volunteer System</span>
                </span>
                <img id="menu-toggle" src="images/menu.png">
            </span>
        </nav>
        <main>
            <p class="happy-toast centered">You have been logged out.</p>
        </main>
    </body>
</html>
