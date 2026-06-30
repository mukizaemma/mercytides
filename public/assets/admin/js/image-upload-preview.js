/**
 * Shows estimated optimized image size/dimensions before admin upload.
 */
(function () {
    'use strict';

    const DEFAULTS = {
        maxBytes: 700 * 1024,
        maxWidth: 2200,
        maxHeight: 2200,
        minQuality: 0.58,
        initialQuality: 0.85,
        presets: {
            portrait: { maxWidth: 1400, maxHeight: 2100 },
            hero: { maxWidth: 2400, maxHeight: 1350 },
            logo: { maxWidth: 900, maxHeight: 900, maxBytes: 350 * 1024 },
            thumb: { maxWidth: 1200, maxHeight: 1200 },
        },
    };

    function config() {
        const fromPage = window.MercyTidesImageUpload || {};
        return {
            maxBytes: fromPage.maxBytes || DEFAULTS.maxBytes,
            maxWidth: fromPage.maxWidth || DEFAULTS.maxWidth,
            maxHeight: fromPage.maxHeight || DEFAULTS.maxHeight,
            minQuality: fromPage.minQuality || DEFAULTS.minQuality,
            initialQuality: fromPage.initialQuality || DEFAULTS.initialQuality,
            presets: Object.assign({}, DEFAULTS.presets, fromPage.presets || {}),
        };
    }

    function formatBytes(bytes) {
        if (!bytes || bytes < 1) {
            return '0 B';
        }
        if (bytes < 1024) {
            return bytes + ' B';
        }
        if (bytes < 1024 * 1024) {
            return (bytes / 1024).toFixed(1) + ' KB';
        }
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function canvasToBlob(canvas, type, quality) {
        return new Promise(function (resolve) {
            canvas.toBlob(function (blob) {
                resolve(blob);
            }, type, quality);
        });
    }

    function loadImage(file) {
        return new Promise(function (resolve, reject) {
            const url = URL.createObjectURL(file);
            const img = new Image();
            img.onload = function () {
                URL.revokeObjectURL(url);
                resolve(img);
            };
            img.onerror = function () {
                URL.revokeObjectURL(url);
                reject(new Error('Could not read image.'));
            };
            img.src = url;
        });
    }

    function drawImage(img, width, height) {
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, width, height);
        ctx.drawImage(img, 0, 0, width, height);
        return canvas;
    }

    async function optimizePreview(file, options) {
        const img = await loadImage(file);
        const originalWidth = img.naturalWidth || img.width;
        const originalHeight = img.naturalHeight || img.height;
        let width = originalWidth;
        let height = originalHeight;
        let resized = false;

        const fit = function () {
            const scale = Math.min(1, options.maxWidth / width, options.maxHeight / height);
            if (scale < 1) {
                resized = true;
                width = Math.max(1, Math.round(width * scale));
                height = Math.max(1, Math.round(height * scale));
            }
        };

        fit();

        let quality = options.initialQuality;
        let canvas = drawImage(img, width, height);
        let blob = await canvasToBlob(canvas, 'image/jpeg', quality);
        let attempts = 0;

        while (blob && blob.size > options.maxBytes && attempts < 16) {
            if (quality > options.minQuality) {
                quality = Math.max(options.minQuality, quality - 0.07);
            } else {
                width = Math.max(1, Math.round(width * 0.88));
                height = Math.max(1, Math.round(height * 0.88));
                resized = true;
                canvas = drawImage(img, width, height);
                quality = options.initialQuality;
            }
            blob = await canvasToBlob(canvas, 'image/jpeg', quality);
            attempts++;
        }

        const compressed = file.size > (blob ? blob.size : file.size) + 2048;
        const needsOptimization = resized || compressed || file.size > options.maxBytes;

        return {
            previewUrl: blob ? URL.createObjectURL(blob) : '',
            width: width,
            height: height,
            originalWidth: originalWidth,
            originalHeight: originalHeight,
            originalSize: file.size,
            optimizedSize: blob ? blob.size : file.size,
            resized: resized,
            compressed: compressed,
            needsOptimization: needsOptimization,
        };
    }

    function resolveOptions(input) {
        const base = config();
        const presetName = input.getAttribute('data-image-preset') || '';
        const preset = presetName && base.presets[presetName] ? base.presets[presetName] : {};

        return {
            maxBytes: preset.maxBytes || base.maxBytes,
            maxWidth: preset.maxWidth || base.maxWidth,
            maxHeight: preset.maxHeight || base.maxHeight,
            minQuality: base.minQuality,
            initialQuality: base.initialQuality,
        };
    }

    function ensurePanel(input) {
        let panel = input.parentElement.querySelector('.image-upload-preview');
        if (!panel) {
            panel = document.createElement('div');
            panel.className = 'image-upload-preview mt-2';
            panel.hidden = true;
            panel.innerHTML =
                '<div class="image-upload-preview__card">' +
                    '<div class="image-upload-preview__thumb-wrap">' +
                        '<img class="image-upload-preview__thumb" alt="" width="48" height="64">' +
                    '</div>' +
                    '<div class="image-upload-preview__body">' +
                        '<div class="image-upload-preview__badge-wrap"></div>' +
                        '<p class="image-upload-preview__line mb-1"><strong>Original:</strong> <span data-role="original"></span></p>' +
                        '<p class="image-upload-preview__line mb-0"><strong>After upload:</strong> <span data-role="optimized"></span></p>' +
                        '<p class="image-upload-preview__hint mb-0"></p>' +
                    '</div>' +
                '</div>';
            const hint = input.parentElement.querySelector('.form-text, small.text-muted');
            if (hint) {
                hint.insertAdjacentElement('afterend', panel);
            } else {
                input.insertAdjacentElement('afterend', panel);
            }
        }
        return panel;
    }

    function setBadge(container, result) {
        container.innerHTML = '';
        const badge = document.createElement('span');
        if (!result.needsOptimization) {
            badge.className = 'badge bg-success image-upload-preview__badge';
            badge.textContent = 'Ready — no resize needed';
        } else if (result.resized && result.compressed) {
            badge.className = 'badge bg-primary image-upload-preview__badge';
            badge.textContent = 'Will be resized and compressed';
        } else if (result.resized) {
            badge.className = 'badge bg-primary image-upload-preview__badge';
            badge.textContent = 'Will be resized';
        } else {
            badge.className = 'badge bg-primary image-upload-preview__badge';
            badge.textContent = 'Will be compressed';
        }
        container.appendChild(badge);
    }

    function revokePrevious(panel) {
        const thumb = panel.querySelector('.image-upload-preview__thumb');
        if (thumb && thumb.dataset.objectUrl) {
            URL.revokeObjectURL(thumb.dataset.objectUrl);
            thumb.removeAttribute('data-object-url');
        }
    }

    async function handleInput(input) {
        const panel = ensurePanel(input);
        revokePrevious(panel);

        const files = input.files ? Array.from(input.files).filter(function (f) {
            return f.type && f.type.indexOf('image/') === 0;
        }) : [];

        if (!files.length) {
            panel.hidden = true;
            return;
        }

        panel.hidden = false;
        panel.classList.add('is-loading');

        try {
            const options = resolveOptions(input);
            const result = await optimizePreview(files[0], options);
            const thumb = panel.querySelector('.image-upload-preview__thumb');
            const originalEl = panel.querySelector('[data-role="original"]');
            const optimizedEl = panel.querySelector('[data-role="optimized"]');
            const hintEl = panel.querySelector('.image-upload-preview__hint');
            const badgeWrap = panel.querySelector('.image-upload-preview__badge-wrap');

            if (result.previewUrl) {
                thumb.src = result.previewUrl;
                thumb.dataset.objectUrl = result.previewUrl;
            }

            originalEl.textContent = formatBytes(result.originalSize) + ' · ' + result.originalWidth + '×' + result.originalHeight + ' px';
            optimizedEl.textContent = formatBytes(result.optimizedSize) + ' · ' + result.width + '×' + result.height + ' px';

            setBadge(badgeWrap, result);

            if (files.length > 1) {
                hintEl.textContent = 'Showing estimate for the first of ' + files.length + ' selected images. Each will be optimized on upload.';
            } else if (result.needsOptimization) {
                hintEl.textContent = 'Estimated preview — the server will optimize before saving (max ' + Math.round(options.maxBytes / 1024) + ' KB).';
            } else {
                hintEl.textContent = 'This image is already within the recommended size limits.';
            }
        } catch (error) {
            panel.querySelector('[data-role="original"]').textContent = 'Could not preview this file.';
            panel.querySelector('[data-role="optimized"]').textContent = '—';
            panel.querySelector('.image-upload-preview__hint').textContent = error.message || 'Unsupported image.';
        } finally {
            panel.classList.remove('is-loading');
        }
    }

    function shouldBind(input) {
        if (input.dataset.imagePreviewBound === 'true') {
            return false;
        }
        if (input.dataset.imagePreview === 'off') {
            return false;
        }
        const name = (input.getAttribute('name') || '').toLowerCase();
        if (name === 'document' || name.indexOf('document') !== -1) {
            return false;
        }
        const accept = (input.getAttribute('accept') || '').toLowerCase();
        if (accept.indexOf('image') !== -1) {
            return true;
        }
        if (name.indexOf('image') !== -1 || name === 'logo' || name.indexOf('gallery') !== -1 || name.indexOf('img') !== -1) {
            return true;
        }
        return false;
    }

    function bindInput(input) {
        if (!shouldBind(input)) {
            return;
        }
        input.dataset.imagePreviewBound = 'true';
        input.addEventListener('change', function () {
            handleInput(input);
        });
    }

    function initImageUploadPreview(root) {
        const scope = root || document;
        scope.querySelectorAll('input[type="file"]').forEach(bindInput);
    }

    window.initImageUploadPreview = initImageUploadPreview;

    document.addEventListener('DOMContentLoaded', function () {
        initImageUploadPreview(document);
    });
    document.addEventListener('turbo:load', function () {
        initImageUploadPreview(document);
    });
    document.addEventListener('shown.bs.modal', function (event) {
        if (event.target) {
            initImageUploadPreview(event.target);
        }
    });
})();
