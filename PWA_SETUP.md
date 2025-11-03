# PWA Setup Guide for FXBG Closet

## What is a Progressive Web App (PWA)?

A Progressive Web App is a web application that uses modern web technologies to deliver an app-like experience to users. PWAs can be installed on phones/tablets, work offline, and send push notifications.

## Files Added

### 1. **manifest.json**
The web app manifest file that tells browsers how to display your PWA:
- Defines the app name, description, and icons
- Specifies the start URL and display mode
- Includes app shortcuts for quick access to key pages
- Sets theme and background colors

### 2. **service-worker.js**
The service worker script that enables offline functionality:
- Caches static assets (CSS, JS, images) on first load
- Implements a network-first strategy for PHP pages (always try network first)
- Implements a cache-first strategy for static assets
- Allows the app to work offline with previously cached content
- Automatically cleans up old caches

### 3. **Updated index.php**
Added PWA metadata to the HTML `<head>`:
- Link to the manifest file
- Meta tags for iOS compatibility
- Apple-specific PWA settings
- Service Worker registration script

## How to Test the PWA

### Desktop (Chrome/Edge)
1. Go to `http://localhost` (or your server URL)
2. Open DevTools (F12)
3. Go to **Application** tab → **Manifest** to verify manifest is loaded
4. Go to **Application** tab → **Service Workers** to verify SW is registered
5. Look for the "Install" button in the address bar (if on HTTPS)

### Mobile (Android)
1. Visit your site on Chrome for Android
2. You should see an "Install" prompt
3. Tap "Install" to add to home screen
4. The app will appear as an icon with your logo
5. Works offline with previously cached pages

### iOS
1. Visit your site in Safari
2. Tap the Share button
3. Tap "Add to Home Screen"
4. The app will be added to your home screen
5. Note: iOS has limited PWA support compared to Android

## Testing Offline Mode

### In Chrome DevTools:
1. Open DevTools (F12)
2. Go to **Application** tab → **Service Workers**
3. Check the "Offline" checkbox
4. Reload the page
5. Pages you've previously visited should load from cache

## Installation Instructions

All PWA files are already in place! To enable PWA functionality:

### Required (Already Done)
- ✅ `manifest.json` created
- ✅ `service-worker.js` created  
- ✅ `index.php` updated with PWA meta tags

### Recommendations

#### 1. **Add to Other Pages**
To add PWA functionality to all pages, add this to the `<head>` of your base template or header:
```html
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#6B46C1">
<meta name="description" content="Volunteer Management System for Fredericksburg Pride">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="FXBG Closet">
<link rel="apple-touch-icon" href="/images/FXBG-PrideWhiteLogo.png">
```

And register the service worker:
```html
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js');
    }
</script>
```

#### 2. **Create Icons**
The manifest references your logo (`/images/FXBG-PrideWhiteLogo.png`). For best results:
- Create 192x192 px version for Android app drawer
- Create 512x512 px version for splash screen
- Create a maskable variant (rounded corners) for adaptive icons
- Save as PNG with transparency

#### 3. **Optional: Push Notifications**
If you want to add push notifications later:
```javascript
Notification.requestPermission().then(permission => {
    if (permission === 'granted') {
        new Notification('Welcome to FXBG Closet!');
    }
});
```

#### 4. **Optional: Install Prompts**
Add custom install UI to your app:
```javascript
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    // Show your custom install button
});

document.getElementById('installBtn').addEventListener('click', async () => {
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    console.log(`User response: ${outcome}`);
});
```

#### 5. **HTTPS in Production**
⚠️ **Important**: Service Workers only work on HTTPS in production (localhost is an exception). When deploying to your live server, ensure you have an SSL certificate.

## Caching Strategy

The service worker uses different strategies for different types of content:

### **PHP Files (Network-First)**
- Always tries to fetch from the network first
- Falls back to cache if offline
- Best for dynamic content like user data

### **Static Assets (Cache-First)**  
- Uses cached version if available
- Falls back to network
- Best for CSS, JS, images

## Updating the Cache

When you make changes to static assets:
1. Update the `CACHE_NAME` version in `service-worker.js`
2. Old caches are automatically deleted on service worker activation
3. Users will get fresh assets on next visit

Example: Change `'fxbg-closet-v1'` to `'fxbg-closet-v2'`

## Troubleshooting

### Service Worker won't register
- Check console for errors
- Ensure `service-worker.js` is in the root directory
- Check HTTPS (or localhost)

### Content not updating
- Open DevTools → Application → Storage → Clear site data
- Or increment `CACHE_NAME` version

### Offline pages show error
- Those pages weren't cached during online browsing
- Visit the page while online first

## Performance Impact

✅ **Benefits:**
- 30-50% faster page loads with cached assets
- Works offline (great for unreliable connections)
- Native app-like experience
- Reduced bandwidth usage

⚠️ **Considerations:**
- Service Worker takes ~50-100KB of memory
- Cache storage (typically 50MB limit per site)
- Potential issues with frequently updated content

## Browser Support

| Browser | Support |
|---------|---------|
| Chrome | ✅ Full |
| Edge | ✅ Full |
| Firefox | ✅ Full |
| Safari (iOS) | ⚠️ Limited |
| Safari (Mac) | ✅ Full |

## Next Steps

1. Test the PWA on various devices
2. Collect user feedback on offline experience
3. Consider adding push notifications
4. Monitor cache performance in production
5. Update icons/branding in `manifest.json` as needed

## Resources

- [MDN: Progressive Web Apps](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
- [Google: Progressive Web Apps](https://developers.google.com/web/progressive-web-apps)
- [Service Worker API](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [Web App Manifest](https://developer.mozilla.org/en-US/docs/Web/Manifest)
