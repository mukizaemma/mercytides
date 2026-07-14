(function () {
    'use strict';

    if (window.__siteGalleryLightboxReady) {
        return;
    }
    window.__siteGalleryLightboxReady = true;

    var state = {
        index: 0,
        root: null,
        imageEl: null,
        videoWrap: null,
        frame: null,
        captionEl: null,
        counterEl: null,
    };

    function tiles() {
        var grid = document.getElementById('siteGalleryGrid');
        if (!grid) return [];
        return Array.prototype.slice.call(grid.querySelectorAll('[data-gallery-open]'));
    }

    function items() {
        return tiles().map(function (tile) {
            return {
                type: tile.getAttribute('data-type') || 'image',
                src: tile.getAttribute('data-src') || '',
                caption: tile.getAttribute('data-caption') || '',
            };
        });
    }

    function cacheEls() {
        state.root = document.getElementById('galleryLightbox');
        if (!state.root) return false;
        state.imageEl = document.getElementById('galleryLightboxImage');
        state.videoWrap = document.getElementById('galleryLightboxVideoWrap');
        state.frame = document.getElementById('galleryLightboxFrame');
        state.captionEl = document.getElementById('galleryLightboxCaption');
        state.counterEl = document.getElementById('galleryLightboxCounter');
        return true;
    }

    function show(i) {
        if (!cacheEls()) return;
        var list = items();
        if (!list.length) return;

        state.index = ((i % list.length) + list.length) % list.length;
        var item = list[state.index];

        if (item.type === 'video') {
            state.imageEl.hidden = true;
            state.imageEl.removeAttribute('src');
            state.videoWrap.hidden = false;
            state.frame.src = item.src;
        } else {
            state.frame.src = '';
            state.videoWrap.hidden = true;
            state.imageEl.hidden = false;
            state.imageEl.src = item.src;
            state.imageEl.alt = item.caption || 'Gallery image';
        }

        if (item.caption) {
            state.captionEl.hidden = false;
            state.captionEl.textContent = item.caption;
        } else {
            state.captionEl.hidden = true;
            state.captionEl.textContent = '';
        }

        state.counterEl.textContent = state.index + 1 + ' / ' + list.length;
    }

    function open(i) {
        if (!cacheEls()) return;
        state.root.hidden = false;
        state.root.setAttribute('aria-hidden', 'false');
        document.documentElement.classList.add('gallery-lightbox-open');
        show(i);
    }

    function close() {
        if (!cacheEls()) return;
        state.root.hidden = true;
        state.root.setAttribute('aria-hidden', 'true');
        document.documentElement.classList.remove('gallery-lightbox-open');
        if (state.frame) state.frame.src = '';
    }

    function onClick(e) {
        var openBtn = e.target.closest('[data-gallery-open]');
        if (openBtn && document.getElementById('siteGalleryGrid') && document.getElementById('siteGalleryGrid').contains(openBtn)) {
            e.preventDefault();
            open(parseInt(openBtn.getAttribute('data-index'), 10) || 0);
            return;
        }

        if (!state.root || state.root.hidden) return;

        if (e.target.closest('[data-gallery-close]')) {
            e.preventDefault();
            close();
            return;
        }
        if (e.target.closest('[data-gallery-prev]')) {
            e.preventDefault();
            show(state.index - 1);
            return;
        }
        if (e.target.closest('[data-gallery-next]')) {
            e.preventDefault();
            show(state.index + 1);
        }
    }

    function onKey(e) {
        if (!cacheEls() || state.root.hidden) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft') show(state.index - 1);
        if (e.key === 'ArrowRight') show(state.index + 1);
    }

    document.addEventListener('click', onClick);
    document.addEventListener('keydown', onKey);
    document.addEventListener('turbo:before-cache', close);
})();
