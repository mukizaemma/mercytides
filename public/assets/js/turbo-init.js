/**
 * Re-initialize page widgets after Turbo Drive navigations.
 * The full-page preloader was removed so content shows immediately;
 * this keeps sliders, backgrounds, and animations working between visits.
 */
(function () {
    'use strict';

    function applyDataBackgrounds(root) {
        var scope = root || document;
        scope.querySelectorAll('[data-background]').forEach(function (el) {
            var url = el.getAttribute('data-background');
            if (url) {
                el.style.backgroundImage = 'url(' + url + ')';
            }
        });
    }

    function destroySwipersInMain() {
        var main = document.querySelector('main');
        if (!main) {
            return;
        }

        main.querySelectorAll('.swiper').forEach(function (el) {
            if (el.swiper) {
                el.swiper.destroy(true, true);
            }
        });
    }

    function initSwiper(selector, options) {
        if (typeof Swiper === 'undefined') {
            return;
        }

        var el = document.querySelector('main ' + selector);
        if (!el || el.classList.contains('swiper-initialized')) {
            return;
        }

        new Swiper(el, options);
    }

    function initPageSwipers() {
        initSwiper('.tp-slider-3__active', {
            loop: true,
            slidesPerView: 1,
            arrows: true,
            autoplay: true,
            effect: 'fade',
            navigation: {
                nextEl: '.test-prev',
                prevEl: '.test-next',
            },
        });

        initSwiper('.tp-donate__active', {
            loop: true,
            slidesPerView: 1,
            arrows: true,
            autoplay: false,
            spaceBetween: 30,
            breakpoints: {
                1200: { slidesPerView: 3 },
                992: { slidesPerView: 3 },
                768: { slidesPerView: 2 },
                0: { slidesPerView: 1, arrows: false },
            },
            navigation: {
                nextEl: '.test-prev',
                prevEl: '.test-next',
            },
        });

        initSwiper('.tp-testimonial__active', {
            loop: true,
            slidesPerView: 3,
            arrows: true,
            autoplay: false,
            spaceBetween: 30,
            centeredSlides: true,
            breakpoints: {
                1200: { slidesPerView: 2, centeredSlides: false },
                992: { slidesPerView: 1, centeredSlides: false },
                0: { slidesPerView: 1, arrows: false, centeredSlides: false },
            },
            navigation: {
                nextEl: '.test-prev',
                prevEl: '.test-next',
            },
        });

        initSwiper('.tp-brand-2__active', {
            loop: true,
            slidesPerView: 1,
            arrows: true,
            autoplay: false,
            breakpoints: {
                1200: { slidesPerView: 5 },
                992: { slidesPerView: 4 },
                768: { slidesPerView: 3 },
                0: { slidesPerView: 1, arrows: false },
            },
            navigation: {
                nextEl: '.test-prev',
                prevEl: '.test-next',
            },
        });
    }

    function initWow() {
        if (typeof WOW === 'undefined') {
            return;
        }

        new WOW({
            mobile: true,
            offset: 80,
            live: true,
        }).init();
    }

    function initScrollReveal() {
        if (typeof window.MercyTidesScrollReveal === 'undefined') {
            return;
        }

        window.MercyTidesScrollReveal.init(document);
    }

    function initPageHeaderParallax() {
        if (typeof window.MercyTidesPageHeaderParallax === 'undefined') {
            return;
        }

        window.MercyTidesPageHeaderParallax.init(document);
    }

    function initNiceSelect() {
        if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.niceSelect) {
            return;
        }

        window.jQuery('main select').each(function () {
            var $select = window.jQuery(this);
            if ($select.next('.nice-select').length) {
                $select.next('.nice-select').remove();
            }
            $select.niceSelect();
        });
    }

    function initPureCounter() {
        if (typeof PureCounter === 'undefined') {
            return;
        }

        document.querySelectorAll('main .purecounter').forEach(function (el) {
            if (!el.dataset.purecounterInitialized) {
                el.dataset.purecounterInitialized = '1';
            }
        });

        new PureCounter();
    }

    var isFirstTurboLoad = true;

    document.addEventListener('turbo:load', function () {
        applyDataBackgrounds(document);

        if (isFirstTurboLoad) {
            isFirstTurboLoad = false;
            return;
        }

        destroySwipersInMain();
        initPageSwipers();
        initWow();
        initScrollReveal();
        initPageHeaderParallax();
        initNiceSelect();
        initPureCounter();
    });

    document.addEventListener('DOMContentLoaded', function () {
        initScrollReveal();
        initPageHeaderParallax();
    });
})();
