/**
 * Theme Toggle Functionality
 * Handles switching between light and dark modes
 */

window.themeToggle = window.themeToggle || {
  toggle: function () {
    const currentTheme = document.documentElement.hasAttribute("data-theme")
      ? "dark"
      : "light";
    const newTheme = currentTheme === "dark" ? "light" : "dark";
    if (newTheme === "dark") {
      document.documentElement.setAttribute("data-theme", "dark");
    } else {
      document.documentElement.removeAttribute("data-theme");
    }
    localStorage.setItem("theme", newTheme);
  },
  getCurrentTheme: function () {
    return document.documentElement.hasAttribute("data-theme")
      ? "dark"
      : "light";
  },
  applyTheme: function (theme) {
    if (theme === "dark") {
      document.documentElement.setAttribute("data-theme", "dark");
    } else {
      document.documentElement.removeAttribute("data-theme");
    }
    localStorage.setItem("theme", theme);
  },
};

// Initialize theme on page load
document.addEventListener("DOMContentLoaded", function () {
  initializeTheme();
  setupThemeToggle();

  // Also set up observer to catch mobile menu if it loads late
  const observer = new MutationObserver(function (mutations) {
    const mobileThemeToggle = document.getElementById("mobileThemeToggle");
    if (
      mobileThemeToggle &&
      !mobileThemeToggle.hasAttribute("data-listener-attached")
    ) {
      console.log("🔄 Mobile menu detected late, attaching listener now");
      mobileThemeToggle.setAttribute("data-listener-attached", "true");

      mobileThemeToggle.addEventListener("click", function (event) {
        console.log("🖱️ Mobile theme toggle clicked (late attach)!");
        event.preventDefault();
        event.stopPropagation();

        const currentTheme = getCurrentTheme();
        const newTheme = currentTheme === "dark" ? "light" : "dark";
        console.log(`🎨 Switching from ${currentTheme} to ${newTheme}`);

        applyTheme(newTheme);
        updateMobileThemeLabel(newTheme);

        console.log("✅ Theme switched successfully");
      });

      updateMobileThemeLabel(getCurrentTheme());
    }
  });

  observer.observe(document.body, { childList: true, subtree: true });
});

/**
 * Initialize theme based on user preference or system preference
 */
function initializeTheme() {
  // Check for saved theme preference or default to system preference
  const savedTheme = localStorage.getItem("theme");
  const systemPrefersDark = window.matchMedia(
    "(prefers-color-scheme: dark)",
  ).matches;

  let theme = "light"; // default

  if (savedTheme) {
    theme = savedTheme;
  } else if (systemPrefersDark) {
    theme = "dark";
  }

  applyTheme(theme);
}

/**
 * Apply the specified theme
 * @param {string} theme - 'light' or 'dark'
 */
function applyTheme(theme) {
  if (theme === "dark") {
    document.documentElement.setAttribute("data-theme", "dark");
  } else {
    document.documentElement.removeAttribute("data-theme");
  }

  // Save preference
  localStorage.setItem("theme", theme);

  // Update toggle button state
  updateToggleButton(theme);
}

/**
 * Update the toggle button appearance based on current theme
 * @param {string} theme - current theme
 */
function updateToggleButton(theme) {
  const toggleButtons = document.querySelectorAll(".theme-toggle");
  toggleButtons.forEach((toggleButton) => {
    toggleButton.setAttribute(
      "aria-label",
      theme === "dark" ? "Switch to light mode" : "Switch to dark mode",
    );
    toggleButton.setAttribute(
      "title",
      theme === "dark" ? "Switch to light mode" : "Switch to dark mode",
    );
  });

  // Update mobile theme label
  updateMobileThemeLabel(theme);
}

/**
 * Update the mobile theme toggle label
 * @param {string} theme - current theme
 */
function updateMobileThemeLabel(theme) {
  const mobileLabel = document.getElementById("mobileThemeLabel");
  if (mobileLabel) {
    mobileLabel.textContent = theme === "dark" ? "Dark Mode ✓" : "Light Mode ✓";
  }
}

/**
 * Setup theme toggle button event listener
 */
function setupThemeToggle() {
  // Use event delegation for better reliability with dynamically loaded content
  document.addEventListener("click", function (event) {
    if (event.target.closest(".theme-toggle")) {
      handleThemeToggle(event);
    }
  });

  // Also attach directly for better performance on desktop
  const toggleButtons = document.querySelectorAll(".theme-toggle");
  toggleButtons.forEach((button) => {
    button.addEventListener("click", handleThemeToggle);
  });

  const mobileThemeToggle = document.getElementById("mobileThemeToggle");
  if (
    mobileThemeToggle &&
    !mobileThemeToggle.hasAttribute("data-listener-attached")
  ) {
    console.log("✅ Mobile theme toggle button found, attaching listener");
    mobileThemeToggle.setAttribute("data-listener-attached", "true");

    mobileThemeToggle.addEventListener("click", function (event) {
      console.log("🖱️ Mobile theme toggle clicked!");
      event.preventDefault();
      event.stopPropagation();

      const currentTheme = getCurrentTheme();
      const newTheme = currentTheme === "dark" ? "light" : "dark";
      console.log(`🎨 Switching from ${currentTheme} to ${newTheme}`);

      applyTheme(newTheme);

      // Update the label text
      updateMobileThemeLabel(newTheme);

      console.log("✅ Theme switched successfully");
      // DON'T close the mobile menu - user can toggle multiple times
    });

    // Set initial label
    updateMobileThemeLabel(getCurrentTheme());
  } else if (!mobileThemeToggle) {
  }
}

/**
 * Handle theme toggle button click
 */
function handleThemeToggle(event) {
  // Prevent default and stop propagation to ensure click works
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }

  const currentTheme = getCurrentTheme();
  const newTheme = currentTheme === "dark" ? "light" : "dark";
  applyTheme(newTheme);

  // Close mobile more menu if clicking from mobile
  if (event && event.target.closest(".mobile-more-item")) {
    const mobileMoreMenu = document.getElementById("mobileMoreMenu");
    if (mobileMoreMenu) {
      mobileMoreMenu.classList.remove("active");
      document.body.style.overflow = "";
    }
  }
}

/**
 * Get current theme
 * @returns {string} 'light' or 'dark'
 */
function getCurrentTheme() {
  return document.documentElement.hasAttribute("data-theme") ? "dark" : "light";
}

/**
 * Toggle theme (public function for external use)
 */
function toggleTheme() {
  const currentTheme = getCurrentTheme();
  const newTheme = currentTheme === "dark" ? "light" : "dark";
  applyTheme(newTheme);
}

// Listen for system theme changes
window
  .matchMedia("(prefers-color-scheme: dark)")
  .addEventListener("change", function (e) {
    // Only auto-switch if user hasn't manually set a preference
    if (!localStorage.getItem("theme")) {
      applyTheme(e.matches ? "dark" : "light");
    }
  });

window.themeToggle.toggle = toggleTheme;
window.themeToggle.getCurrentTheme = getCurrentTheme;
window.themeToggle.applyTheme = applyTheme;
