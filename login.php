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

        if(!verify_account_password($username, $password)) {
            $badLogin = true;
        } 
        else {
            session_regenerate_id(true);

            // Not setting session variables f_name, l_name, or type anymore
            // If stuff breaks might need to add them back
            $_SESSION['logged_in'] = true;
            $_SESSION['_id'] = $username;

          
            // In the original code here is what the access levels are: 
            // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
            // Trying to map them but might break stuff
            $accessLevel = $_SESSION['access_level'];
            switch(get_account_type($username)){
                // volunteer
                case 0:
                    $_SESSION['access_level'] = 1;
                    break;
                // coordinator/board member    
                case 1:
                    $_SESSION['access_level'] = 1;
                    break;
                // admin
                case 2:
                    $_SESSION['access_level'] = 3;
                    break;
            }
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
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/theme-toggle.css">
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
  </style>
  <title>FXBG Pride Volunteer System | Log In</title>
</head>

<body>
  <div class="h-screen flex" style="background-color: var(--bg-color);">

    <!-- Left: Image Section (Hidden on small screens) -->
    <div class="hidden md:block md:w-1/2 bg-center rounded-r-[50px]"
      style="background-image: url(images/PrideFlagInWind.png); background-size: cover; background-position: center; background-repeat: no-repeat;">
    </div>

    <!-- Right: Form Section -->

    <div class="w-full md:w-1/2 flex flex-col justify-center items-center bg-[var(--bg-color)] relative ">


      <div class="w-2/3 max-w-md flex flex-col items-center">

        <!-- Logo Placeholder (Now the same width as inputs and centered) -->
        <div class="w-full flex justify-center mb-6">
          <img src="images/FXBG-PrideLogo.png" 
            alt="Logo" 
            class="logo-lightMode w-full max-w-xs">
          <img src="images/FXBG-PrideWhiteLogo.png" 
            alt="Logo (Dark Mode)" 
            class="logo-darkMode w-full max-w-xs">
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
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
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