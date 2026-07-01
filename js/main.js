/**
 * ChatJovenes Theme - Main JS
 */
(function() {
    'use strict';

    // Mobile menu toggle
    var toggle = document.querySelector('.menu-toggle');
    var nav = document.querySelector('.main-nav');
    if (toggle && nav) {
        toggle.addEventListener('click', function() {
            nav.classList.toggle('active');
        });
        document.addEventListener('click', function(e) {
            if (!nav.contains(e.target) && !toggle.contains(e.target)) {
                nav.classList.remove('active');
            }
        });
    }

    // Hide header on scroll down, show on scroll up
    var header = document.querySelector('.site-header');
    if (header) {
        var lastScroll = 0;
        window.addEventListener('scroll', function() {
            var currentScroll = window.scrollY;
            if (currentScroll > 80) {
                if (currentScroll > lastScroll) {
                    header.classList.add('header-hidden');
                } else {
                    header.classList.remove('header-hidden');
                }
                header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.08)';
            } else {
                header.classList.remove('header-hidden');
                header.style.boxShadow = 'none';
            }
            lastScroll = currentScroll;
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
})();
