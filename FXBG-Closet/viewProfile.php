<?php
    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    $isAdmin = false;
    if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
        header('Location: login.php');
        die();
    }
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $isAdmin = $accessLevel >= 2;
        $userID = $_SESSION['_id'];
    } else {
        header('Location: login.php');
        die();
    }
    if ($isAdmin && isset($_GET['id'])) {
        require_once('include/input-validation.php');
        $args = sanitize($_GET);
        $id = strtolower($args['id']);
    } else {
        $id = $userID;
    }
    require_once('database/dbPersons.php');
    //if (isset($_GET['removePic'])) {
     // if ($_GET['removePic'] === 'true') {
       // remove_profile_picture($id);
      //}
    //}

   $user = retrieve_person($id);
   if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_hours'])) {
    require_once('database/dbPersons.php'); // already required, so you can just remove the duplicate
    $con = connect();

    $newHours = floatval($_POST['new_hours']);
    $safeID = mysqli_real_escape_string($con, $id);

    $update = mysqli_query($con, "
        UPDATE dbpersons 
        SET total_hours_volunteered = $newHours 
        WHERE id = '$safeID'
    ");

    if ($update) {
        $user = retrieve_person($id); // refresh with updated hours
        echo '
        <div id="success-message" class="absolute left-[40%] top-[15%] z-50 bg-green-800 p-4 text-white rounded-xl text-xl">
          Hours updated successfully!
        </div>
        <script>
          setTimeout(() => {
            const msg = document.getElementById("success-message");
            if (msg) msg.remove();
          }, 3000);
        </script>
        ';
    } else {
        echo '<div class="absolute left-[40%] top-[15%] z-50 bg-red-800 p-4 text-white rounded-xl text-xl">Failed to update hours.</div>';
    }
  
}

    $viewingOwnProfile = $id == $userID;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['url'])) {
        if (!update_profile_pic($id, $_POST['url'])) {
          header('Location: viewProfile.php?id='.$id.'&picsuccess=False');
        } else {
          header('Location: viewProfile.php?id='.$id.'&picsuccess=True');
        }
      }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function showSection(sectionId) {
      const sections = document.querySelectorAll('.profile-section');
      sections.forEach(section => section.classList.add('hidden'));
      document.getElementById(sectionId).classList.remove('hidden');

      const tabs = document.querySelectorAll('.tab-button');
      tabs.forEach(tab => {
        tab.classList.remove('border-b-4', 'border-blue-900');
        tab.classList.add('hover:border-b-2', 'hover:border-blue-700');
      });

      const activeTab = document.querySelector(`[data-tab="${sectionId}"]`);
      activeTab.classList.add('border-b-4', 'border-blue-900');
      activeTab.classList.remove('hover:border-b-2', 'hover:border-blue-700');
    }

    window.onload = () => showSection('personal');
  </script>
  <?php 
    require_once('header.php'); 
    require_once('include/output.php');
  ?>

</head>
            <?php if ($id == 'vmsroot'): ?>
		<div class="absolute left-[40%] top-[20%] bg-red-800 p-4 text-white rounded-xl text-xl">The root user does not have a profile.</div>
                </main></body></html>
                <?php die() ?>
            <?php elseif (!$user): ?>
		<div class="absolute left-[40%] top-[20%] bg-red-800 p-4 text-white rounded-xl text-xl">User does not exist.</div>
                </main></body></html>
                <?php die() ?>
            <?php endif ?>
            <?php if (isset($_GET['editSuccess'])): ?>
		<div class="absolute left-[40%] top-[15%] z-50 bg-green-800 p-4 text-white rounded-xl text-xl">Profile updated successfully!</div>
            <?php endif ?>
            <?php if (isset($_GET['rscSuccess'])): ?>
		<div class="absolute left-[40%] top-[15%] z-50 bg-green-800 p-4 text-white rounded-xl text-xl">User role/status updated successfully!</div>
            <?php endif ?>

