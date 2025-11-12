# FXBG-Closet Feature Analysis
## Assignment #2, Section 4.1 - Sequence Diagrams Documentation

This document provides a comprehensive overview of two key features in the FXBG-Closet volunteer management system:
1. Theme Change Feature (Light/Dark Mode)
2. PWA (Progressive Web App) Installation Feature

---

## 1. Theme Change Feature

### Overview
The theme change feature allows users to toggle between light and dark modes, providing better accessibility and user preference customization. The theme preference persists across sessions using browser localStorage.

### Files Implementing Theme Switching

#### Core Files:
1. **`/js/theme-toggle.js`** (121 lines)
   - Main JavaScript implementation
   - Handles theme initialization, toggling, and persistence

2. **`/css/theme-toggle.css`** (1,813 lines)
   - CSS variables for both light and dark themes
   - Theme-specific styling rules
   - Button styling and animations

3. **`/header.php`** (Lines 24, 283-298, 858)
   - Includes theme CSS and JS
   - Renders theme toggle button in navigation
   - Contains SVG icons for sun/moon

### Theme Storage Mechanism

**Storage Type:** `localStorage` (browser-based)

**Key Used:** `"theme"`

**Values:** 
- `"light"` - Light mode (default)
- `"dark"` - Dark mode

**Storage Location:**
```javascript
// Set theme preference
localStorage.setItem("theme", theme);

// Retrieve theme preference
const savedTheme = localStorage.getItem("theme");
```

**Persistence:** The theme preference is stored in the browser's localStorage, which:
- Persists across browser sessions
- Is specific to the origin (domain)
- Survives browser restarts
- Is cleared only when user clears browser data or manually

### User Interaction Flow

#### Step-by-Step Flow:

1. **Page Load**
   ```
   User opens any page → DOMContentLoaded event fires → initializeTheme() executes
   ```

2. **Theme Initialization**
   ```javascript
   function initializeTheme() {
     // Check for saved theme preference
     const savedTheme = localStorage.getItem("theme");
     const systemPrefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
     
     let theme = "light"; // default
     
     if (savedTheme) {
       theme = savedTheme; // Use saved preference
     } else if (systemPrefersDark) {
       theme = "dark"; // Use system preference
     }
     
     applyTheme(theme);
   }
   ```

3. **Apply Theme**
   ```javascript
   function applyTheme(theme) {
     if (theme === "dark") {
       document.documentElement.setAttribute("data-theme", "dark");
     } else {
       document.documentElement.removeAttribute("data-theme");
     }
     
     localStorage.setItem("theme", theme);
     updateToggleButton(theme);
   }
   ```

4. **User Clicks Toggle Button**
   ```
   User clicks theme button → handleThemeToggle() executes → getCurrentTheme() → Toggle theme → applyTheme()
   ```

5. **Theme Switch**
   ```javascript
   function handleThemeToggle() {
     const currentTheme = getCurrentTheme();
     const newTheme = currentTheme === "dark" ? "light" : "dark";
     applyTheme(newTheme);
   }
   ```

### UI Components Involved

#### 1. Theme Toggle Button (header.php, lines 283-298)
```html
<button class="theme-toggle nav-action-btn" aria-label="Toggle theme" title="Toggle dark/light mode">
    <!-- Sun icon (visible in dark mode) -->
    <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="5"></circle>
        <line x1="12" y1="1" x2="12" y2="3"></line>
        <!-- ...more sun rays... -->
    </svg>
    
    <!-- Moon icon (visible in light mode) -->
    <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
    </svg>
</button>
```

**Button Features:**
- Located in the navigation bar (appears in 3 places for different nav states)
- SVG icons for sun (dark mode active) and moon (light mode active)
- CSS transitions for smooth icon rotation and fade effects
- Accessible with ARIA labels

#### 2. CSS Variables System (theme-toggle.css)
```css
/* Light Mode Variables (Default) */
:root {
  --bg-color: #f9f6f3;
  --text-color: #363434;
  --navbar-bg: #ffffff;
  --main-color: #e8c4b8;
  --accent-color: #d4af37;
  /* ...100+ more variables... */
}

/* Dark Mode Variables */
[data-theme="dark"] {
  --bg-color: #1a1816;
  --text-color: #e8c4b8;
  --navbar-bg: #2a2826;
  --main-color: #4a3f3a;
  --accent-color: #d4af37;
  /* ...100+ more variables... */
}
```

