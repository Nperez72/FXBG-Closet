/**
 * Theme Presets Manager
 * Allows users to choose from different color scheme presets
 * Works alongside the dark/light mode toggle
 */

class ThemePresets {
    constructor() {
        this.storageKey = 'fxbg-theme-preset';
        
        this.themes = {
            'olivia': {
                name: 'GMK Olivia',
                description: 'Soft peach with cream accents',
                icon: '⌨️',
                colors: {
                    '--main-color': '#e8c4b8',
                    '--accent-color': '#d4af37',
                    '--button-text': '#363434',
                    '--date-box-bg': '#e8c4b8',
                    '--date-box-text': '#363434',
                    '--text-tertiary': '#e8c4b8',
                    '--secondary-accent-color': '#e8c4b8',
                    '--dropdown-border': '#e8c4b8',
                    '--dropdown-hover': 'rgba(232, 196, 184, 0.1)',
                    '--nav-item-hover': '#e8c4b8',
                    '--content-box-border': '#e8c4b8',
                    '--card-border': '#e8c4b8',
                    '--card-shadow': 'rgba(232, 196, 184, 0.15)',
                    '--button-bg': '#e8c4b8',
                    '--logo-bg': '#e8c4b8',
                    '--border-color': '#e8c4b8',
                    '--full-width-bar-bg': '#e8c4b8',
                    '--nav-item-active-bg': '#f4ede9',
                    '--svg-fill': '#e8c4b8',
                    '--inactive-background-color': '#f4ede9',
                    '--shadow-and-border-color': '#e8c4b8',
                    '--calendar-event-color-hover': '#e8c4b8'
                }
            },
            'laser': {
                name: 'GMK Laser',
                description: 'Cyberpunk purple and cyan',
                icon: '🔮',
                colors: {
                    '--main-color': '#b967ff',
                    '--accent-color': '#05d9e8',
                    '--button-text': '#ffffff',
                    '--date-box-bg': '#b967ff',
                    '--date-box-text': '#ffffff',
                    '--text-tertiary': '#b967ff',
                    '--secondary-accent-color': '#b967ff',
                    '--dropdown-border': '#b967ff',
                    '--dropdown-hover': 'rgba(185, 103, 255, 0.1)',
                    '--nav-item-hover': '#b967ff',
                    '--content-box-border': '#b967ff',
                    '--card-border': '#b967ff',
                    '--card-shadow': 'rgba(185, 103, 255, 0.15)',
                    '--button-bg': '#b967ff',
                    '--logo-bg': '#b967ff',
                    '--border-color': '#b967ff',
                    '--full-width-bar-bg': '#b967ff',
                    '--nav-item-active-bg': '#1a1a2e',
                    '--svg-fill': '#b967ff',
                    '--inactive-background-color': '#1a1a2e',
                    '--shadow-and-border-color': '#b967ff',
                    '--calendar-event-color-hover': '#b967ff'
                }
            },
            'nautilus': {
                name: 'GMK Nautilus',
                description: 'Deep navy with gold details',
                icon: '⚓',
                colors: {
                    '--main-color': '#1e3a5f',
                    '--accent-color': '#d4af37',
                    '--button-text': '#ffffff',
                    '--date-box-bg': '#1e3a5f',
                    '--date-box-text': '#ffffff',
                    '--text-tertiary': '#1e3a5f',
                    '--secondary-accent-color': '#1e3a5f',
                    '--dropdown-border': '#1e3a5f',
                    '--dropdown-hover': 'rgba(30, 58, 95, 0.1)',
                    '--nav-item-hover': '#1e3a5f',
                    '--content-box-border': '#1e3a5f',
                    '--card-border': '#1e3a5f',
                    '--card-shadow': 'rgba(30, 58, 95, 0.15)',
                    '--button-bg': '#1e3a5f',
                    '--logo-bg': '#1e3a5f',
                    '--border-color': '#1e3a5f',
                    '--full-width-bar-bg': '#1e3a5f',
                    '--nav-item-active-bg': '#e8eef5',
                    '--svg-fill': '#1e3a5f',
                    '--inactive-background-color': '#e8eef5',
                    '--shadow-and-border-color': '#1e3a5f',
                    '--calendar-event-color-hover': '#1e3a5f'
                }
            },
            'striker': {
                name: 'GMK Striker',
                description: 'Bold orange and black',
                icon: '⚡',
                colors: {
                    '--main-color': '#ff6b35',
                    '--accent-color': '#2c3e50',
                    '--button-text': '#ffffff',
                    '--date-box-bg': '#ff6b35',
                    '--date-box-text': '#ffffff',
                    '--text-tertiary': '#ff6b35',
                    '--secondary-accent-color': '#ff6b35',
                    '--dropdown-border': '#ff6b35',
                    '--dropdown-hover': 'rgba(255, 107, 53, 0.1)',
                    '--nav-item-hover': '#ff6b35',
                    '--content-box-border': '#ff6b35',
                    '--card-border': '#ff6b35',
                    '--card-shadow': 'rgba(255, 107, 53, 0.15)',
                    '--button-bg': '#ff6b35',
                    '--logo-bg': '#ff6b35',
                    '--border-color': '#ff6b35',
                    '--full-width-bar-bg': '#ff6b35',
                    '--nav-item-active-bg': '#f5f5f5',
                    '--svg-fill': '#ff6b35',
                    '--inactive-background-color': '#f5f5f5',
                    '--shadow-and-border-color': '#ff6b35',
                    '--calendar-event-color-hover': '#ff6b35'
                }
            },
            'cafe': {
                name: 'GMK Café',
                description: 'Warm coffee browns',
                icon: '☕',
                colors: {
                    '--main-color': '#8b5e3c',
                    '--accent-color': '#c19a6b',
                    '--button-text': '#ffffff',
                    '--date-box-bg': '#8b5e3c',
                    '--date-box-text': '#ffffff',
                    '--text-tertiary': '#8b5e3c',
                    '--secondary-accent-color': '#8b5e3c',
                    '--dropdown-border': '#8b5e3c',
                    '--dropdown-hover': 'rgba(139, 94, 60, 0.1)',
                    '--nav-item-hover': '#8b5e3c',
                    '--content-box-border': '#8b5e3c',
                    '--card-border': '#8b5e3c',
                    '--card-shadow': 'rgba(139, 94, 60, 0.15)',
                    '--button-bg': '#8b5e3c',
                    '--logo-bg': '#8b5e3c',
                    '--border-color': '#8b5e3c',
                    '--full-width-bar-bg': '#8b5e3c',
                    '--nav-item-active-bg': '#f5f0e8',
                    '--svg-fill': '#8b5e3c',
                    '--inactive-background-color': '#f5f0e8',
                    '--shadow-and-border-color': '#8b5e3c',
                    '--calendar-event-color-hover': '#8b5e3c'
                }
            }
        };

    this.init();
  }

