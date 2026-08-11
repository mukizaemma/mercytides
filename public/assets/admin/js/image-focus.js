/**
 * Click-or-slider focal point for circular team portraits.
 */
(function () {
    'use strict';

    function clamp(value) {
        return Math.max(0, Math.min(100, value));
    }

    function bindPicker(root) {
        if (root.dataset.focusBound === '1') {
            return;
        }
        root.dataset.focusBound = '1';

        const preview = root.querySelector('[data-focus-preview]');
        const image = root.querySelector('[data-focus-image]');
        const pin = root.querySelector('[data-focus-pin]');
        const inputX = root.querySelector('[data-focus-x]');
        const inputY = root.querySelector('[data-focus-y]');
        const hidden = root.querySelector('[data-focus-input]');

        function apply(x, y) {
            x = clamp(x);
            y = clamp(y);
            if (inputX) inputX.value = Math.round(x);
            if (inputY) inputY.value = Math.round(y);
            if (hidden) hidden.value = Math.round(x) + ' ' + Math.round(y);
            if (image) image.style.objectPosition = x + '% ' + y + '%';
            if (pin) {
                pin.style.left = x + '%';
                pin.style.top = y + '%';
            }
        }

        function fromSliders() {
            apply(inputX ? inputX.value : 50, inputY ? inputY.value : 18);
        }

        if (inputX) inputX.addEventListener('input', fromSliders);
        if (inputY) inputY.addEventListener('input', fromSliders);

        if (preview) {
            preview.addEventListener('click', function (event) {
                const rect = preview.getBoundingClientRect();
                if (!rect.width || !rect.height) {
                    return;
                }
                const x = ((event.clientX - rect.left) / rect.width) * 100;
                const y = ((event.clientY - rect.top) / rect.height) * 100;
                apply(x, y);
            });
        }
    }

    function init() {
        document.querySelectorAll('[data-image-focus]').forEach(bindPicker);
    }

    document.addEventListener('DOMContentLoaded', init);
    document.addEventListener('turbo:load', init);
    document.addEventListener('turbo:frame-load', init);
})();
