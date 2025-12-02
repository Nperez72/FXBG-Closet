/**
 * Theme Presets Initialization (Early Load)
 * This script loads and applies saved theme presets before the page fully renders
 * to prevent flash of unstyled content
 */

(function() {
    'use strict';
    
    const storageKey = 'fxbg-theme-preset';
    
    const themes = {
        'olivia': {
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
        },
        'laser': {
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
        },
        'nautilus': {
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
        },
        'striker': {
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
        },
        'cafe': {
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
    };
    
    try {
        // Load saved theme
        const savedTheme = localStorage.getItem(storageKey) || 'olivia';
        console.log('🎨 [INIT] Loading theme:', savedTheme);
        
        const themeColors = themes[savedTheme];
        
        if (themeColors) {
            // Create CSS rules with ULTRA HIGH SPECIFICITY
            let cssRules = '';
            
            console.log('🎨 [INIT] Applying theme colors...');
            
            // Apply to :root (light mode) with multiple specificity
            cssRules += ':root:root {\n';
            for (const [property, value] of Object.entries(themeColors)) {
                cssRules += `  ${property}: ${value} !important;\n`;
            }
            cssRules += '}\n\n';
            
            // Apply to html (extra specificity)
            cssRules += 'html:root {\n';
            for (const [property, value] of Object.entries(themeColors)) {
                cssRules += `  ${property}: ${value} !important;\n`;
            }
            cssRules += '}\n\n';
            
            // ALSO apply to [data-theme="dark"] with HIGH SPECIFICITY
            // AND override dark-mode-specific variables
            cssRules += 'html[data-theme="dark"]:root, [data-theme="dark"]:root, html[data-theme="dark"], [data-theme="dark"] {\n';
            for (const [property, value] of Object.entries(themeColors)) {
                cssRules += `  ${property}: ${value} !important;\n`;
            }
            // Override additional dark mode variables to use theme colors
            cssRules += `  --navbar-bg: var(--main-color) !important;\n`;
            cssRules += `  --full-width-bar-bg: var(--main-color) !important;\n`;
            cssRules += `  --dropdown-border: var(--main-color) !important;\n`;
            cssRules += `  --content-box-border: var(--main-color) !important;\n`;
            cssRules += `  --card-border: var(--main-color) !important;\n`;
            cssRules += `  --logo-bg: var(--main-color) !important;\n`;
            cssRules += `  --button-bg: var(--main-color) !important;\n`;
            // Background and text colors for dark mode
            cssRules += `  --bg-color: #1a1a1a !important;\n`;
            cssRules += `  --card-bg: #2a2a2a !important;\n`;
            cssRules += `  --content-box-bg: #2a2a2a !important;\n`;
            cssRules += `  --dropdown-bg: #2a2a2a !important;\n`;
            cssRules += `  --standout-background: #2a2a2a !important;\n`;
            cssRules += `  --page-background-color: #1a1a1a !important;\n`;
            cssRules += `  --text-color: #e0e0e0 !important;\n`;
            cssRules += `  --text-secondary: #c0c0c0 !important;\n`;
            cssRules += `  --page-font-color: #e0e0e0 !important;\n`;
            cssRules += `  --button-font-color: var(--button-text) !important;\n`;
            cssRules += '}';
            
            // Inject style tag - will be at end of head when stylesheets finish loading
            const styleTag = document.createElement('style');
            styleTag.id = 'theme-preset-override';
            styleTag.textContent = cssRules;
            document.head.appendChild(styleTag);
            
        } else {
        }
    } catch (error) {
    }
})();
