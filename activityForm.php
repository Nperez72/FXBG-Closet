<?php
  if (!isset($_SESSION['_id'])) {
    header("Location: login.php");
    die();
  }

  $all_events = get_all_events_sorted_by_date_not_archived();
  
  if ($role === 'board member') {
    $board_members = getBoardMembers();
  }
  if ($role === 'volunteer coordinator') {
    $coordinators = getVolunteerCoordinators();
  }
?>

<header class="hero-header"> 
    <div class="center-header">
        <h1>Track Volunteer Activities</h1>
    </div>
</header>

<?php if (isset($flash) && $flash) : ?>
    <?php $isSuccess = ($flash['type'] === 'success'); ?>
  <div class="flash-wrap">
    <div id="flash" class="flash-card <?php echo $isSuccess ? 'success' : 'error'; ?>" role="alert" aria-live="polite">
      <div class="flash-body">
        <ul class="flash-list">
          <?php foreach ((array)($flash['messages'] ?? []) as $m) : ?>
            <li><?php echo htmlspecialchars($m, ENT_QUOTES, 'UTF-8'); ?></li>
          <?php endforeach; ?>
        </ul>
        <button class="flash-close" aria-label="Dismiss" onclick="document.getElementById('flash')?.remove()">×</button>
      </div>
    </div>
  </div>
<?php endif; ?>

<main>
  <div class="main-content-box w-full max-w-3xl p-8 mb-8">
    <form class="signup-form" method="post" enctype="multipart/form-data">
        <div class="text-center mb-8">
          <h2 class="mb-8">Activity Log Form</h2>
            <div class="main-content-box border-2 mb-0 shadow-xs w-full p-4">
              <p class="sub-text">Please fill out the following form to log your volunteer activities.</p>
              <p>An asterisk (<em>*</em>) indicates a required field.</p>
            </div>
        </div>

        <!-- PERSON IDENTIFICATION (for board members and coordinators) -->
        <?php if ($role === 'board member' || $role === 'volunteer coordinator') : ?>
        <fieldset class="section-box mb-4">
            <h3 class="mt-2">Your Information</h3>
            <div class="blue-div"></div>
            <label for="person_name"><em>* </em>Your Name</label>
            <select id="person_name" name="person_name" required>
                <option value="">Select your name</option>
                <?php 
                $people_list = ($role === 'board member') ? $board_members : $coordinators;
                foreach ($people_list as $person) : 
                ?>
                    <option value="<?php echo $person['fullname']; ?>">
                        <?php echo htmlspecialchars($person['fullname']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </fieldset>
        <?php else : ?>
            <!-- For volunteers, just collect name as text -->
            <fieldset class="section-box mb-4">
                <h3 class="mt-2">Your Information</h3>
                <div class="blue-div"></div>
                <label for="person_name"><em>* </em>Your Name</label>
                <input type="text" id="person_name" name="person_name" required placeholder="Enter your full name">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address">
            </fieldset>
        <?php endif; ?>
        
         <!-- ACTIVITY DETAILS (common to all) -->
        <fieldset class="section-box mb-4">
            <h3 class="mt-2">Activity Details</h3>
            <p class="mb-2">Please provide information about your volunteer activity.</p>
            <div class="blue-div"></div>

            <label for="event_id"><em>* </em>Event Name</label>
            <select id="event_id" name="event_id" required>
                <option value="">Select an event</option>
                <!-- Loop through every value in all_events and display the name for each one -->
                <?php foreach ($all_events as $event) : ?>
                    <option value="<?php echo $event->getID(); ?>">
                        <?php echo htmlspecialchars($event->getName()); ?> - <?php echo $event->getDate(); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="hours_spent"><em>* </em>Hours Spent</label>
            <input type="number" id="hours_spent" name="hours_spent" min="0" step="0.5" required placeholder="Enter hours spent (e.g., 2.5)">
        </fieldset>
        
        <!-- COMMUNITY INTERACTIONS (volunteers and board members only) -->
        <?php if ($role === 'volunteer' || $role === 'board member') : ?>
        <fieldset class="section-box mb-4">
            <h3 class="mt-2">Community Interactions</h3>
            <div class="blue-div"></div>
            <p class="mb-6 text-gray-600">Estimate the number of community members you interacted with during this activity.</p>
            
            <!-- Age Ranges -->
            <div class="mb-8">
                <h4 class="text-base font-semibold mb-4 text-gray-700">Age Ranges</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <div>
                        <label for="age_0_12" class="block text-sm mb-1 text-gray-600">0-12 years</label>
                        <input type="number" id="age_0_12" name="age_0_12" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="age_13_17" class="block text-sm mb-1 text-gray-600">13-17 years</label>
                        <input type="number" id="age_13_17" name="age_13_17" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="age_18_24" class="block text-sm mb-1 text-gray-600">18-24 years</label>
                        <input type="number" id="age_18_24" name="age_18_24" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="age_25_54" class="block text-sm mb-1 text-gray-600">25-54 years</label>
                        <input type="number" id="age_25_54" name="age_25_54" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="age_55_plus" class="block text-sm mb-1 text-gray-600">55+ years</label>
                        <input type="number" id="age_55_plus" name="age_55_plus" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>
            
            <!-- Ethnicity -->
            <div>
                <h4 class="text-base font-semibold mb-4 text-gray-700">Ethnicity</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    <div>
                        <label for="ethnicity_white" class="block text-sm mb-1 text-gray-600">White/Caucasian</label>
                        <input type="number" id="ethnicity_white" name="ethnicity_white" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="ethnicity_black" class="block text-sm mb-1 text-gray-600">Black/African American</label>
                        <input type="number" id="ethnicity_black" name="ethnicity_black" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="ethnicity_hispanic" class="block text-sm mb-1 text-gray-600">Hispanic/Latino</label>
                        <input type="number" id="ethnicity_hispanic" name="ethnicity_hispanic" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="ethnicity_asian" class="block text-sm mb-1 text-gray-600">Asian</label>
                        <input type="number" id="ethnicity_asian" name="ethnicity_asian" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="ethnicity_native" class="block text-sm mb-1 text-gray-600">Native American/Indigenous</label>
                        <input type="number" id="ethnicity_native" name="ethnicity_native" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="ethnicity_other" class="block text-sm mb-1 text-gray-600">Other/Multiracial</label>
                        <input type="number" id="ethnicity_other" name="ethnicity_other" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>
        </fieldset>
        <?php endif; ?>

        <!-- PHOTO UPLOAD (common to all) -->
        <fieldset class="section-box mb-4">
            <h3 class="mt-2">Documentation Photos</h3>
            <div class="blue-div"></div>
            <label for="activity_image">Choose photos to send as documentation (max 10 files, 10MB per file, 50MB total)</label>
            <input id="activity_image" name="activity_images[]" multiple accept="image/jpeg, image/png" type="file" /> 
        </fieldset>

        <input type="submit" name="activity-form" value="Submit" class="blue-button">
    </form>
   </div> 
</main>
