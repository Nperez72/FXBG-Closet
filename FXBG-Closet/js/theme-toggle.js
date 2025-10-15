/**
 * Theme Toggle Functionality
 * Handles switching between light and dark modes
 */

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeTheme();
    setupThemeToggle();
});

/**
 * Initialize theme based on user preference or system preference
 */
function initializeTheme() {
    // Check for saved theme preference or default to system preference
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    let theme = 'light'; // default
    
    if (savedTheme) {
        theme = savedTheme;
    } else if (systemPrefersDark) {
        theme = 'dark';
    }
    
    applyTheme(theme);
}

/**
 * Apply the specified theme
 * @param {string} theme - 'light' or 'dark'
 */
function applyTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        document.documentElement.removeAttribute('data-theme');
    }
    
    // Save preference
    localStorage.setItem('theme', theme);
    
    // Update toggle button state
    updateToggleButton(theme);
}

/**
 * Update the toggle button appearance based on current theme
 * @param {string} theme - current theme
 */
function updateToggleButton(theme) {
    const toggleButton = document.querySelector('.theme-toggle');
    if (toggleButton) {
        toggleButton.setAttribute('aria-label', 
            theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'
        );
        toggleButton.setAttribute('title', 
            theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'
        );
    }
}

/**
 * Setup theme toggle button event listener
 */
function setupThemeToggle() {
    const toggleButton = document.querySelector('.theme-toggle');
    if (toggleButton) {
        toggleButton.addEventListener('click', handleThemeToggle);
    }
}

/**
 * Handle theme toggle button click
 */
function handleThemeToggle() {
    const currentTheme = getCurrentTheme();
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    applyTheme(newTheme);
}

/**
 * Get current theme
 * @returns {string} 'light' or 'dark'
 */
function getCurrentTheme() {
    return document.documentElement.hasAttribute('data-theme') ? 'dark' : 'light';
}

/**
 * Toggle theme (public function for external use)
 */
function toggleTheme() {
    const currentTheme = getCurrentTheme();
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    applyTheme(newTheme);
}

// Listen for system theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
    // Only auto-switch if user hasn't manually set a preference
    if (!localStorage.getItem('theme')) {
        applyTheme(e.matches ? 'dark' : 'light');
    }
});

// Export functions for external use
window.themeToggle = {
    toggle: toggleTheme,
    getCurrentTheme: getCurrentTheme,
    applyTheme: applyTheme
};

