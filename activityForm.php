<?php
  $all_events = get_all_events_sorted_by_date_not_archived(); // grab active events to display in dropdown table
?>

<header class="hero-header"> 
    <div class="center-header">
        <h1>Track Volunteer Activities</h1>
    </div>
</header>

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

            <label for="activity_description"><em>* </em>Activity Description</label>
            <textarea id="activity_description" name="activity_description" rows="6" required placeholder="Describe what you did during this volunteer activity"></textarea>
        </fieldset>
           <label for="activity_image">Choose photos</label>
  <input id="activity_image" name="activity_images[]" multiple accept="image/jpeg, image/png" type="file" /> 
        <input type="submit" name="activity-form" value="Submit" class="blue-button">
    </form>
   </div> 
</main>
