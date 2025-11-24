/**
 * Accessibility Settings Manager
 * Allows users to customize font properties for better readability
 * Settings are stored in localStorage and applied via CSS variables
 */

class AccessibilitySettings {
    constructor() {
        this.storageKey = 'fxbg-accessibility-settings';
        
        // Default settings
        this.defaults = {
            fontFamily: 'quicksand',
            fontSize: 'medium',
            fontWeight: 'normal',
            lineHeight: 'normal',
            letterSpacing: 'normal',
            textAlign: 'default'
        };

        // Font options
        this.fontFamilies = {
            'quicksand': { name: 'Quicksand (Default)', value: '"Quicksand", sans-serif' },
            'arial': { name: 'Arial', value: 'Arial, sans-serif' },
            'verdana': { name: 'Verdana', value: 'Verdana, sans-serif' },
            'georgia': { name: 'Georgia', value: 'Georgia, serif' },
            'times': { name: 'Times New Roman', value: '"Times New Roman", serif' },
            'courier': { name: 'Courier', value: '"Courier New", monospace' },
            'comic': { name: 'Comic Sans', value: '"Comic Sans MS", cursive' },
            'opendyslexic': { name: 'OpenDyslexic', value: 'OpenDyslexic, sans-serif' }
        };

        this.fontSizes = {
            'small': { name: 'Small', scale: 0.875 },
            'medium': { name: 'Medium (Default)', scale: 1 },
            'large': { name: 'Large', scale: 1.125 },
            'xlarge': { name: 'Extra Large', scale: 1.25 },
            'xxlarge': { name: 'XXL', scale: 1.5 }
        };

        this.fontWeights = {
            'light': { name: 'Light', value: '300' },
            'normal': { name: 'Normal (Default)', value: '400' },
            'medium': { name: 'Medium', value: '500' },
            'semibold': { name: 'Semi-Bold', value: '600' },
            'bold': { name: 'Bold', value: '700' }
        };

        this.lineHeights = {
            'tight': { name: 'Tight', value: '1.25' },
            'normal': { name: 'Normal (Default)', value: '1.5' },
            'relaxed': { name: 'Relaxed', value: '1.75' },
            'loose': { name: 'Loose', value: '2' }
        };

        this.letterSpacings = {
            'tight': { name: 'Tight', value: '-0.025em' },
            'normal': { name: 'Normal (Default)', value: '0' },
            'wide': { name: 'Wide', value: '0.025em' },
            'wider': { name: 'Wider', value: '0.05em' }
        };

        this.textAligns = {
            'default': { name: 'Default', value: 'inherit' },
            'left': { name: 'Left', value: 'left' },
            'justify': { name: 'Justify', value: 'justify' }
        };

        this.init();
    }

    init() {
        this.loadSettings();
        this.applySettings();
        this.initModal();
        this.attachEventListeners();
    }

    loadSettings() {
        try {
            const saved = localStorage.getItem(this.storageKey);
            this.settings = saved ? JSON.parse(saved) : { ...this.defaults };
        } catch (error) {
            console.error('Error loading accessibility settings:', error);
            this.settings = { ...this.defaults };
        }
    }