#### 3. Animated Icon Transitions (theme-toggle.css, lines 137-160)
```css
/* Sun icon - hidden in light mode, visible in dark mode */
.theme-toggle .sun-icon {
  opacity: 0;
  transform: rotate(180deg) scale(0);
  transition: all 0.3s ease;
}

/* Moon icon - visible in light mode, hidden in dark mode */
.theme-toggle .moon-icon {
  opacity: 1;
  transform: rotate(0deg) scale(1);
  transition: all 0.3s ease;
}

/* Dark mode icon states */
[data-theme="dark"] .theme-toggle .sun-icon {
  opacity: 1;
  transform: rotate(0deg) scale(1);
}

[data-theme="dark"] .theme-toggle .moon-icon {
  opacity: 0;
  transform: rotate(-180deg) scale(0);
}
```

### Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         Page Load                                │
└────────────────────────────┬────────────────────────────────────┘
                             ↓
                ┌────────────────────────┐
                │  DOMContentLoaded      │
                │  Event Fires           │
                └────────────┬───────────┘
                             ↓
                ┌────────────────────────┐
                │  initializeTheme()     │
                └────────────┬───────────┘
                             ↓
           ┌─────────────────┴─────────────────┐
           ↓                                   ↓
  ┌────────────────────┐           ┌──────────────────────┐
  │ Check localStorage │           │ Check System Pref    │
  │ for "theme" key    │           │ (prefers-color-      │
  │                    │           │  scheme: dark)       │
  └────────┬───────────┘           └──────────┬───────────┘
           ↓                                   ↓
    ┌──────────────┐                   ┌──────────────┐
    │ Saved theme? │───No───────────→ │ Dark system? │
    └──────┬───────┘                   └──────┬───────┘
           │ Yes                               ↓
           ↓                              Yes / No
    ┌──────────────┐                   ┌──────────────┐
    │ Use saved    │                   │ Use system   │
    │ theme        │                   │ or default   │
    └──────┬───────┘                   └──────┬───────┘
           └───────────────┬───────────────────┘
                           ↓
                  ┌────────────────────┐
                  │  applyTheme(theme) │
                  └────────┬───────────┘
                           ↓
          ┌────────────────┴────────────────┐
          ↓                                  ↓
  ┌───────────────────┐          ┌─────────────────────┐
  │ Set data-theme    │          │ Update localStorage │
  │ attribute on HTML │          │ with theme value    │
  └───────┬───────────┘          └─────────┬───────────┘
          └────────────┬─────────────────────┘
                       ↓
            ┌──────────────────────┐
            │ updateToggleButton() │
            │ Set ARIA labels      │
            └──────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    User Clicks Toggle Button                     │
└────────────────────────────┬────────────────────────────────────┘
                             ↓
                ┌────────────────────────┐
                │  handleThemeToggle()   │
                └────────────┬───────────┘
                             ↓
                ┌────────────────────────┐
                │  getCurrentTheme()     │
                │  (check data-theme)    │
                └────────────┬───────────┘
                             ↓
                ┌────────────────────────┐
                │  Toggle: light ↔ dark  │
                └────────────┬───────────┘
                             ↓
                ┌────────────────────────┐
                │  applyTheme(newTheme)  │
                └────────────┬───────────┘
                             ↓
                  [Same flow as above]
