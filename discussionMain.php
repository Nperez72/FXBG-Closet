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
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Volunteer Management Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <?php require_once('header.php') ?>
  <style>
        .date-box {
            background: #274471;
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
  </style>

</head>
<body class="bg-gray-100 min-h-screen flex flex-col">


  <!-- Larger Hero Section -->
  <header class="h-40 w-full bg-auto bg-center relative z-0" style="background-image: url('https://images.thdstatic.com/productImages/7c22c2c6-a12a-404c-bdd6-d56779e7a66f/svn/chesapeake-wallpaper-rolls-3122-10402-64_600.jpg');"></header>


  <!-- Main Content -->
  <main class="flex-grow flex items-start justify-center p-6 -mt-12 relative z-10">
    <div class="flex flex-col lg:flex-row gap-12 w-full max-w-6xl">

      <!-- Buttons Section -->
      <div class="grid grid-cols-1 gap-6 w-full md:grid-cols-2 lg:w-2/3 lg:grid-cols-1">
        <button class="flex justify-center relative bg-white border-2 border-gray-300 rounded-2xl shadow-sm hover:shadow-md cursor-pointer hover:border-blue-900 p-6 transition-all duration-200 text-xl font-medium text-gray-700 hover:bg-gray-100" onclick="window.location.href='viewDiscussions.php';">
	  <div class="absolute top-0 left-0 w-[80px] h-full bg-gray-200 rounded-l-2xl pointer-events-none z-0"></div>
	  <div>View Discussions</div>
	  <img class="w-14 h-14 absolute left-3 top-1/2 -translate-y-1/2 p-[2px]" src="images/group.svg" alt="Calendar Icon">
        </button>

<?php if ($accessLevel >= 2): ?>
        <button class="flex justify-center relative bg-white border-2 border-gray-300 rounded-2xl shadow-sm hover:shadow-md cursor-pointer hover:border-blue-900 p-6 transition-all duration-200 text-xl font-medium text-gray-700 hover:bg-gray-100" onclick="window.location.href='createDiscussion.php';">
	  <div class="absolute top-0 left-0 w-[80px] h-full bg-gray-200 rounded-l-2xl pointer-events-none z-0"></div>
	  <div>Create Discussion</div>
	  <img class="w-14 h-14 absolute left-3 top-1/2 -translate-y-1/2 p-[2px]" src="images/creategroup.svg" alt="Calendar Icon">
        </button>
<?php endif; ?>
           </div>

      <!-- Text Section -->
      <div class="flex flex-col justify-center w-full lg:w-1/2 text-gray-700">
        <h1 class="text-3xl font-bold mb-4 mt-6">Discussions</h1>
        <div class="h-px bg-blue-900 w-full"></div>
        <p class="text-lg leading-relaxed">
          Welcome to the management hub. Use the controls on the left to manage users, content, access, and view important analytics and logs. Everything you need to control and configure your platform is just a click away.
        </p>
      </div>

    </div>
  </main>
</body>
</html>
