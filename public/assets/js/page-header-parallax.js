/**
 * Inner page headers: full-viewport hero + scroll parallax on background layer.
 * Background is clipped to the hero (does not paint under the site nav).
 */
(function () {
    'use strict';

    var scrollBound = false;

    function prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    function applyBackgrounds(root) {
        var scope = root || document;
        scope.querySelectorAll('.tp-breadcrumb__parallax-bg[data-background]').forEach(function (el) {
            var url = el.getAttribute('data-background');
            if (url) {
                el.style.backgroundImage = 'url(' + url + ')';
            }
        });
    }

    function updateParallax() {
        if (prefersReducedMotion()) {
            return;
        }

        var vh = window.innerHeight;

        document.querySelectorAll('.tp-breadcrumb--fullscreen').forEach(function (section) {
            var bg = section.querySelector('.tp-breadcrumb__parallax-bg');
            if (!bg) {
                return;
            }

            var rect = section.getBoundingClientRect();
            if (rect.bottom < 0 || rect.top > vh) {
                return;
            }

            var travel = rect.height + vh;
            var progress = (vh - rect.top) / travel;
            var shift = (progress - 0.35) * 100;

            bg.style.transform = 'translate3d(0, ' + shift.toFixed(2) + 'px, 0) scale(1.12)';
        });
    }

    function bindScroll() {
        if (scrollBound) {
            return;
        }

        var ticking = false;

        function onScroll() {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    updateParallax();
                    ticking = false;
                });
                ticking = true;
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll, { passive: true });
        scrollBound = true;
    }

    function init(root) {
        applyBackgrounds(root);
        updateParallax();
        bindScroll();
    }

    window.MercyTidesPageHeaderParallax = { init: init, update: updateParallax };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            init(document);
        });
    } else {
        init(document);
    }
})();