```

### Dependencies and Libraries

**No external libraries required!** The theme feature is built using vanilla JavaScript and CSS.

**Browser APIs Used:**
- `localStorage` API - for persistent storage
- `window.matchMedia()` - for system theme preference detection
- `document.documentElement` - for applying theme attribute
- `DOMContentLoaded` event - for initialization timing

**CSS Features Used:**
- CSS Custom Properties (Variables)
- Attribute Selectors (`[data-theme="dark"]`)
- CSS Transitions
- SVG styling

### Fallback Behavior

1. **No localStorage support:** Theme defaults to light mode
2. **No saved preference:** Uses system preference if available
3. **No system preference:** Defaults to light mode
4. **System theme changes:** Auto-updates only if user hasn't manually set preference

---

## 2. PWA (Progressive Web App) Installation Feature

### Overview
The PWA feature enables the FXBG-Closet application to be installed on users' devices like a native app. It provides offline functionality through service workers and caching strategies.

### Service Worker Implementation

#### File: `/service-worker.js` (117 lines)

**Purpose:** Handles caching, offline functionality, and app lifecycle

**Key Components:**

1. **Cache Configuration**
   ```javascript
   const CACHE_NAME = "fxbg-closet-v1";
   const STATIC_ASSETS = [
     "/",
     "/index.php",
     "/login.php",
     "/css/base.css",
     "/css/theme-toggle.css",
     "/css/normal_base.css",
     "/css/management_base.css",
     "/js/theme-toggle.js",
     "/images/FXBG-PrideWhiteLogo.png",
     "https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap"
   ];
   ```

2. **Install Event Handler** (Lines 17-40)
   ```javascript
   self.addEventListener("install", (event) => {
     event.waitUntil(
       caches.open(CACHE_NAME)
         .then((cache) => {
           console.log("Service Worker: Caching static assets");
           return Promise.allSettled(
             STATIC_ASSETS.map((url) =>
               fetch(url)
                 .then((response) => {
                   if (response.status === 200) {
                     return cache.add(url);
                   }
                 })
                 .catch(() => {})
             )
           );
         })
     );
     self.skipWaiting(); // Activate immediately
   });
   ```

3. **Activate Event Handler** (Lines 42-56)
   ```javascript
   self.addEventListener("activate", (event) => {
     event.waitUntil(
       caches.keys().then((cacheNames) => {
         return Promise.all(
           cacheNames.map((cacheName) => {
             if (cacheName !== CACHE_NAME) {
               console.log("Service Worker: Deleting old cache:", cacheName);
               return caches.delete(cacheName);
             }
           })
         );
       })
     );
     self.clients.claim(); // Take control of all pages immediately
   });
   ```

4. **Fetch Event Handler** (Lines 58-116)
   ```javascript
   self.addEventListener("fetch", (event) => {
     const { request } = event;
     
     // Only handle same-origin requests
     if (!request.url.startsWith(self.location.origin)) {
       return;
     }
     
     // Different strategies for PHP vs static files
     if (request.url.includes(".php")) {
       // Network-first strategy for PHP files
       event.respondWith(
         fetch(request)
           .then((response) => {
             // Cache successful responses
             if (response && response.status === 200 && response.type === "basic") {
               const responseClone = response.clone();
               caches.open(CACHE_NAME).then((cache) => {
                 cache.put(request, responseClone);
               });
             }
             return response;
           })
           .catch(() => {
             // Fallback to cache if network fails
             return caches.match(request).then((cachedResponse) => {
               return cachedResponse || 
                      new Response("Offline - Page not cached", { status: 503 });
             });
           })
       );
     } else {
       // Cache-first strategy for static assets
       event.respondWith(
         caches.match(request).then((cachedResponse) => {
           if (cachedResponse) {
             return cachedResponse;
           }
           return fetch(request)
             .then((response) => {
               if (response && response.status === 200) {
                 const responseClone = response.clone();
                 caches.open(CACHE_NAME).then((cache) => {
                   cache.put(request, responseClone);
                 });
               }
               return response;
             })
             .catch(() => {
               return new Response("Offline - Asset not available", { status: 503 });
             });
         })
       );
     }
   });
   ```

### Manifest.json Configuration

#### File: `/manifest.json` (75 lines)

**Purpose:** Defines PWA metadata and installation behavior

**Key Configuration:**

```json
{
  "name": "FXBG Pride Volunteer Management",
  "short_name": "FXBG Closet",
  "description": "Volunteer Management System for Fredericksburg Pride",
  "start_url": "/",
  "scope": "/",
  "display": "standalone",
  "orientation": "portrait-primary",
  "background_color": "#ffffff",
  "theme_color": "#6B46C1",
  "icons": [
    {
      "src": "/images/FXBG-PrideWhiteLogo.png",
      "sizes": "192x192",
      "type": "image/png",
      "purpose": "any"
    },
    {
      "src": "/images/FXBG-PrideWhiteLogo.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "any"
    },
    {
      "src": "/images/FXBG-PrideWhiteLogo.png",
      "sizes": "192x192",
      "type": "image/png",
      "purpose": "maskable"
    }
  ],
  "screenshots": [...],
  "categories": ["productivity", "business"],
  "shortcuts": [
    {
      "name": "View Events",
      "short_name": "Events",
      "url": "/viewAllEvents.php",
      "icons": [...]
    },
    {
      "name": "My Profile",
      "short_name": "Profile",
      "url": "/viewProfile.php",
      "icons": [...]
    }
  ],
  "prefer_related_applications": false
}
```

**Key Properties Explained:**

- **`display: "standalone"`**: App opens without browser UI
- **`start_url: "/"`**: Starting page when app launches
- **`scope: "/"`**: URLs that belong to this PWA
- **`theme_color: "#6B46C1"`**: Colors the browser UI (purple theme)
- **`icons`**: App icons for different sizes and purposes
- **`shortcuts`**: Quick actions from app icon (right-click menu)
- **`orientation: "portrait-primary"`**: Preferred screen orientation

### Service Worker Registration Process

#### Location: `/header.php` (Lines 1118-1127)

```javascript
// PWA Service Worker Registration
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
        .then((registration) => {
            console.log('Service Worker registered:', registration);
        })
        .catch((error) => {
            console.log('Service Worker registration failed:', error);
        });
}
```

**Registration Flow:**

1. Check if Service Workers are supported
2. Attempt to register `/service-worker.js`
3. Log success or failure
4. Browser handles the rest automatically

### PWA Meta Tags (header.php, Lines 27-34)

```html
<!-- PWA Meta Tags - Enables offline support, installation, etc. -->
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#6B46C1">
<meta name="description" content="Volunteer Management System for Fredericksburg Pride - FXBG Closet">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="FXBG Closet">
<link rel="apple-touch-icon" href="/images/FXBG-PrideWhiteLogo.png">
```

**Purpose of Each Tag:**

- **`<link rel="manifest">`**: Links to PWA manifest file
- **`theme-color`**: Colors browser UI on Android
- **`apple-mobile-web-app-capable`**: Enables full-screen on iOS
- **`apple-mobile-web-app-status-bar-style`**: iOS status bar appearance
- **`apple-mobile-web-app-title`**: iOS home screen app name
- **`apple-touch-icon`**: iOS home screen icon

### Install Prompt Handling

**Current Implementation:** Browser-default installation prompt

**How Installation Works:**

1. **Browser Checks Installability**
   - Has valid `manifest.json`
   - Has registered service worker
   - Is served over HTTPS (or localhost)
   - Meets engagement requirements (varies by browser)

2. **Browser Shows Install Prompt**
   - **Chrome/Edge:** Address bar install button + banner
   - **Safari:** Share menu → "Add to Home Screen"
   - **Firefox:** Menu → "Install"

3. **User Installs**
   - User clicks browser's install prompt
   - App icon added to device
   - App opens in standalone mode

**Note:** There is currently **no custom install prompt UI** implemented. The application relies on the browser's native installation flow, which is simpler and follows platform conventions.

### Caching Strategies

The service worker implements two different caching strategies:

#### 1. Network-First (for PHP files)
```
Request → Try Network → Success? → Cache & Return
                      → Failure? → Try Cache → Return cached or 503
