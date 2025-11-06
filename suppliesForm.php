<header class="hero-header"> 
    <div class="center-header">
        <h1>Material Resupply Request</h1>
    </div>
</header>

<main>
  <div class="main-content-box w-full max-w-3xl p-8 mb-8">
    <form class="signup-form" method="post">
        <div class="text-center mb-8">
          <h2 class="mb-8">Resupply Checklist</h2>
            <div class="main-content-box border-2 mb-0 shadow-xs w-full p-4">
              <p class="sub-text">Request materials that need to be resupplied for volunteer activities.</p>
              <p>An asterisk (<em>*</em>) indicates a required field.</p>
            </div>
        </div>
        
        <fieldset class="section-box mb-4">
            <h3 class="mt-2">Supply Request Details</h3>
            <p class="mb-2">Please provide information about the materials needed.</p>
            <div class="blue-div"></div>

            <label for="item_type"><em>* </em>Item Type</label>
            <select id="item_type" name="item_type" required>
                <option value="">Select an item type</option>
                <option value="flyers">Flyers</option>
                <option value="handouts">Handouts</option>
                <option value="other">Other</option>
            </select>

            <div id="other_item_field">
                <label for="other_item">Specify Item (if not included in dropdown) </label>
                <input type="text" id="other_item" name="other_item" placeholder="Enter the item type">
            </div>

            <label for="quantity"><em>* </em>Quantity</label>
            <input type="number" id="quantity" name="quantity" min="1" required placeholder="Enter quantity needed">

            <label for="description"><em>* </em>Description</label>
            <textarea id="description" name="description" rows="6" required placeholder="Describe the materials needed and any additional details"></textarea>
        </fieldset>
            
        <input type="submit" name="supply-form" value="Submit Request" class="blue-button">
    </form>
   </div> 
</main>
