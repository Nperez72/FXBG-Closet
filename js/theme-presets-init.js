/**
 * Theme Presets Initialization (Early Load)
 * This script loads and applies saved theme presets before the page fully renders
 * to prevent flash of unstyled content
 */

(function () {
  "use strict";

  const storageKey = "fxbg-theme-preset";

  // Theme definitions (must match theme-presets.js)
  const themes = {
    default: {
      "--main-color": "#e8c4b8",
      "--accent-color": "#d4af37",
      "--button-text": "#363434",
      "--date-box-bg": "#e8c4b8",
      "--date-box-text": "#363434",
      "--text-tertiary": "#e8c4b8",
      "--secondary-accent-color": "#e8c4b8",
      "--dropdown-border": "#e8c4b8",
      "--dropdown-hover": "rgba(232, 196, 184, 0.1)",
      "--nav-item-hover": "#e8c4b8",
      "--content-box-border": "#e8c4b8",
      "--card-border": "#e8c4b8",
      "--card-shadow": "rgba(232, 196, 184, 0.15)",
      "--button-bg": "#e8c4b8",
      "--logo-bg": "#e8c4b8",
      "--border-color": "#e8c4b8",
      "--full-width-bar-bg": "#e8c4b8",
      "--nav-item-active-bg": "#f4ede9",
      "--svg-fill": "#e8c4b8",
      "--inactive-background-color": "#f4ede9",
      "--shadow-and-border-color": "#e8c4b8",
      "--calendar-event-color-hover": "#e8c4b8",
    },
    pride: {
      "--main-color": "#ff6b9d",
      "--accent-color": "#9b59b6",
      "--button-text": "#ffffff",
      "--date-box-bg":
        "linear-gradient(135deg, #ff6b9d, #c06c84, #6c5b7b, #355c7d)",
      "--date-box-text": "#ffffff",
      "--text-tertiary": "#ff6b9d",
      "--secondary-accent-color": "#ff6b9d",
      "--dropdown-border": "#ff6b9d",
      "--dropdown-hover": "rgba(255, 107, 157, 0.1)",
      "--nav-item-hover": "#ff6b9d",
      "--content-box-border": "#ff6b9d",
      "--card-border": "#ff6b9d",
      "--card-shadow": "rgba(155, 89, 182, 0.15)",
      "--button-bg": "#ff6b9d",
      "--logo-bg": "#ff6b9d",
      "--border-color": "#ff6b9d",
      "--full-width-bar-bg": "#ff6b9d",
      "--nav-item-active-bg": "#fce4ec",
      "--svg-fill": "#ff6b9d",
      "--inactive-background-color": "#fce4ec",
      "--shadow-and-border-color": "#ff6b9d",
      "--calendar-event-color-hover": "#ff6b9d",
    },
    ocean: {
      "--main-color": "#5dade2",
      "--accent-color": "#3498db",
      "--button-text": "#ffffff",
      "--date-box-bg": "#5dade2",
      "--date-box-text": "#ffffff",
      "--text-tertiary": "#5dade2",
      "--secondary-accent-color": "#5dade2",
      "--dropdown-border": "#5dade2",
      "--dropdown-hover": "rgba(93, 173, 226, 0.1)",
      "--nav-item-hover": "#5dade2",
      "--content-box-border": "#5dade2",
      "--card-border": "#5dade2",
      "--card-shadow": "rgba(52, 152, 219, 0.15)",
      "--button-bg": "#5dade2",
      "--logo-bg": "#5dade2",
      "--border-color": "#5dade2",
      "--full-width-bar-bg": "#5dade2",
      "--nav-item-active-bg": "#e3f2fd",
      "--svg-fill": "#5dade2",
      "--inactive-background-color": "#e3f2fd",
      "--shadow-and-border-color": "#5dade2",
      "--calendar-event-color-hover": "#5dade2",
    },
    forest: {
      "--main-color": "#52b788",
      "--accent-color": "#2d6a4f",
      "--button-text": "#ffffff",
      "--date-box-bg": "#52b788",
      "--date-box-text": "#ffffff",
      "--text-tertiary": "#52b788",
      "--secondary-accent-color": "#52b788",
      "--dropdown-border": "#52b788",
      "--dropdown-hover": "rgba(82, 183, 136, 0.1)",
      "--nav-item-hover": "#52b788",
      "--content-box-border": "#52b788",
      "--card-border": "#52b788",
      "--card-shadow": "rgba(45, 106, 79, 0.15)",
      "--button-bg": "#52b788",
      "--logo-bg": "#52b788",
      "--border-color": "#52b788",
      "--full-width-bar-bg": "#52b788",
      "--nav-item-active-bg": "#e8f5e9",
      "--svg-fill": "#52b788",
      "--inactive-background-color": "#e8f5e9",
      "--shadow-and-border-color": "#52b788",
      "--calendar-event-color-hover": "#52b788",
    },
    sunset: {
      "--main-color": "#9b59b6",
      "--accent-color": "#8e44ad",
      "--button-text": "#ffffff",
      "--date-box-bg": "#9b59b6",
      "--date-box-text": "#ffffff",
      "--text-tertiary": "#9b59b6",
      "--secondary-accent-color": "#9b59b6",
      "--dropdown-border": "#9b59b6",
      "--dropdown-hover": "rgba(155, 89, 182, 0.1)",
      "--nav-item-hover": "#9b59b6",
      "--content-box-border": "#9b59b6",
      "--card-border": "#9b59b6",
      "--card-shadow": "rgba(142, 68, 173, 0.15)",
      "--button-bg": "#9b59b6",
      "--logo-bg": "#9b59b6",
      "--border-color": "#9b59b6",
      "--full-width-bar-bg": "#9b59b6",
      "--nav-item-active-bg": "#f3e5f5",
      "--svg-fill": "#9b59b6",
      "--inactive-background-color": "#f3e5f5",
      "--shadow-and-border-color": "#9b59b6",
      "--calendar-event-color-hover": "#9b59b6",
    },
  };

  try {
    // Load saved theme
    const savedTheme = localStorage.getItem(storageKey) || "default";
    const themeColors = themes[savedTheme];

    if (themeColors) {
      // Apply theme colors immediately
      const root = document.documentElement;
      for (const [property, value] of Object.entries(themeColors)) {
        root.style.setProperty(property, value);
      }
    }
  } catch (error) {
    // Silent fail if localStorage isn't available
    console.warn("Theme presets init failed:", error);
  }
})();