```

**Why:** Dynamic content should be fresh when online, but available offline

#### 2. Cache-First (for static assets)
```
Request → Try Cache → Found? → Return cached
                    → Not Found? → Fetch Network → Cache & Return
```

**Why:** CSS, JS, images rarely change and load faster from cache

### Data Flow Diagram - PWA Installation

```
┌─────────────────────────────────────────────────────────────────┐
│                   User Visits Site (First Time)                  │
└────────────────────────────┬────────────────────────────────────┘
                             ↓
                ┌────────────────────────┐
                │  Browser loads HTML    │
                │  (header.php)          │
                └────────────┬───────────┘
                             ↓
          ┌──────────────────┴──────────────────┐
          ↓                                     ↓
  ┌────────────────┐                  ┌─────────────────────┐
  │ Parse PWA Meta │                  │ Load manifest.json  │
  │ Tags           │                  │                     │
  └────────┬───────┘                  └─────────┬───────────┘
           └──────────────┬────────────────────┘
                          ↓
              ┌───────────────────────┐
              │ Check Service Worker  │
              │ support in navigator  │
              └───────────┬───────────┘
                          ↓
                  ┌───────────────┐
                  │ Supported?    │
                  └───┬───────┬───┘
                  Yes │       │ No
                      ↓       └──→ [End - No PWA features]
        ┌──────────────────────────┐
        │ Register Service Worker  │
        │ navigator.serviceWorker  │
        │   .register('/sw.js')    │
        └─────────────┬────────────┘
                      ↓
        ┌─────────────────────────────┐
        │ Browser downloads           │
        │ service-worker.js           │
        └─────────────┬───────────────┘
                      ↓
        ┌─────────────────────────────┐
        │ Service Worker INSTALL      │
        │ event fires                 │
        └─────────────┬───────────────┘
                      ↓
        ┌─────────────────────────────┐
        │ Open cache "fxbg-closet-v1" │
        └─────────────┬───────────────┘
                      ↓
        ┌─────────────────────────────┐
        │ Cache static assets:        │
        │ - HTML pages                │
        │ - CSS files                 │
        │ - JS files                  │
        │ - Images                    │
        │ - Fonts                     │
        └─────────────┬───────────────┘
                      ↓
        ┌─────────────────────────────┐
        │ Service Worker ACTIVATE     │
        │ event fires                 │
        └─────────────┬───────────────┘
                      ↓
        ┌─────────────────────────────┐
        │ Delete old caches           │
        │ (version management)        │
        └─────────────┬───────────────┘
                      ↓
        ┌─────────────────────────────┐
        │ Service Worker ready        │
        │ Intercepts fetch requests   │
        └─────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│               Browser Determines Installability                  │
