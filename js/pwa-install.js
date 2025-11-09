/**
 * PWA Install Prompt
 *
 * Provides a user-friendly install prompt for PWA installation
 * Handles iOS and Android differently due to browser limitations
 */

(function () {
  "use strict";

  let deferredPrompt;
  const installButton = document.getElementById("pwa-install-btn");

  const isStandalone =
    window.matchMedia("(display-mode: standalone)").matches ||
    window.navigator.standalone ||
    document.referrer.includes("android-app://");

  if (isStandalone) {
    console.log("PWA is already installed");
    return;
  }

  const isIOS =
    /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

  const isAndroid = /Android/.test(navigator.userAgent);

  /**
   * Show iOS install instructions
   */
  function showIOSInstallPrompt() {
    const lastShown = localStorage.getItem("pwa-ios-prompt-shown");
    const now = Date.now();

    if (lastShown && now - parseInt(lastShown) < 7 * 24 * 60 * 60 * 1000) {
      return;
    }

    const banner = document.createElement("div");
    banner.id = "ios-install-banner";
    banner.innerHTML = `
      <div style="
        position: fixed;
        bottom: 80px;
        left: 0;
        right: 0;
        background: var(--navbar-bg, #ffffff);
        border-top: 1px solid var(--border-color, #e8c4b8);
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
        padding: 20px;
        z-index: 9999;
        animation: slideUp 0.3s ease-out;
      ">
        <button id="ios-install-close" style="
          position: absolute;
          top: 10px;
          right: 10px;
          background: transparent;
          border: none;
          font-size: 24px;
          color: var(--text-color, #363434);
          cursor: pointer;
          padding: 5px;
          line-height: 1;
        " aria-label="Close">&times;</button>
        
        <div style="
          display: flex;
          align-items: center;
          gap: 16px;
          color: var(--text-color, #363434);
        ">
          <img src="/images/FXBG-PrideWhiteLogo.png" alt="App Icon" style="
            width: 60px;
            height: 60px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
          ">
          <div style="flex: 1;">
            <h3 style="
              margin: 0 0 8px 0;
              font-size: 18px;
              font-weight: 600;
              color: var(--text-color, #363434);
            ">Install FXBG Closet</h3>
            <p style="
              margin: 0;
              font-size: 14px;
              color: var(--text-secondary, #5a5856);
              line-height: 1.4;
            ">
              Install this app on your iPhone: tap 
              <svg style="display: inline; width: 16px; height: 16px; vertical-align: middle;" fill="currentColor" viewBox="0 0 24 24">
                <path d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V5c0-2.21-1.79-4-4-4S7 2.79 7 5v12.5c0 3.04 2.46 5.5 5.5 5.5s5.5-2.46 5.5-5.5V6h-1.5z"></path>
              </svg>
              and then <strong>Add to Home Screen</strong>
            </p>
          </div>
        </div>
      </div>
      <style>
        @keyframes slideUp {
          from {
            transform: translateY(100%);
            opacity: 0;
          }
          to {
            transform: translateY(0);
            opacity: 1;
          }
        }
      </style>
    `;

    document.body.appendChild(banner);

    // Close button
    document
      .getElementById("ios-install-close")
      .addEventListener("click", function () {
        banner.remove();
        localStorage.setItem("pwa-ios-prompt-shown", now.toString());
      });

    // Auto-hide after 15 seconds
    setTimeout(() => {
      if (document.getElementById("ios-install-banner")) {
        banner.remove();
        localStorage.setItem("pwa-ios-prompt-shown", now.toString());
      }
    }, 15000);
  }

  /**
   * Show Android install prompt
   */
  window.addEventListener("beforeinstallprompt", (e) => {
    console.log("PWA install prompt available");

    e.preventDefault();

    deferredPrompt = e;

    if (installButton) {
      installButton.style.display = "block";

      installButton.addEventListener("click", async () => {
        if (!deferredPrompt) {
          return;
        }

        deferredPrompt.prompt();

        const { outcome } = await deferredPrompt.userChoice;
        console.log(`User response to install prompt: ${outcome}`);

        deferredPrompt = null;

        installButton.style.display = "none";
      });
    } else {
      showInlineInstallPrompt();
    }
  });

  /**
   * Show inline install prompt for Android
   */
  function showInlineInstallPrompt() {
    const lastShown = localStorage.getItem("pwa-android-prompt-shown");
    const now = Date.now();

    if (lastShown && now - parseInt(lastShown) < 7 * 24 * 60 * 60 * 1000) {
      return;
    }

    const banner = document.createElement("div");
    banner.id = "android-install-banner";
    banner.innerHTML = `
      <div style="
        position: fixed;
        bottom: 80px;
        left: 16px;
        right: 16px;
        background: var(--navbar-bg, #ffffff);
        border: 1px solid var(--border-color, #e8c4b8);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        padding: 16px;
        z-index: 9999;
        animation: slideUp 0.3s ease-out;
      ">
        <button id="android-install-close" style="
          position: absolute;
          top: 8px;
          right: 8px;
          background: transparent;
          border: none;
          font-size: 20px;
          color: var(--text-color, #363434);
          cursor: pointer;
          padding: 4px;
          line-height: 1;
        " aria-label="Close">&times;</button>
        
        <div style="
          display: flex;
          align-items: center;
          gap: 12px;
          margin-bottom: 12px;
          color: var(--text-color, #363434);
        ">
          <img src="/images/FXBG-PrideWhiteLogo.png" alt="App Icon" style="
            width: 48px;
            height: 48px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
          ">
          <div style="flex: 1;">
            <h3 style="
              margin: 0 0 4px 0;
              font-size: 16px;
              font-weight: 600;
              color: var(--text-color, #363434);
            ">Install FXBG Closet</h3>
            <p style="
              margin: 0;
              font-size: 13px;
              color: var(--text-secondary, #5a5856);
            ">
              Get quick access from your home screen
            </p>
          </div>
        </div>
        
        <button id="android-install-action" style="
          width: 100%;
          padding: 12px;
          background: var(--accent-color, #d4af37);
          color: var(--button-text, #363434);
          border: none;
          border-radius: 8px;
          font-size: 15px;
          font-weight: 600;
          cursor: pointer;
          transition: transform 0.2s;
        " onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
          Install App
        </button>
      </div>
      <style>
        @keyframes slideUp {
          from {
            transform: translateY(100%);
            opacity: 0;
          }
          to {
            transform: translateY(0);
            opacity: 1;
          }
        }
      </style>
    `;

    document.body.appendChild(banner);

    // Install button
    document
      .getElementById("android-install-action")
      .addEventListener("click", async () => {
        if (!deferredPrompt) {
          return;
        }

        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        console.log(`User response: ${outcome}`);

        banner.remove();
        localStorage.setItem("pwa-android-prompt-shown", now.toString());
        deferredPrompt = null;
      });

    // Close button
    document
      .getElementById("android-install-close")
      .addEventListener("click", function () {
        banner.remove();
        localStorage.setItem("pwa-android-prompt-shown", now.toString());
      });

    // Auto-hide after 20 seconds
    setTimeout(() => {
      if (document.getElementById("android-install-banner")) {
        banner.remove();
        localStorage.setItem("pwa-android-prompt-shown", now.toString());
      }
    }, 20000);
  }

  /**
   * Track successful installation
   */
  window.addEventListener("appinstalled", () => {
    console.log("PWA was installed successfully");

    const iosBanner = document.getElementById("ios-install-banner");
    const androidBanner = document.getElementById("android-install-banner");

    if (iosBanner) iosBanner.remove();
    if (androidBanner) androidBanner.remove();
    if (installButton) installButton.style.display = "none";
  });

  /**
   * Show appropriate install prompt based on platform
   */
  function showInstallPrompt() {
    setTimeout(() => {
      if (isIOS && !isStandalone) {
        showIOSInstallPrompt();
      } else if (isAndroid && deferredPrompt) {
        showInlineInstallPrompt();
      }
    }, 3000);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", showInstallPrompt);
  } else {
    showInstallPrompt();
  }
})();