<body class="bg-gray-100">
  <!-- Hero Section -->
  <div class="h-48 relative" style="background-image: url('https://images.thdstatic.com/productImages/7c22c2c6-a12a-404c-bdd6-d56779e7a66f/svn/chesapeake-wallpaper-rolls-3122-10402-64_600.jpg');">
  </div>

  <!-- Profile Content -->
  <div class="max-w-6xl mx-auto px-4 -mt-20 relative z-10 flex flex-col md:flex-row gap-6">
    <!-- Left Box -->
    <div class="w-full md:w-1/3 bg-white border border-gray-300 rounded-2xl shadow-lg p-6 flex flex-col justify-between">
      <div>
	<div class="flex justify-between items-center">
	<?php if ($viewingOwnProfile): ?>
          <h2 class="text-xl font-semibold mb-4">My Profile</h2>
	  <h2 class="mb-4">Edit Icon Placeholder</h2>
	<?php else: ?>
	  <h2 class="text-xl font-semibold mb-4">Viewing <?php echo $user->get_first_name() . ' ' . $user->get_last_name() ?></h2>
	<?php endif ?>
	</div>
        <div class="space-y-2 divide-y divide-gray-300">
          <div class="flex justify-between py-2">
            <span class="font-medium">Joined</span><span>Jan 2022</span>
          </div>
          <div class="flex justify-between py-2">
            <span class="font-medium">Role</span><span>Volunteer</span>
          </div>
          <div class="flex justify-between py-2">
            <span class="font-medium">Status</span><span><?php
                 if ($user->get_archived()) {
                     echo 'Archived';
                 } else {
                     echo 'Active';
                 }
                     ?></span>
          </div>
        </div>
      </div>
      <div class="mt-6 space-y-2">
        <button onclick="window.location.href='editProfile.php<?php if ($id != $userID) echo '?id=' . $id ?>';" class="text-lg font-medium w-full px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-700 cursor-pointer">Edit Profile</button>

<!-- -->
            <?php if ($id != $userID): ?>
                <?php if (($accessLevel == 2 && $user->get_access_level() == 1) || $accessLevel >= 3): ?>
        <button onclick="window.location.href='resetPassword.php?id=<?php echo htmlspecialchars($_GET['id']) ?>';" class="text-lg font-medium w-full px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-700 cursor-pointer">Change Password</button>
                <?php endif ?>
        <button onclick="window.location.href='volunteerReport.php?id=<?php echo htmlspecialchars($_GET['id']) ?>';" class="text-lg font-medium w-full px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-700 cursor-pointer">View Volunteer Hours</button>
        <button onclick="window.location.href='personSearch.php';" class="text-lg font-medium w-full px-4 py-2 border-2 border-gray-300 text-black rounded-md hover:border-blue-700 cursor-pointer">Return to User Search</button>
            <?php else: ?>
        <button onclick="window.location.href='changePassword.php';" class="text-lg font-medium w-full px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-700 cursor-pointer">Change Password</button>
        <button onclick="window.location.href='volunteerReport.php';" class="text-lg font-medium w-full px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-700 cursor-pointer">View Volunteer Hours</button>
        <button onclick="window.location.href='milestonePoints.php';" class="text-lg font-medium w-full px-4 py-2 bg-blue-900 text-white rounded-md hover:bg-blue-700 cursor-pointer">View Milestones &amp Points</button>
            <?php endif ?>
<!-- -->


<?php if ($accessLevel < 2) : ?>
        <button onclick="window.location.href='volunteerReport.php?id=<?php echo $user->get_id() ?>';" class="text-lg font-medium w-full px-4 py-2 border-2 border-gray-300 text-black rounded-md hover:border-blue-700 cursor-pointer">My Volunteering Report</button>
<?php else : ?>
        <button onclick="window.location.href='volunteerReport.php?id=<?php echo $user->get_id() ?>';" class="text-lg font-medium w-full px-4 py-2 border-2 border-gray-300 text-black rounded-md hover:border-blue-700 cursor-pointer"><?php echo $user->get_first_name() ?> <?php echo $user->get_last_name() ?>'s Volunteering Report</button>