└────────────────────────────┬────────────────────────────────────┘
                             ↓
              ┌──────────────────────────┐
              │ Check Install Criteria:  │
              │ ✓ Valid manifest.json    │
              │ ✓ Service worker active  │
              │ ✓ HTTPS connection       │
              │ ✓ User engagement met    │
              └──────────┬───────────────┘
                         ↓
                  ┌──────────────┐
                  │ Installable? │
                  └──┬────────┬──┘
                 Yes │        │ No
                     ↓        └──→ [No prompt shown]
        ┌────────────────────────┐
        │ Browser shows install  │
        │ prompt (native UI):    │
        │ - Chrome: address bar  │
        │ - Safari: share menu   │
        │ - Firefox: menu        │
        └────────┬───────────────┘
                 ↓
        ┌────────────────────────┐
        │ User clicks install    │
        └────────┬───────────────┘
                 ↓
        ┌────────────────────────┐
        │ beforeinstallprompt    │
        │ event (automatic)      │
        └────────┬───────────────┘
                 ↓
        ┌────────────────────────┐
        │ Browser adds app icon  │
        │ to device home screen  │
        └────────┬───────────────┘
                 ↓
        ┌────────────────────────┐
        │ App installed!         │
        │ Can launch standalone  │
        └────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                User Requests Page (After Install)                │
└────────────────────────────┬────────────────────────────────────┘
                             ↓
                ┌────────────────────────┐
                │ Service Worker         │
                │ intercepts fetch       │
                └────────────┬───────────┘
                             ↓
                    ┌────────────────┐
                    │ Is same-origin?│
                    └────┬──────┬────┘
                     Yes │      │ No
                         ↓      └──→ [Pass through to network]
              ┌──────────────────┐
              │ Is .php file?    │
              └────┬────────┬────┘
               Yes │        │ No
                   ↓        ↓
    ┌──────────────────┐   ┌──────────────────┐
    │ Network-First    │   │ Cache-First      │
    │ Strategy         │   │ Strategy         │
    └────┬─────────────┘   └─────┬────────────┘
         ↓                        ↓
    ┌──────────┐           ┌──────────────┐
    │ Try      │           │ Check cache  │
    │ network  │           │ first        │
    └────┬─────┘           └──┬───────────┘
         ↓                    ↓
    Success?            Found in cache?
         ↓                    ↓
    ┌──────────┐        ┌──────────┐
    │ Cache &  │        │ Return   │
    │ return   │        │ cached   │
    └──────────┘        └──────────┘
         ↓
    Failed?
         ↓
    ┌──────────┐
    │ Return   │
    │ from     │
    │ cache or │
    │ 503      │
    └──────────┘
