# FXBG-Closet Feature Summary
## Quick Reference for Sequence Diagrams

This is a concise summary of the detailed analysis found in `FEATURE_ANALYSIS.md`.

---

## Theme Change Feature - Quick Facts

### What It Does
Allows users to toggle between light and dark color themes with persistent preference storage.

### How It Works (5 Steps)
1. **Page loads** → JavaScript checks localStorage for saved theme
2. **No saved theme?** → Check system preference (prefers-color-scheme)
3. **Apply theme** → Set `data-theme="dark"` attribute on `<html>` element
4. **User clicks toggle** → Switch between light/dark
5. **Save preference** → Store in `localStorage.setItem("theme", "light|dark")`

### Key Files
- `js/theme-toggle.js` - Logic (121 lines)
- `css/theme-toggle.css` - Styles (1,813 lines)
- `header.php` - Integration (lines 24, 283-298, 858)

### Technical Details
- **Storage:** Browser localStorage (key: "theme", values: "light"/"dark")
- **Fallback:** System preference → Default light mode
- **Persistence:** Survives page reloads and browser restarts
- **UI:** Toggle button with animated sun/moon SVG icons
- **Styling:** 100+ CSS custom properties (CSS variables)

---

## PWA Installation Feature - Quick Facts

### What It Does
Enables the web app to be installed on devices like a native app, with offline functionality.

### How It Works (5 Steps)
1. **Page loads** → Register service worker (`/service-worker.js`)
2. **Service worker installs** → Cache critical assets (HTML, CSS, JS, images)
3. **Service worker activates** → Delete old caches, take control of pages
4. **Browser checks installability** → Valid manifest + active SW + HTTPS
5. **User installs** → Browser shows install prompt → App added to device

### Key Files
- `service-worker.js` - Caching logic (117 lines)
- `manifest.json` - App metadata (75 lines)
- `header.php` - PWA meta tags and registration (lines 27-34, 1118-1127)

### Technical Details
- **Caching Strategy:**
  - PHP files: Network-first (fresh when online, cached when offline)
  - Static assets: Cache-first (instant load from cache)
- **Cache Name:** "fxbg-closet-v1"
- **Cached Assets:** 10 critical files (HTML, CSS, JS, images, fonts)
- **Installation:** Browser-native prompt (no custom UI)
- **Offline:** Previously visited pages and cached assets work offline

---

## For Sequence Diagrams

### Theme Change - Key Actors
1. User
2. Browser
3. DOM (HTML element)
4. localStorage
5. JavaScript (theme-toggle.js)
6. CSS Engine

### Theme Change - Critical Events
- `DOMContentLoaded` → Initialize theme
- User click → Toggle theme
- `localStorage.setItem()` → Save preference
- `setAttribute("data-theme")` → Apply theme
- CSS transition → Visual change

### PWA Installation - Key Actors
1. User
2. Browser
3. Web Server
4. Service Worker
5. Cache Storage
6. Device OS

### PWA Installation - Critical Events
- `navigator.serviceWorker.register()` → Start registration
- `install` event → Cache assets
- `activate` event → Take control
- `fetch` event → Intercept network requests
- `beforeinstallprompt` event → Show install UI (automatic)
- User accepts → App installed

---

## Code Snippets for Reference

### Theme Toggle (JavaScript)
```javascript
// From js/theme-toggle.js
function handleThemeToggle() {
  const currentTheme = getCurrentTheme();
  const newTheme = currentTheme === "dark" ? "light" : "dark";
  applyTheme(newTheme);
}

function applyTheme(theme) {
  if (theme === "dark") {
    document.documentElement.setAttribute("data-theme", "dark");
  } else {
    document.documentElement.removeAttribute("data-theme");
  }
  localStorage.setItem("theme", theme);
}
```

### Service Worker Registration (JavaScript)
```javascript
// From header.php
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/service-worker.js')
    .then(registration => console.log('SW registered:', registration))
    .catch(error => console.log('SW registration failed:', error));
}
```

### Service Worker Caching (JavaScript)
```javascript
// From service-worker.js
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    })
  );
  self.skipWaiting();
});
```

---

## Data Flow Summary

### Theme Change Flow
```
User Click → getCurrentTheme() → Toggle → applyTheme() → 
  → setAttribute("data-theme") → CSS applies styles
  → localStorage.setItem() → Preference saved
```

### PWA Installation Flow
```
Page Load → Register SW → SW Downloads → Install Event →
  → Cache Assets → Activate Event → Take Control →
  → Browser Checks Criteria → Show Install Prompt →
  → User Accepts → App Installed
```

### Service Worker Fetch Flow
```
Request → SW Intercepts → PHP file or Static?
  → PHP: Try Network → Cache result → Return
  → Static: Try Cache → Cache hit? Return : Fetch & Cache
```

---

## Browser Compatibility

### Theme Toggle
- ✅ All modern browsers (Chrome, Firefox, Safari, Edge)
- ✅ Mobile browsers
- Uses standard APIs (localStorage, CSS variables)

### PWA Installation
- ✅ Chrome/Edge (Desktop & Mobile) - Full support with install prompts
- ✅ Firefox (Desktop & Mobile) - Full support
- ⚠️ Safari (Desktop) - Partial support (manual "Add to Dock")
- ⚠️ Safari (iOS) - Partial support (manual "Add to Home Screen")

---

## No External Dependencies

Both features use only vanilla JavaScript and standard Web APIs:
- No jQuery, React, or other frameworks
- No npm packages for runtime
- Standard browser APIs only
- Lightweight and performant

---

**For full details with code explanations and diagrams, see `FEATURE_ANALYSIS.md`**