  init() {
    this.loadTheme();
    this.applyTheme();
    this.attachEventListeners();
  }

    loadTheme() {
        try {
            const saved = localStorage.getItem(this.storageKey);
            this.currentTheme = saved || 'olivia';
        } catch (error) {
            console.error('Error loading theme preset:', error);
            this.currentTheme = 'olivia';
        }
    }

    applyTheme(themeName = this.currentTheme) {
        console.log('🎨 Applying theme:', themeName);
        
        const theme = this.themes[themeName];
        if (!theme) {
            console.warn(`Theme '${themeName}' not found, using olivia`);
            themeName = 'olivia';
        }

        this.currentTheme = themeName;
        
        // Remove existing theme preset style tag if it exists
        const existingStyle = document.getElementById('theme-preset-override');
        if (existingStyle) {
            existingStyle.remove();
        }
        
        // Create CSS rules with ULTRA HIGH SPECIFICITY to override everything
        const colors = this.themes[themeName].colors;
        let cssRules = '';
        
        // Apply to :root (light mode) with multiple specificity levels
        cssRules += ':root:root {\n';
        for (const [property, value] of Object.entries(colors)) {
            cssRules += `  ${property}: ${value} !important;\n`;
        }
        cssRules += '}\n\n';
        
        // Apply to html (extra specificity)
        cssRules += 'html:root {\n';
        for (const [property, value] of Object.entries(colors)) {
            cssRules += `  ${property}: ${value} !important;\n`;
        }
        cssRules += '}\n\n';
        
        // ALSO apply to [data-theme="dark"] with HIGH SPECIFICITY
        // In dark mode, ONLY override accent colors, keep dark backgrounds
        cssRules += 'html[data-theme="dark"]:root, [data-theme="dark"]:root, html[data-theme="dark"], [data-theme="dark"] {\n';
        // Apply theme colors to accents/borders/buttons only
        cssRules += `  --main-color: ${colors['--main-color']} !important;\n`;
        cssRules += `  --accent-color: ${colors['--accent-color']} !important;\n`;
        cssRules += `  --button-bg: ${colors['--main-color']} !important;\n`;
        cssRules += `  --border-color: ${colors['--main-color']} !important;\n`;
        cssRules += `  --card-border: ${colors['--main-color']} !important;\n`;
        cssRules += `  --content-box-border: ${colors['--main-color']} !important;\n`;
        cssRules += `  --dropdown-border: ${colors['--main-color']} !important;\n`;
        cssRules += `  --nav-item-hover: ${colors['--main-color']} !important;\n`;
        cssRules += `  --shadow-and-border-color: ${colors['--main-color']} !important;\n`;
        cssRules += `  --svg-fill: ${colors['--main-color']} !important;\n`;
        cssRules += `  --calendar-event-color-hover: ${colors['--main-color']} !important;\n`;
        cssRules += `  --button-text: ${colors['--button-text']} !important;\n`;
        // Keep dark backgrounds - DON'T override these
        cssRules += `  --bg-color: #1a1a1a !important;\n`;
        cssRules += `  --navbar-bg: #2a2826 !important;\n`;
        cssRules += `  --card-bg: #2a2826 !important;\n`;
        cssRules += `  --content-box-bg: #2a2826 !important;\n`;
        cssRules += `  --dropdown-bg: #2a2826 !important;\n`;
        cssRules += `  --standout-background: #2a2826 !important;\n`;
        cssRules += `  --page-background-color: #1a1a1a !important;\n`;
        cssRules += `  --full-width-bar-bg: #2a2826 !important;\n`;
        // Keep light text in dark mode
        cssRules += `  --text-color: #e0e0e0 !important;\n`;
        cssRules += `  --text-secondary: #c0c0c0 !important;\n`;
        cssRules += `  --page-font-color: #e0e0e0 !important;\n`;
        cssRules += '}';
        
        // Inject style tag at the VERY END of head to ensure it loads last
        const styleTag = document.createElement('style');
        styleTag.id = 'theme-preset-override';
        styleTag.textContent = cssRules;
        // Append to end of head (loads after all other stylesheets)
        document.head.appendChild(styleTag);
        

        // Verify variables were set
        const mainColor = getComputedStyle(document.documentElement).getPropertyValue('--main-color');

    // Save to localStorage
    try {
      localStorage.setItem(this.storageKey, themeName);
    } catch (error) {
      console.error("Error saving theme preset:", error);
    }

        // Update UI
        this.updateThemeSelector();
        
        // Dispatch custom event for other scripts
        window.dispatchEvent(new CustomEvent('themePresetChanged', { 
            detail: { theme: themeName } 
        }));
        
    }