```

### Dependencies and Libraries

**No external libraries required!** The PWA feature uses only browser APIs.

**Browser APIs Used:**
- **Service Worker API** - Background script for caching and offline
- **Cache API** - Storage mechanism for assets
- **Fetch API** - Network requests
- **Promise API** - Asynchronous operations
- **Manifest specification** - W3C standard for PWA metadata

**Browser Support:**
- Chrome/Edge: Full support ✓
- Firefox: Full support ✓
- Safari: Partial support (no install prompt, manual add to home)
- Mobile browsers: Good support across platforms

### Offline Functionality

When the user is offline:

1. **Cached Pages:** Previously visited PHP pages load from cache
2. **Static Assets:** CSS, JS, images load instantly from cache
3. **Uncached Content:** Shows "Offline" message with 503 status
4. **Graceful Degradation:** App remains functional for cached content

### Installation Triggers

**What triggers the install prompt:**
1. ✓ Valid `manifest.json` file
2. ✓ Active service worker
3. ✓ HTTPS or localhost
4. ✓ User has visited at least twice (engagement heuristic)
5. ✓ At least 30 seconds between visits (Chrome requirement)

**What the user sees:**
- **Desktop Chrome/Edge:** Install button in address bar + mini-infobar
- **Mobile Chrome:** Bottom sheet prompt "Add FXBG Closet to Home screen"
- **iOS Safari:** Manual - Share button → "Add to Home Screen"
- **Desktop Safari:** Manual - File menu → "Add to Dock"

---

## Summary for Assignment #2

### Theme Change Feature Summary

**Purpose:** Provides dark/light mode toggle for better accessibility and user preference

**Key Technical Points:**
- Uses vanilla JavaScript and CSS (no dependencies)
- Persists preference in browser localStorage
- Respects system preferences as fallback
- Smooth CSS transitions between themes
- 100+ CSS custom properties for comprehensive theming

**User Experience:**
1. User clicks moon/sun icon in navigation
2. Theme instantly switches with smooth animations
3. Preference saved for future visits
4. Works across all pages of the application

### PWA Installation Feature Summary

**Purpose:** Enables app installation on devices for native-like experience and offline access

**Key Technical Points:**
- Service worker caches critical assets for offline use
- Manifest defines app metadata and appearance
- Two caching strategies: network-first for dynamic, cache-first for static
- Browser handles installation prompt automatically
- No custom install UI (uses native browser prompts)

**User Experience:**
1. User visits site on supported browser
2. After engagement criteria met, browser shows install option
3. User clicks install from browser UI
4. App added to home screen/dock
5. Opens in standalone window (no browser chrome)
6. Works offline with cached content

---

## File Reference Quick List

### Theme Change Feature Files:
- `/js/theme-toggle.js` - Main JavaScript logic
- `/css/theme-toggle.css` - Theme styles and variables
- `/header.php` (lines 24, 283-298, 858) - Integration points

### PWA Feature Files:
- `/service-worker.js` - Caching and offline logic
- `/manifest.json` - PWA configuration
- `/header.php` (lines 27-34, 1118-1127) - PWA meta tags and registration
- `/images/FXBG-PrideWhiteLogo.png` - App icon

---

## Additional Notes for Sequence Diagrams

When creating sequence diagrams for Assignment #2, consider these key interaction sequences:

### Theme Change Sequence Actors:
1. User
2. Browser
3. Theme Toggle Button
4. JavaScript (theme-toggle.js)
5. localStorage
6. DOM (document.documentElement)
7. CSS Engine

### PWA Installation Sequence Actors:
1. User
2. Browser
3. Web Server
4. Service Worker
5. Cache Storage
6. Network
7. Device OS (for installation)

### Critical Timing Points:
- **Theme:** DOMContentLoaded → Initialize → User Click → Toggle → Apply
- **PWA:** Page Load → Register SW → Install → Activate → Fetch Intercept → Install Prompt → User Install

---

*This documentation was created for Assignment #2, Section 4.1 - Sequence Diagrams. It provides all necessary information to create comprehensive sequence diagrams and explanatory paragraphs for the design paper.*
