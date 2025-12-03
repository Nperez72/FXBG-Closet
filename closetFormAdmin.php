<header class="hero-header"> 
    <div class="center-header">
        <h1>Pride Closet Form</h1>
    </div>
</header>

<main>
  <div class="main-content-box w-full max-w-3xl p-8 mb-8">
    <form class="signup-form" method="post">
        <div class="text-center mb-8">
          <h2 class="mb-8">Closet Addition Fillout</h2>
            <div class="main-content-box border-2 mb-0 shadow-xs w-full p-4">
              <p class="sub-text">Update inventory quantity here.</p>
              <p>An asterisk (<em>*</em>) indicates a required field.</p>
            </div>
        </div>
        
        <fieldset class="section-box mb-4">
            <h3 class="mt-2">Closet Update Details</h3>
            <p class="mb-2">Please provide information about the items added.</p>
            <div class="blue-div"></div>
            <label for="item_type"><em>* </em>Item Type</label>
            <select id="item_type" name="item_type" required>
                <option value="">Select an item type</option>
                <option value="clothing">Clothing</option>
                <option value="shoes">Shoes</option>
                <option value="accessories">Accessories</option>
                <option value="hygiene">Hygiene Products</option>
            </select>

            <label for="quantity"><em>* </em>Quantity</label>
            <input type="number" id="quantity" name="quantity" min="1" required placeholder="Enter quantity added">

        </fieldset>
            
        <input type="submit" name="closet-addition" value="Submit" class="blue-button">
    </form>
   </div> 
</main>
