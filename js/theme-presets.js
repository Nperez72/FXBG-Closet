/**
 * Theme Presets Manager
 * Allows users to choose from different color scheme presets
 * Works alongside the dark/light mode toggle
 */

class ThemePresets {
    constructor() {
        this.storageKey = 'fxbg-theme-preset';
        
        // Define 5 color scheme presets
        this.themes = {
            'default': {
                name: 'Classic Peach',
                description: 'Warm and welcoming peach tones',
                icon: '🍑',
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
            'pride': {
                name: 'Pride Rainbow',
                description: 'Vibrant pride colors',
                icon: '🏳️‍🌈',
                colors: {
                    '--main-color': '#ff6b9d',
                    '--accent-color': '#9b59b6',
                    '--button-text': '#ffffff',
                    '--date-box-bg': 'linear-gradient(135deg, #ff6b9d, #c06c84, #6c5b7b, #355c7d)',
                    '--date-box-text': '#ffffff',
                    '--text-tertiary': '#ff6b9d',
                    '--secondary-accent-color': '#ff6b9d',
                    '--dropdown-border': '#ff6b9d',
                    '--dropdown-hover': 'rgba(255, 107, 157, 0.1)',
                    '--nav-item-hover': '#ff6b9d',
                    '--content-box-border': '#ff6b9d',
                    '--card-border': '#ff6b9d',
                    '--card-shadow': 'rgba(155, 89, 182, 0.15)',
                    '--button-bg': '#ff6b9d',
                    '--logo-bg': '#ff6b9d',
                    '--border-color': '#ff6b9d',
                    '--full-width-bar-bg': '#ff6b9d',
                    '--nav-item-active-bg': '#fce4ec',
                    '--svg-fill': '#ff6b9d',
                    '--inactive-background-color': '#fce4ec',
                    '--shadow-and-border-color': '#ff6b9d',
                    '--calendar-event-color-hover': '#ff6b9d'
                }
            },
            'ocean': {
                name: 'Ocean Blue',
                description: 'Calm and professional blues',
                icon: '🌊',
                colors: {
                    '--main-color': '#5dade2',
                    '--accent-color': '#3498db',
                    '--button-text': '#ffffff',
                    '--date-box-bg': '#5dade2',
                    '--date-box-text': '#ffffff',
                    '--text-tertiary': '#5dade2',
                    '--secondary-accent-color': '#5dade2',
                    '--dropdown-border': '#5dade2',
                    '--dropdown-hover': 'rgba(93, 173, 226, 0.1)',
                    '--nav-item-hover': '#5dade2',
                    '--content-box-border': '#5dade2',
                    '--card-border': '#5dade2',
                    '--card-shadow': 'rgba(52, 152, 219, 0.15)',
                    '--button-bg': '#5dade2',
                    '--logo-bg': '#5dade2',
                    '--border-color': '#5dade2',
                    '--full-width-bar-bg': '#5dade2',
                    '--nav-item-active-bg': '#e3f2fd',
                    '--svg-fill': '#5dade2',
                    '--inactive-background-color': '#e3f2fd',
                    '--shadow-and-border-color': '#5dade2',
                    '--calendar-event-color-hover': '#5dade2'
                }
            },
            'forest': {
                name: 'Forest Green',
                description: 'Natural and earthy greens',
                icon: '🌲',
                colors: {
                    '--main-color': '#52b788',
                    '--accent-color': '#2d6a4f',
                    '--button-text': '#ffffff',
                    '--date-box-bg': '#52b788',
                    '--date-box-text': '#ffffff',
                    '--text-tertiary': '#52b788',
                    '--secondary-accent-color': '#52b788',
                    '--dropdown-border': '#52b788',
                    '--dropdown-hover': 'rgba(82, 183, 136, 0.1)',
                    '--nav-item-hover': '#52b788',
                    '--content-box-border': '#52b788',
                    '--card-border': '#52b788',
                    '--card-shadow': 'rgba(45, 106, 79, 0.15)',
                    '--button-bg': '#52b788',
                    '--logo-bg': '#52b788',
                    '--border-color': '#52b788',
                    '--full-width-bar-bg': '#52b788',
                    '--nav-item-active-bg': '#e8f5e9',
                    '--svg-fill': '#52b788',
                    '--inactive-background-color': '#e8f5e9',
                    '--shadow-and-border-color': '#52b788',
                    '--calendar-event-color-hover': '#52b788'
                }
            },
            'sunset': {
                name: 'Sunset Purple',
                description: 'Rich and elegant purples',
                icon: '🌅',
                colors: {
                    '--main-color': '#9b59b6',
                    '--accent-color': '#8e44ad',
                    '--button-text': '#ffffff',
                    '--date-box-bg': '#9b59b6',
                    '--date-box-text': '#ffffff',
                    '--text-tertiary': '#9b59b6',
                    '--secondary-accent-color': '#9b59b6',
                    '--dropdown-border': '#9b59b6',
                    '--dropdown-hover': 'rgba(155, 89, 182, 0.1)',
                    '--nav-item-hover': '#9b59b6',
                    '--content-box-border': '#9b59b6',
                    '--card-border': '#9b59b6',
                    '--card-shadow': 'rgba(142, 68, 173, 0.15)',
                    '--button-bg': '#9b59b6',
                    '--logo-bg': '#9b59b6',
                    '--border-color': '#9b59b6',
                    '--full-width-bar-bg': '#9b59b6',
                    '--nav-item-active-bg': '#f3e5f5',
                    '--svg-fill': '#9b59b6',
                    '--inactive-background-color': '#f3e5f5',
                    '--shadow-and-border-color': '#9b59b6',
                    '--calendar-event-color-hover': '#9b59b6'
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
            this.currentTheme = saved || 'default';
        } catch (error) {
            console.error('Error loading theme preset:', error);
            this.currentTheme = 'default';
        }
    }

    applyTheme(themeName = this.currentTheme) {
        const theme = this.themes[themeName];
        if (!theme) {
            console.warn(`Theme '${themeName}' not found, using default`);
            themeName = 'default';
        }

        this.currentTheme = themeName;
        
        // Apply CSS variables
        const root = document.documentElement;
        const colors = this.themes[themeName].colors;
        
        for (const [property, value] of Object.entries(colors)) {
            root.style.setProperty(property, value);
        }

        // Save to localStorage
        try {
            localStorage.setItem(this.storageKey, themeName);
        } catch (error) {
            console.error('Error saving theme preset:', error);
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
        document.querySelectorAll('.theme-card').forEach(card => {
            const themeName = card.dataset.theme;
            if (themeName === this.currentTheme) {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
        });

        // Update button badge/indicator
        const themeBtn = document.getElementById('openThemePresets');
        if (themeBtn && this.currentTheme !== 'default') {
            themeBtn.classList.add('customized');
        } else if (themeBtn) {
            themeBtn.classList.remove('customized');
        }
    }

    attachEventListeners() {
        // Modal toggle
        const openBtn = document.getElementById('openThemePresets');
        const openBtnMobile = document.getElementById('openThemePresetsMobile');
        const closeBtn = document.getElementById('closeThemePresets');
        const modal = document.getElementById('themePresetsModal');

        if (openBtn && modal) {
            openBtn.addEventListener('click', (e) => {
                e.preventDefault();
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                this.updateThemeSelector();
            });
        }

        if (openBtnMobile && modal) {
            openBtnMobile.addEventListener('click', (e) => {
                e.preventDefault();
                // Close mobile more menu
                const mobileMoreMenu = document.getElementById('mobileMoreMenu');
                if (mobileMoreMenu) {
                    mobileMoreMenu.classList.remove('active');
                }
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                this.updateThemeSelector();
            });
        }

        if (closeBtn && modal) {
            closeBtn.addEventListener('click', () => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            });

            // Close on backdrop click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        }

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Theme card clicks
        document.addEventListener('click', (e) => {
            const themeCard = e.target.closest('.theme-card');
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
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.themePresets = new ThemePresets();
    });
} else {
    window.themePresets = new ThemePresets();
}

