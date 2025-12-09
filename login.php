<?php
// Comment for assignment -Madi
// Template for new VMS pages. Base your new page on this one

// Make session information accessible, allowing us to associate
// data with the logged-in user.
session_cache_expire(30);
session_start();

ini_set("display_errors", 1);
error_reporting(E_ALL);

// redirect to index if already logged in
if (isset($_SESSION['_id'])) {
    header('Location: index.php');
    die();
}

$badLogin = false;
$archivedAccount = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once('include/input-validation.php');
    require_once("database/dbAccounts.php");

    $ignoreList = array('password');
    $args = sanitize($_POST, $ignoreList);
    $required = array('username', 'password');

    if (wereRequiredFieldsSubmitted($args, $required)) {
        $username = strtolower($args['username']);
        $password = $args['password'];

        if (!verify_account_password($username, $password)) {
            $badLogin = true;
        } else {
            session_regenerate_id(true);

            // Not setting session variables f_name, l_name, or type anymore
            // If stuff breaks might need to add them back
            $_SESSION['logged_in'] = true;
            $_SESSION['_id'] = $username;


            // In the original code here is what the access levels are:
            // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
            // Trying to map them but might break stuff
            switch (get_account_type($username)) {
                // volunteer
                case 0:
                    $_SESSION['access_level'] = 1;
                    break;
                // coordinator/board member
                case 1:
                    $_SESSION['access_level'] = -1;
                    header('Location: roleChange.php');
                    die();
                    break;
                // admin
                case 2:
                    $_SESSION['access_level'] = 4;
                    break;
            }
            $accessLevel = $_SESSION['access_level'];

            header('Location: index.php');
            die();
        }
    }
}
    //<p>Or <a href="register.php">register as a new volunteer</a>!</p>
    //Had this line under login button, took user to register page
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
  <script>
    (function() {
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
        
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    })();
  </script>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/theme-toggle.css">
  <link rel="stylesheet" href="css/pwa-mobile.css">
  
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#6B46C1">
  <meta name="description" content="Volunteer Management System for Fredericksburg Pride - FXBG Closet">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="FXBG Closet">
  <link rel="apple-touch-icon" href="/images/FXBG-PrideWhiteLogo.png">
  
  <script src="js/theme-toggle.js"></script>
  <style>
    /* Found this on codepen :D */
    .wave {
      animation-name: wave-animation;
      /* Refers to the name of your @keyframes element below */
      animation-duration: 2.5s;
      /* Change to speed up or slow down */
      animation-iteration-count: infinite;
      /* Never stop waving :) */
      transform-origin: 70% 70%;
      /* Pivot around the bottom-left palm */
      display: inline-block;
    }

    @keyframes wave-animation {
      0% {
        transform: rotate(0.0deg)
      }

      10% {
        transform: rotate(14.0deg)
      }

      /* The following five values can be played with to make the waving more or less extreme */
      20% {
        transform: rotate(-8.0deg)
      }

      30% {
        transform: rotate(14.0deg)
      }

      40% {
        transform: rotate(-4.0deg)
      }

      50% {
        transform: rotate(10.0deg)
      }

      60% {
        transform: rotate(0.0deg)
      }

      /* Reset for the last half to pause */
      100% {
        transform: rotate(0.0deg)
      }
    }

    * {
      font-family: Quicksand, sans-serif;
    }

    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
    }

    @media (min-width: 769px) {
      .login-container {
        height: 100vh;
        display: flex;
      }

      .login-image-section {
        display: block !important;
        width: 50%;
        background-image: url(images/PrideFlagInWind.png);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 0 50px 50px 0;
      }

      .login-form-section {
        width: 50%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 40px;
      }

      .login-form-container {
        width: 66.666667%;
        max-width: 28rem;
      }

      .login-logo {
        width: 100%;
        max-width: 24rem;
      }
    }

    @media (max-width: 768px) {
      .login-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
      }

      .login-image-section {
        display: none !important;
      }

      .login-form-section {
        width: 100%;
        padding: 20px;
      }

      .login-form-container {
        width: 100%;
        max-width: 100%;
      }

      .login-logo {
        max-width: 200px !important;
        width: 100%;
      }

      h2.text-3xl {
        font-size: 1.5rem !important;
      }

      .login-theme-toggle {
        top: 16px;
        right: 16px;
      }
    }

    .login-theme-toggle {
      position: fixed;
      top: 24px;
      right: 24px;
      z-index: 1000;
    }
  </style>
  <title>FXBG Pride Volunteer System | Log In</title>
