<?php

/**
 * PWA Meta Tags and Service Worker Registration
 * Include this file in your page headers to enable PWA functionality
 * Usage: <?php include 'include/pwa-meta.php'; ?>
 */

?>
<!-- PWA Manifest -->
<link rel="manifest" href="/manifest.json">

<!-- PWA Meta Tags -->
<meta name="theme-color" content="#6B46C1">
<meta name="description" content="Volunteer Management System for Fredericksburg Pride - FXBG Closet">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="FXBG Closet">
<link rel="apple-touch-icon" href="/images/FXBG-PrideWhiteLogo.png">

<!-- Service Worker Registration -->
<script>
    if ('serviceWorker' in navigator && !window.location.hostname.includes('localhost')) {
        // Only register on non-localhost in production
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/service-worker.js')
                .then((registration) => {
                    console.log('Service Worker registered:', registration);
                })
                .catch((error) => {
                    console.log('Service Worker registration failed:', error);
                });
        });
    } else if ('serviceWorker' in navigator) {
        // Register on localhost for development
        navigator.serviceWorker.register('/service-worker.js')
            .then((registration) => {
                console.log('Service Worker registered (dev):', registration);
            })
            .catch((error) => {
                console.log('Service Worker registration failed (dev):', error);
            });
    }
</script>