    saveSettings() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.settings));
        } catch (error) {
            console.error('Error saving accessibility settings:', error);
        }
    }

    applySettings() {
        const root = document.documentElement;

        // Apply font family
        const fontFamily = this.fontFamilies[this.settings.fontFamily]?.value || this.fontFamilies.quicksand.value;
        root.style.setProperty('--accessibility-font-family', fontFamily);

        // Apply font size scale
        const fontScale = this.fontSizes[this.settings.fontSize]?.scale || 1;
        root.style.setProperty('--accessibility-font-scale', fontScale);

        // Apply font weight
        const fontWeight = this.fontWeights[this.settings.fontWeight]?.value || '400';
        root.style.setProperty('--accessibility-font-weight', fontWeight);

        // Apply line height
        const lineHeight = this.lineHeights[this.settings.lineHeight]?.value || '1.5';
        root.style.setProperty('--accessibility-line-height', lineHeight);

        // Apply letter spacing
        const letterSpacing = this.letterSpacings[this.settings.letterSpacing]?.value || '0';
        root.style.setProperty('--accessibility-letter-spacing', letterSpacing);

        // Apply text align
        const textAlign = this.textAligns[this.settings.textAlign]?.value || 'inherit';
        root.style.setProperty('--accessibility-text-align', textAlign);

        // Update body class for special fonts
        if (this.settings.fontFamily === 'opendyslexic') {
            document.body.classList.add('dyslexic-font');
        } else {
            document.body.classList.remove('dyslexic-font');
        }
    }

    updateSetting(key, value) {
        this.settings[key] = value;
        this.saveSettings();
        this.applySettings();
        this.updateModalValues();
    }

    resetSettings() {
        if (confirm('Reset all accessibility settings to defaults?')) {
            this.settings = { ...this.defaults };
            this.saveSettings();
            this.applySettings();
            this.updateModalValues();
        }
    }

    initModal() {
        // Modal will be created in HTML, this just populates it
        this.updateModalValues();
    }

    updateModalValues() {
        // Update all controls to match current settings
        for (const [key, value] of Object.entries(this.settings)) {
            const control = document.getElementById(`a11y-${key}`);
            if (control) {
                control.value = value;
            }
        }

        // Update preview
        this.updatePreview();
    }

    updatePreview() {
        const preview = document.getElementById('a11y-preview');
        if (preview) {
            const fontFamily = this.fontFamilies[this.settings.fontFamily]?.value;
            const fontScale = this.fontSizes[this.settings.fontSize]?.scale;
            const fontWeight = this.fontWeights[this.settings.fontWeight]?.value;
            const lineHeight = this.lineHeights[this.settings.lineHeight]?.value;
            const letterSpacing = this.letterSpacings[this.settings.letterSpacing]?.value;
            const textAlign = this.textAligns[this.settings.textAlign]?.value;

            preview.style.fontFamily = fontFamily;
            preview.style.fontSize = `${fontScale}rem`;
            preview.style.fontWeight = fontWeight;
            preview.style.lineHeight = lineHeight;
            preview.style.letterSpacing = letterSpacing;
            preview.style.textAlign = textAlign;
        }
    }

    attachEventListeners() {
        // Modal controls
        document.addEventListener('change', (e) => {
            if (e.target.id?.startsWith('a11y-')) {
                const key = e.target.id.replace('a11y-', '');
                this.updateSetting(key, e.target.value);
            }
        });

        // Reset button
        const resetBtn = document.getElementById('a11y-reset');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => this.resetSettings());
        }

        // Modal toggle
        const openBtn = document.getElementById('openA11ySettings');
        const openBtnMobile = document.getElementById('openA11ySettingsMobile');
        const closeBtn = document.getElementById('closeA11ySettings');
        const modal = document.getElementById('a11yModal');

        if (openBtn && modal) {
            openBtn.addEventListener('click', (e) => {
                e.preventDefault();
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        }

        // Mobile button handler
        if (openBtnMobile && modal) {
            openBtnMobile.addEventListener('click', (e) => {
                e.preventDefault();
                // Close the mobile more menu first
                const mobileMoreMenu = document.getElementById('mobileMoreMenu');
                if (mobileMoreMenu) {
                    mobileMoreMenu.classList.remove('active');
                }
                // Open accessibility modal
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
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
            if (e.key === 'Escape' && modal?.classList.contains('active')) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // Public method to get current settings
    getCurrentSettings() {
        return { ...this.settings };
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.accessibilitySettings = new AccessibilitySettings();
    });
} else {
    window.accessibilitySettings = new AccessibilitySettings();
}

