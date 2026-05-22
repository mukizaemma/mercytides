/**
 * Mercy Tides — scroll reveal (bottom-up fade) via Intersection Observer.
 * Complements WOW.js; works after Turbo navigations when re-initialized.
 */
(function () {
    'use strict';

    var observer = null;

    function prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function initScrollReveal(root) {
        if (prefersReducedMotion()) {
            (root || document).querySelectorAll('.reveal-on-scroll').forEach(function (el) {
                el.classList.add('is-revealed');
            });
            return;
        }

        var scope = root || document;
        var targets = scope.querySelectorAll('.reveal-on-scroll:not(.is-revealed)');

        if (!targets.length) {
            return;
        }

        if (observer) {
            observer.disconnect();
        }

        observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }
                    var el = entry.target;
                    var delay = parseInt(el.getAttribute('data-reveal-delay') || '0', 10);
                    window.setTimeout(function () {
                        el.classList.add('is-revealed');
                    }, delay);
                    observer.unobserve(el);
                });
            },
            { root: null, rootMargin: '0px 0px -8% 0px', threshold: 0.12 }
        );

        targets.forEach(function (el) {
            observer.observe(el);
        });
    }

    window.MercyTidesScrollReveal = { init: initScrollReveal };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initScrollReveal(document);
        });
    } else {
        initScrollReveal(document);
    }
})();
