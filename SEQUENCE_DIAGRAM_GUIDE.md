# Sequence Diagram Guide
## For Assignment #2, Section 4.1

This guide provides ready-to-use sequence information for creating diagrams in your design paper.

---

## Feature 1: Theme Change (Light/Dark Mode Toggle)

### Actors
1. **User** - Person using the application
2. **Browser** - Web browser
3. **Toggle Button** - UI element (sun/moon icon)
4. **theme-toggle.js** - JavaScript controller
5. **localStorage** - Browser storage
6. **HTML Document** - DOM root element
7. **CSS Engine** - Browser rendering engine

### Sequence: Page Load & Theme Initialization

```
User → Browser: Opens page
Browser → theme-toggle.js: DOMContentLoaded event
theme-toggle.js → localStorage: getItem("theme")
localStorage → theme-toggle.js: Returns saved theme or null

alt: Theme preference exists
    theme-toggle.js → HTML Document: Apply saved theme
else: No saved preference
    theme-toggle.js → Browser: Check matchMedia("prefers-color-scheme: dark")
    Browser → theme-toggle.js: Returns system preference
    theme-toggle.js → HTML Document: Apply system preference or default
end

theme-toggle.js → HTML Document: setAttribute("data-theme", "dark") or removeAttribute
HTML Document → CSS Engine: Trigger CSS variable update
CSS Engine → Browser: Re-render page with new theme
theme-toggle.js → Toggle Button: Update icon (sun/moon)
Browser → User: Display themed page
```

### Sequence: User Toggles Theme

```
User → Toggle Button: Click theme button
Toggle Button → theme-toggle.js: handleThemeToggle() event
theme-toggle.js → HTML Document: getAttribute("data-theme")
HTML Document → theme-toggle.js: Returns current theme
theme-toggle.js → theme-toggle.js: Toggle theme (light ↔ dark)
theme-toggle.js → HTML Document: setAttribute("data-theme", newTheme)
HTML Document → CSS Engine: Trigger CSS transition
CSS Engine → Browser: Animate theme change (0.3s transition)
theme-toggle.js → localStorage: setItem("theme", newTheme)
localStorage → theme-toggle.js: Confirm saved
theme-toggle.js → Toggle Button: Update icon with rotation animation
Browser → User: Display new theme with smooth transition
```

### Key Messages

| From | To | Message | Data |
|------|----|---------| -----|
| theme-toggle.js | localStorage | getItem() | "theme" |
| localStorage | theme-toggle.js | return | "light", "dark", or null |
| theme-toggle.js | HTML Document | setAttribute() | "data-theme", "dark" |
| theme-toggle.js | localStorage | setItem() | "theme", "light" or "dark" |
| CSS Engine | Browser | render() | Updated styles |

### Timing Notes
- **DOMContentLoaded to theme applied:** ~10-50ms
- **User click to visual change:** ~300ms (CSS transition)
- **localStorage operations:** ~1-5ms (synchronous)

---

## Feature 2: PWA Installation

### Actors
1. **User** - Person using the application
2. **Browser** - Web browser with PWA support
3. **Web Server** - PHP/Apache server
4. **header.php** - Registration code
5. **Service Worker** - Background script (service-worker.js)
6. **Cache Storage** - Browser cache API
7. **Network** - Internet connection
8. **Device OS** - Operating system (for app installation)

### Sequence: Initial Registration & Caching

