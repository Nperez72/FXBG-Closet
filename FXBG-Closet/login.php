<?php
    // Comment for assignment -Madi
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();
    
    ini_set("display_errors",1);
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
        $ignoreList = array('password');
        $args = sanitize($_POST, $ignoreList);
        $required = array('username', 'password');
        if (wereRequiredFieldsSubmitted($args, $required)) {
            require_once('domain/Person.php');
            require_once('database/dbPersons.php');
            /*@require_once('database/dbMessages.php');*/
            /*@dateChecker();*/
            $username = strtolower($args['username']);
            $password = $args['password'];
            $user = retrieve_person($username);
            if (!$user) {
                $badLogin = true;
            } else if ($user->get_status() === "Inactive") {
                // If the user is archived, block login
                $archivedAccount = true;
            } else if (password_verify($password, $user->get_password())) {
                $_SESSION['logged_in'] = true;

                $_SESSION['access_level'] = $user->get_access_level();
                $_SESSION['f_name'] = $user->get_first_name();
                $_SESSION['l_name'] = $user->get_last_name();

                
                $_SESSION['type'] = 'admin';
                $_SESSION['_id'] = $user->get_id();
                
                 //hard code root privileges
                 if ($user->get_id() == 'vmsroot') {
                    $_SESSION['access_level'] = 3;
		    $_SESSION['locked'] = false;
                    header('Location: index.php');
               }
            
                //if ($changePassword) {
                //    $_SESSION['access_level'] = 0;
                //    $_SESSION['change-password'] = true;
                //    header('Location: changePassword.php');
                //    die();
                //} 
                else {
                    header('Location: index.php');
                    die();
                }
                die();
            } else {
                $badLogin = true;
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
	<style>
/* Found this on codepen :D */
.wave {
  animation-name: wave-animation;  /* Refers to the name of your @keyframes element below */
  animation-duration: 2.5s;        /* Change to speed up or slow down */
  animation-iteration-count: infinite;  /* Never stop waving :) */
  transform-origin: 70% 70%;       /* Pivot around the bottom-left palm */
  display: inline-block;
}

@keyframes wave-animation {
    0% { transform: rotate( 0.0deg) }
   10% { transform: rotate(14.0deg) }  /* The following five values can be played with to make the waving more or less extreme */
   20% { transform: rotate(-8.0deg) }
   30% { transform: rotate(14.0deg) }
   40% { transform: rotate(-4.0deg) }
   50% { transform: rotate(10.0deg) }
   60% { transform: rotate( 0.0deg) }  /* Reset for the last half to pause */
  100% { transform: rotate( 0.0deg) }
}
* { font-family: Quicksand, sans-serif; }
	</style>
        <title>Fredericksburg SPCA Volunteer System | Log In</title>
    </head>
    <body>
<div class="h-screen flex">

  <!-- Left: Image Section (Hidden on small screens) -->
  <div class="hidden md:block md:w-1/2 bg-center rounded-r-[50px]"
       style="background-image: url(images/login_background.jpg);">
  </div>

  <!-- Right: Form Section -->

  <div class="w-full md:w-1/2 flex flex-col justify-center items-center bg-white relative ">


    <div class="w-2/3 max-w-md flex flex-col items-center">

      <!-- Logo Placeholder (Now the same width as inputs and centered) -->
      <div class="w-full flex justify-center mb-6">
        <img src="images/drawn_logo.png"
             alt="Logo"
             class="w-full max-w-xs">
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
          <label class="block text-gray-700 font-medium mb-2" for="username">Login</label>
          <input class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400" type="text" name="username" placeholder="Enter your username" required>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-medium mb-2" for="password">Password</label>
          <input class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400" type="password" name="password" placeholder="Enter your password" required>
        </div>
        <div class="flex justify-between items-center mb-4">
          <a href="#" class="text-blue-500 text-sm hover:underline">Forgot password?</a>
          <a href="https://fredspca.org/" class="text-blue-500 text-sm hover:underline">Fredericksburg SPCA Website</a>
        </div>
        <button class="cursor-pointer w-full bg-[#345284] hover:bg-blue-600 text-white font-semibold py-3 rounded-lg transition duration-300">Login</button>
      </form>

      <!-- Divider -->
      <div class="flex items-center my-6 w-full">
        <div class="flex-grow border-t border-gray-300"></div>
        <span class="mx-4 text-gray-500">or</span>
        <div class="flex-grow border-t border-gray-300"></div>
      </div>

      <!-- Sign Up Section -->
      <p class="text-center text-gray-700">
        Don’t have an account?
        <a href="VolunteerRegister.php" class="text-blue-500 font-semibold hover:underline">Sign Up Now</a>
      </p>

    </div>
  </div>

</div>

    </body>
</html>