  updateThemeSelector() {
    // Update active state in theme cards
    document.querySelectorAll(".theme-card").forEach((card) => {
      const themeName = card.dataset.theme;
      if (themeName === this.currentTheme) {
        card.classList.add("active");
      } else {
        card.classList.remove("active");
      }
    });

        // Update button badge/indicator
        const themeBtn = document.getElementById('openThemePresets');
        if (themeBtn && this.currentTheme !== 'olivia') {
            themeBtn.classList.add('customized');
        } else if (themeBtn) {
            themeBtn.classList.remove('customized');
        }
    }

  attachEventListeners() {
    // Modal toggle
    const openBtn = document.getElementById("openThemePresets");
    const openBtnMobile = document.getElementById("openThemePresetsMobile");
    const closeBtn = document.getElementById("closeThemePresets");
    const modal = document.getElementById("themePresetsModal");

    if (openBtn && modal) {
      openBtn.addEventListener("click", (e) => {
        e.preventDefault();
        modal.classList.add("active");
        document.body.style.overflow = "hidden";
        this.updateThemeSelector();
      });
    }

    if (openBtnMobile && modal) {
      openBtnMobile.addEventListener("click", (e) => {
        e.preventDefault();
        // Close mobile more menu
        const mobileMoreMenu = document.getElementById("mobileMoreMenu");
        if (mobileMoreMenu) {
          mobileMoreMenu.classList.remove("active");
        }
        modal.classList.add("active");
        document.body.style.overflow = "hidden";
        this.updateThemeSelector();
      });
    }

    if (closeBtn && modal) {
      closeBtn.addEventListener("click", () => {
        modal.classList.remove("active");
        document.body.style.overflow = "";
      });

      // Close on backdrop click
      modal.addEventListener("click", (e) => {
        if (e.target === modal) {
          modal.classList.remove("active");
          document.body.style.overflow = "";
        }
      });
    }

    // Close on escape key
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && modal && modal.classList.contains("active")) {
        modal.classList.remove("active");
        document.body.style.overflow = "";
      }
    });

    // Theme card clicks
    document.addEventListener("click", (e) => {
      const themeCard = e.target.closest(".theme-card");
      if (themeCard) {
        const themeName = themeCard.dataset.theme;
        this.applyTheme(themeName);
      }
    });
  }

  getCurrentTheme() {
    return this.currentTheme;
  }

  getThemeInfo(themeName = this.currentTheme) {
    return this.themes[themeName];
  }
}

// Initialize when DOM is ready
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => {
    window.themePresets = new ThemePresets();
  });
} else {
  window.themePresets = new ThemePresets();
}