</head>

<body>
  <!-- Theme Toggle Button -->
  <button class="theme-toggle login-theme-toggle" aria-label="Toggle theme" title="Toggle dark/light mode">
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

  <div class="login-container" style="background-color: var(--bg-color);">

    <!-- Left: Image Section (Hidden on small screens) -->
    <div class="login-image-section">
    </div>

    <!-- Right: Form Section -->
    <div class="login-form-section" style="background-color: var(--bg-color);">

      <div class="login-form-container flex flex-col items-center">

        <!-- Logo Placeholder (Now the same width as inputs and centered) -->
        <div class="w-full flex justify-center mb-6">
          <img src="images/FXBG-PrideLogo.png" 
            alt="Logo" 
            class="logo-lightMode login-logo">
          <img src="images/FXBG-PrideWhiteLogo.png" 
            alt="Logo (Dark Mode)" 
            class="logo-darkMode login-logo">
        </div>

        <h2 class="text-3xl font-bold mb-6 text-gray-800 text-center">
          <span class="wave">👋</span> Nice to see you again.
        </h2>

        <form class="w-full" method="post">
          <?php
            if ($badLogin) {
                echo '<span class="text-white bg-red-700 text-center block p-2 rounded-lg mb-2">No login with that username and password combination currently exists.</span>';
            }
            if ($archivedAccount) {
                echo '<span class="text-white bg-red-700 block p-2 rounded-lg mb-2">This account has either been archived or not yet approved by managment. For help, notify <a href="mailto:volunteer@fredspca.org">volunteer@fredspca.org</a>.</span>';
            }
            if (isset($_GET['registerSuccess'])) {
                echo '<span class="text-white text-center bg-green-700 block p-2 rounded-lg mb-2">Registration Successful! Please login below.</span>';
            }
            ?>
          <div class="mb-4">
        <?php if (isset($_GET['success']) && $_GET['success'] == 1) : ?>
            <div class="success-message bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <p class="text-center">Password successfully changed!</p>
                <p class="text-center">Please log in with your new password.</p>
            </div>
        <?php endif ?>
            <label class="block text-gray-700 font-medium mb-2" for="username">Login</label>
            <input class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none focus:ring-2" style="focus:ring-color: var(--accent-color, #d4af37);" type="text" name="username" placeholder="Enter your username" required>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2" for="password">Password</label>
            <input class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none focus:ring-2" style="focus:ring-color: var(--accent-color, #d4af37);" type="password" name="password" placeholder="Enter your password" required>
          </div>
          <div class="flex justify-between items-center mb-4">
            <a href="#" class="text-sm hover:underline" style="color: var(--text-muted, #e8c4b8);">Forgot password?</a>
            <a href="https://fxbgpride.org/" class="text-sm hover:underline" style="color: var(--text-muted, #e8c4b8);">Fredericksburg Pride Website</a>
          </div>
          <button class="cursor-pointer w-full text-white font-semibold py-3 rounded-lg transition duration-300" style="background-color: var(--main-color, #e8c4b8); color: var(--button-text, #363434);" onmouseover="this.style.backgroundColor='var(--accent-color, #d4af37)'" onmouseout="this.style.backgroundColor='var(--main-color, #e8c4b8)'">Login</button>
        </form>

        <!-- Divider -->
        <div class="flex items-center my-6 w-full">
          <div class="flex-grow border-t border-gray-300"></div>
          <span class="mx-4 text-gray-400">or</span>
          <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <!-- Sign Up Section -->
        <p class="text-center text-gray-700">
          Don’t have an account?
          <a href="VolunteerRegister.php" class="font-semibold hover:underline" style="color: var(--accent-color, #d4af37);">Sign Up Now</a>
        </p>

      </div>
    </div>

  </div>

</body>

</html>