<?php endif ?>
        <button onclick="window.location.href='index.php';" class="text-lg font-medium w-full px-4 py-2 border-2 border-gray-300 text-black rounded-md hover:border-blue-700 cursor-pointer">Return to Dashboard</button>
      </div>
    </div>

    <!-- Right Box -->
    <div class="w-full md:w-2/3 bg-white rounded-2xl shadow-lg border border-gray-300 p-6">
      <!-- Tabs -->
      <div class="flex border-b border-gray-300 mb-4">
        <button class="tab-button px-4 py-2 text-lg font-medium text-gray-700 border-b-4 border-blue-900" data-tab="personal" onclick="showSection('personal')">Personal Information</button>
        <button class="tab-button px-4 py-2 text-lg font-medium text-gray-700" data-tab="contact" onclick="showSection('contact')">Contact Information</button>
        <button class="tab-button px-4 py-2 text-lg font-medium text-gray-700" data-tab="volunteer" onclick="showSection('volunteer')">Volunteer Information</button>
      </div>

      <!-- Personal Section -->
      <div id="personal" class="profile-section space-y-4">
        <div>
          <span class="block text-sm font-medium text-blue-900">Username</span>
          <p class="text-gray-900 font-medium text-xl"><?php echo $user->get_id() ?></p>
        </div>
        <div>
          <span class="block text-sm font-medium text-blue-900">Name</span>
          <p class="text-gray-900 font-medium text-xl"><?php echo $user->get_first_name() ?> <?php echo $user->get_last_name() ?></p>
        </div>
        <div>
          <span class="block text-sm font-medium text-blue-900">Date of Birth</span>
          <p class="text-gray-900 font-medium text-xl"><?php echo date('m/d/Y', strtotime($user->get_birthday())) ?></p>
        </div>
        <div>
          <span class="block text-sm font-medium text-blue-900">Address</span>
          <p class="text-gray-900 font-medium text-xl"><?php echo $user->get_street_address() . ', ' . $user->get_city() . ', ' . $user->get_state() . ' ' . $user->get_zip_code() ?></p>
        </div>
      </div>

      <!-- Contact Section -->
      <div id="contact" class="profile-section space-y-4 hidden">
        <div>
          <span class="block text-sm font-medium text-blue-900">Email</span>
          <p class="text-gray-900 font-medium text-xl"><a href="mailto:<?php echo $user->get_email() ?>"><?php echo $user->get_email() ?></a></p>
        </div>
        <div>
          <span class="block text-sm font-medium text-blue-900">Phone Number</span>
          <p class="text-gray-900 font-medium text-xl"><a href="tel:<?php echo $user->get_phone1() ?>"><?php echo formatPhoneNumber($user->get_phone1()) ?></a> (<?php echo ucfirst($user->get_phone1type()) ?>)</p>
        </div>
        <div>
          <span class="block text-sm font-medium text-blue-900">Emergency Contact Name</span>
          <p class="text-gray-900 font-medium text-xl"><?php echo $user->get_emergency_contact_first_name() . ' ' . $user->get_emergency_contact_last_name() ?></p>
        </div>
        <div>
          <span class="block text-sm font-medium text-blue-900">Emergency Contact Relation</span>
          <p class="text-gray-900 font-medium text-xl"><?php echo $user->get_emergency_contact_relation() ?></p>
        </div>
        <div>
          <span class="block text-sm font-medium text-blue-900">Emergency Contact Phone Number</span>
          <p class="text-gray-900 font-medium text-xl"><a href="tel:<?php echo $user->get_emergency_contact_phone() ?>"><?php echo formatPhoneNumber($user->get_emergency_contact_phone()) ?></a> (<?php echo ucfirst($user->get_emergency_contact_phone_type()) ?>)</p>
        </div>
 
      </div>

      <!-- Volunteer Section -->
      <div id="volunteer" class="profile-section space-y-4 hidden">
        <div>
          <span class="block text-sm font-medium text-blue-900">Account Type</span>
          <p class="text-gray-900 font-medium text-xl"><?php
                if ($user->get_is_community_service_volunteer()) {
                    echo 'Community Service Volunteer';
                } else {
                    echo 'Standard Volunteer';
                }
                    ?></p>
        </div>
        <div>
          <span class="block text-sm font-medium text-blue-900">Skills</span>
          <p class="text-gray-900 font-medium text-xl"><?php echo ucfirst($user->get_skills() ?: "Not specified") ?></p>
        </div>
        <div>
          <span class="block text-sm font-medium text-blue-900">Interests</span>
          <p class="text-gray-900 font-medium text-xl"><?php echo ucfirst($user->get_interests() ?: "Not specified") ?></p>
        </div>
	      <div>
          <span class="block text-sm font-medium text-blue-900">Total Hours Volunteered</span>
          <?php if ($isAdmin && !$viewingOwnProfile): ?>
            <form method="POST" class="mt-2 flex items-center gap-4">
              <input type="number" step="0.01" name="new_hours" min="0" value="<?= htmlspecialchars($user->get_total_hours_volunteered()) ?>" required
                    class="border border-gray-300 px-3 py-1 rounded-md w-32 shadow-sm">
            

              <button type="submit" class="bg-blue-900 text-white px-4 py-1 rounded-md hover:bg-blue-700">
                Update
              </button>
            </form>
          <?php else: ?>
            <p class="text-gray-900 font-medium text-xl">
              <?= number_format($user->get_total_hours_volunteered(), 2) ?> hours
            </p>
          <?php endif; ?>
        </div>

	      
      </div>
    </div>
  </div>
</body>
</html>