```
User → Browser: Opens page for first time
Browser → Web Server: GET /index.php
Web Server → Browser: Returns HTML (header.php)
Browser → header.php: Execute registration code
header.php → Browser: Check 'serviceWorker' in navigator
Browser → header.php: Return true (supported)
header.php → Browser: navigator.serviceWorker.register('/service-worker.js')
Browser → Web Server: GET /service-worker.js
Web Server → Browser: Return service worker script
Browser → Service Worker: Create worker instance
Browser → Service Worker: Dispatch 'install' event
Service Worker → Cache Storage: caches.open("fxbg-closet-v1")
Cache Storage → Service Worker: Return cache instance

loop: For each asset in STATIC_ASSETS
    Service Worker → Network: fetch(asset_url)
    Network → Service Worker: Return asset
    Service Worker → Cache Storage: cache.put(asset_url, response)
end

Service Worker → Browser: skipWaiting()
Browser → Service Worker: Dispatch 'activate' event
Service Worker → Cache Storage: caches.keys()
Cache Storage → Service Worker: Return all cache names

loop: For each old cache
    Service Worker → Cache Storage: caches.delete(old_cache)
end

Service Worker → Browser: clients.claim()
Browser → Service Worker: Service worker now controls pages
Browser → header.php: Registration complete
header.php → Browser: console.log('Service Worker registered')
Browser → User: Page ready (SW active in background)
```

### Sequence: Intercepting Requests (After SW Active)

```
User → Browser: Navigate to /viewProfile.php
Browser → Service Worker: 'fetch' event (request)
Service Worker → Service Worker: Check if same-origin
Service Worker → Service Worker: Is .php file?

alt: PHP file (network-first strategy)
    Service Worker → Network: fetch(request)
    alt: Network available
        Network → Service Worker: Return response
        Service Worker → Cache Storage: cache.put(request, response.clone())
        Service Worker → Browser: Return response
    else: Network unavailable
        Service Worker → Cache Storage: caches.match(request)
        alt: Found in cache
            Cache Storage → Service Worker: Return cached response
            Service Worker → Browser: Return cached response
        else: Not cached
            Service Worker → Browser: Return 503 error
        end
    end
else: Static file (cache-first strategy)
    Service Worker → Cache Storage: caches.match(request)
    alt: Found in cache
        Cache Storage → Service Worker: Return cached response
        Service Worker → Browser: Return cached response (fast!)
    else: Not in cache
        Service Worker → Network: fetch(request)
        Network → Service Worker: Return response
        Service Worker → Cache Storage: cache.put(request, response.clone())
        Service Worker → Browser: Return response
    end
end

Browser → User: Display page
```

### Sequence: PWA Installation (User-Initiated)

```
Browser → Browser: Check installability criteria
note: Criteria: valid manifest.json, active service worker, 
      HTTPS, user engagement (2+ visits, 30s+ between visits)

Browser → Browser: All criteria met
Browser → User: Show install prompt (address bar icon / banner)
User → Browser: Click "Install" button
Browser → Browser: Fire 'beforeinstallprompt' event (handled internally)
Browser → Device OS: Request app installation
Device OS → Device OS: Create app shortcut/icon
Device OS → Browser: Installation confirmed
Browser → Web Server: GET /manifest.json
Web Server → Browser: Return manifest with app metadata
Browser → Device OS: Configure app with manifest data
Device OS → Device OS: Add icon to home screen/app drawer
Browser → User: Show "App installed" confirmation
User → Device OS: Click app icon
Device OS → Browser: Launch app in standalone mode
Browser → Web Server: Load start_url from manifest
Web Server → Browser: Return starting page
Browser → User: Display app (fullscreen, no browser chrome)
```

### Key Messages

| From | To | Message | Data |
|------|----|---------| -----|
| header.php | Browser | register() | '/service-worker.js' |
| Service Worker | Cache Storage | open() | "fxbg-closet-v1" |
| Service Worker | Cache Storage | put() | url, response |
| Service Worker | Network | fetch() | request |
| Browser | Device OS | install() | manifest data |

### Timing Notes
- **Service worker registration:** ~100-500ms
- **Initial caching (10 assets):** ~500-2000ms
- **Cache-first response:** ~1-10ms (very fast!)
- **Network-first response:** ~50-500ms (depends on connection)
- **Installation process:** ~1-3 seconds

---

## Comparative Analysis

