<!-- Modern Header Navigation -->
<?php
date_default_timezone_set('America/New_York');/*
* Copyright 2013 by Allen Tucker.
* This program is part of RMHP-Homebase, which is free software.  It comes with
* absolutely no warranty. You can redistribute and/or modify it under the terms
* of the GNU General Public License as published by the Free Software Foundation
* (see <http://www.gnu.org/licenses/ for more information).
*
if (date("H:i:s") > "18:19:59") {
 require_once 'database/dbShifts.php';
 auto_checkout_missing_shifts();
}
*/

// check if we are in locked mode, if so,
// user cannot access anything else without
// logging back in
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
            
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/theme-toggle.css">
    <link rel="stylesheet" href="css/mobile-nav.css">
    <link rel="stylesheet" href="css/pwa-mobile.css">
    
    <!-- PWA Meta Tags - Enables offline support, installation, etc. -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#6B46C1">
    <meta name="description" content="Volunteer Management System for Fredericksburg Pride - FXBG Closet">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FXBG Closet">
    <link rel="apple-touch-icon" href="/images/FXBG-PrideWhiteLogo.png">
</head>

<header>
    <?php
            echo('
            <nav class="modern-navbar">
                <div class="nav-container">
                    <!-- Logo and Brand -->
                    <div class="nav-brand">
                        <a href="https://fxbgpride.org">
                            <img src="images/FXBG-PrideLogo.png" alt="Logo" class="nav-logo logo-lightMode">
                            <img src="images/FXBG-PrideWhiteLogo.png" alt="Logo (Dark Mode)" class="nav-logo logo-darkMode">
                        </a>
                    </div>

                    <!-- Main Navigation -->
                    <div class="nav-menu" id="navMenu">
                        <div class="nav-dropdown">
                        </div>

                        <div class="nav-dropdown">
                        </div>
                    </div>

                    <!-- Right Actions -->
                    <div class="nav-actions">
                        
                        <button class="theme-toggle nav-action-btn" aria-label="Toggle theme" title="Toggle dark/light mode">
                            <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                            <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
                            </svg>
                        </button>

                        <div class="nav-date"></div>

                        <div class="nav-dropdown user-dropdown">
                        </div>

                        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle mobile menu">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </nav>');
            ?>

    <script>
    function updateNavDate() {
        const now = new Date();
        const width = window.innerWidth;
        let formatted = "";

        if (width > 1400) {
            formatted = now.toLocaleDateString("en-US", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric"
            });
        } else if (width >= 1000) {
            formatted = now.toLocaleDateString("en-US", {
                month: "short",
                day: "numeric",
                year: "numeric"
            });
        } else {
            formatted = now.toLocaleDateString("en-US", {
                month: "short",
                day: "numeric"
            });
        }

        document.querySelectorAll(".nav-date").forEach(el => {
            if (width < 900) {
                el.style.display = "none";
            } else {
                el.style.display = "";
                el.textContent = formatted;
            }
        });
    }

    // Dropdown functionality
    document.addEventListener("DOMContentLoaded", function() {
        const dropdowns = document.querySelectorAll(".nav-dropdown");
        
        dropdowns.forEach(dropdown => {
            const button = dropdown.querySelector("button");
            const menu = dropdown.querySelector(".dropdown-menu");
            
            if (button && menu) {
                button.addEventListener("click", function(e) {
                    e.stopPropagation();
                    
                    // Close other dropdowns
                    dropdowns.forEach(other => {
                        if (other !== dropdown) {
                            other.classList.remove("active");
                        }
                    });
                    
                    // Toggle current dropdown
                    dropdown.classList.toggle("active");
                });
            }
        });

        // Close dropdowns when clicking outside
        document.addEventListener("click", function() {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove("active");
            });
        });

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById("mobileMenuBtn");
        const navMenu = document.getElementById("navMenu");
        
        if (mobileMenuBtn && navMenu) {
            mobileMenuBtn.addEventListener("click", function(e) {
                e.stopPropagation();
                this.classList.toggle("active");
                navMenu.classList.toggle("active");
                document.body.classList.toggle("mobile-menu-open");
            });
        }

        // Update date on load and resize
        updateNavDate();
        window.addEventListener("resize", updateNavDate);
    });
    </script>
    <script src="js/theme-toggle.js"></script>

    <?php
    // Mobile Bottom Navigation (PWA-optimized)
            echo('
            <nav class="mobile-bottom-nav">
                <a href="https://fxbgpride.org" class="mobile-nav-item" data-page="index">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>FXBG Pride</span>
                </a>
                <button class="mobile-nav-item mobile-nav-more" id="mobileMoreBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="12" cy="5" r="1"></circle>
                        <circle cx="12" cy="19" r="1"></circle>
                    </svg>
                    <span>More</span>
                </button>
            </nav>
            
            <!-- Mobile More Menu -->
            <div class="mobile-more-menu" id="mobileMoreMenu">
                <div class="mobile-more-header">
                    <h3>More Options</h3>
                    <button class="mobile-more-close" id="mobileMoreClose">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="mobile-more-content">
                </div>
            </div>');
            ?>
    
    <script>
    // Mobile Bottom Nav Active State & More Menu
    document.addEventListener('DOMContentLoaded', function() {
        // Set active state based on current page
        const currentPage = window.location.pathname.split('/').pop().replace('.php', '');
        const mobileNavItems = document.querySelectorAll('.mobile-nav-item[data-page]');
        
        mobileNavItems.forEach(item => {
            const page = item.getAttribute('data-page');
            if (currentPage.includes(page) || (currentPage === '' && page === 'index')) {
                item.classList.add('active');
            }
        });
        
        // More menu functionality
        const moreBtn = document.getElementById('mobileMoreBtn');
        const moreMenu = document.getElementById('mobileMoreMenu');
        const moreClose = document.getElementById('mobileMoreClose');
        
        if (moreBtn && moreMenu && moreClose) {
            moreBtn.addEventListener('click', function(e) {
                e.preventDefault();
                moreMenu.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
            
            moreClose.addEventListener('click', function() {
                moreMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
            
            // Close when clicking outside
            moreMenu.addEventListener('click', function(e) {
                if (e.target === moreMenu) {
                    moreMenu.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        }
        
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
    });
    </script>
</header>