### Similarities
- Both use browser APIs (no external libraries)
- Both provide enhanced user experience
- Both persist data (localStorage vs Cache API)
- Both work offline after initial setup

### Differences

| Aspect | Theme Change | PWA Installation |
|--------|--------------|------------------|
| **Purpose** | Visual preference | Offline capability + native feel |
| **Storage** | localStorage (5-10MB limit) | Cache API (varies by browser, often 50MB+) |
| **Persistence** | Until manually cleared | Until manually cleared or updated |
| **User control** | Explicit toggle button | Browser-initiated prompt |
| **Activation** | Instant (one click) | Multi-step (register → cache → install) |
| **Reversibility** | Easy (one click) | Requires uninstall |
| **Scope** | Single preference | Entire app behavior |

---

## Implementation Complexity

### Theme Change: ⭐⭐ (Low-Medium)
- Simple localStorage operations
- CSS variable switching
- Single event handler
- ~120 lines of JavaScript
- No asynchronous complexity

### PWA Installation: ⭐⭐⭐⭐ (Medium-High)
- Service worker lifecycle management
- Cache strategies and versioning
- Network/cache coordination
- ~117 lines of JavaScript (SW) + integration
- Significant asynchronous operations
- Browser compatibility considerations

---

## Error Handling

### Theme Change
- **localStorage unavailable:** Falls back to system preference
- **No system preference:** Defaults to light mode
- **Invalid theme value:** Treats as light mode
- **Result:** Graceful degradation, always functional

### PWA Installation
- **Service worker unsupported:** App works normally (no offline)
- **Cache fails:** Continues without that asset
- **Network unavailable:** Serves cached content or 503
- **Installation criteria not met:** No prompt shown
- **Result:** Progressive enhancement, core functionality maintained

---

## User Experience Impact

### Theme Change
- **Benefit:** Reduced eye strain, accessibility, preference
- **Improvement:** Instant visual feedback, smooth transitions
- **Persistence:** Preference remembered across sessions
- **User satisfaction:** High (common feature request)

### PWA Installation
- **Benefit:** Offline access, faster load times, native feel
- **Improvement:** No browser chrome, app-like experience
- **Persistence:** Works offline indefinitely
- **User satisfaction:** High for frequent users

---

## For Your Diagrams

### Recommended UML Diagram Types

**Theme Change:**
- **Sequence Diagram** ✓ (shows temporal flow clearly)
- **Activity Diagram** ✓ (shows decision logic)
- **Class Diagram** ⚠️ (minimal classes, mostly functions)

**PWA Installation:**
- **Sequence Diagram** ✓✓ (excellent for showing actor interactions)
- **State Machine Diagram** ✓ (shows SW lifecycle states)
- **Deployment Diagram** ✓ (shows cache/network architecture)

### Tips for Creating Diagrams

1. **Use activation boxes** to show when functions are executing
2. **Show loops** for iteration through assets/cache entries
3. **Use alt/else** for conditional logic (if/else branches)
4. **Include timing notes** for async operations
5. **Add notes** for complex interactions
6. **Show return values** with dashed lines
7. **Group related actors** visually if possible

---

## References to Include in Paper

### Theme Change Feature
- **W3C CSS Variables Spec:** https://www.w3.org/TR/css-variables/
- **MDN localStorage:** https://developer.mozilla.org/en-US/docs/Web/API/Window/localStorage
- **Web.dev Dark Mode Guide:** https://web.dev/prefers-color-scheme/

### PWA Installation Feature
- **W3C Service Worker Spec:** https://www.w3.org/TR/service-workers/
- **W3C Web App Manifest:** https://www.w3.org/TR/appmanifest/
- **Google PWA Checklist:** https://web.dev/pwa-checklist/
- **MDN Cache API:** https://developer.mozilla.org/en-US/docs/Web/API/Cache

---

**This guide provides all the sequence information needed for Assignment #2, Section 4.1. For detailed code and technical explanations, refer to FEATURE_ANALYSIS.md.**
