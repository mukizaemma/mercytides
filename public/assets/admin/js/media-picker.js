/**
 * Admin media picker: upload a new image or select from existing library.
 */
(function () {
    'use strict';

    function debounce(fn, wait) {
        let t;
        return function () {
            const args = arguments;
            const ctx = this;
            clearTimeout(t);
            t = setTimeout(function () {
                fn.apply(ctx, args);
            }, wait);
        };
    }

    function initPicker(root) {
        if (!root || root.dataset.pickerReady === '1') {
            return;
        }
        root.dataset.pickerReady = '1';

        const mode = root.dataset.mode || 'single';
        const libraryUrl = root.dataset.libraryUrl;
        const libraryName = root.dataset.libraryName || (mode === 'multiple' ? 'gallery_paths[]' : 'image_path');
        const tabs = root.querySelectorAll('[data-media-tab]');
        const panels = root.querySelectorAll('[data-media-panel]');
        const uploadInput = root.querySelector('[data-media-upload]');
        const grid = root.querySelector('[data-media-grid]');
        const search = root.querySelector('[data-media-search]');
        const libraryInput = root.querySelector('[data-media-library-input]');
        const libraryInputsWrap = root.querySelector('[data-media-library-inputs]');
        const selectedList = root.querySelector('[data-media-selected-list]');
        const currentInline = root.querySelector('[data-media-current-inline]');

        let selected = [];
        let loaded = false;
        let items = [];

        function setTab(name) {
            tabs.forEach(function (btn) {
                btn.classList.toggle('active', btn.dataset.mediaTab === name);
            });
            panels.forEach(function (panel) {
                panel.classList.toggle('d-none', panel.dataset.mediaPanel !== name);
            });
            if (name === 'library' && !loaded) {
                fetchLibrary();
            }
        }

        tabs.forEach(function (btn) {
            btn.addEventListener('click', function () {
                setTab(btn.dataset.mediaTab);
            });
        });

        function clearUpload() {
            if (uploadInput) {
                uploadInput.value = '';
                uploadInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        if (uploadInput) {
            uploadInput.addEventListener('change', function () {
                if (uploadInput.files && uploadInput.files.length) {
                    selected = [];
                    if (libraryInput) {
                        libraryInput.value = '';
                    }
                    if (libraryInputsWrap) {
                        libraryInputsWrap.innerHTML = '';
                    }
                    if (selectedList) {
                        selectedList.innerHTML = '';
                    }
                    if (currentInline) {
                        currentInline.classList.add('d-none');
                    }
                    highlightGrid();
                }
            });
        }

        function syncLibraryInputs() {
            if (mode === 'multiple' && libraryInputsWrap) {
                libraryInputsWrap.innerHTML = '';
                selected.forEach(function (item) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = libraryName;
                    input.value = item.path;
                    libraryInputsWrap.appendChild(input);
                });
            } else if (libraryInput) {
                libraryInput.value = selected.length ? selected[0].path : '';
            }
        }

        function renderSelected() {
            if (mode !== 'multiple' || !selectedList) {
                return;
            }
            selectedList.innerHTML = '';
            selected.forEach(function (item) {
                const chip = document.createElement('div');
                chip.className = 'admin-media-picker__chip';
                chip.innerHTML =
                    '<img src="' + item.url + '" alt="" class="admin-media-picker__thumb">' +
                    '<button type="button" class="btn-close btn-close-white admin-media-picker__chip-remove" aria-label="Remove"></button>';
                chip.querySelector('button').addEventListener('click', function () {
                    selected = selected.filter(function (s) {
                        return s.path !== item.path;
                    });
                    syncLibraryInputs();
                    renderSelected();
                    highlightGrid();
                });
                selectedList.appendChild(chip);
            });
        }

        function highlightGrid() {
            if (!grid) {
                return;
            }
            grid.querySelectorAll('[data-path]').forEach(function (btn) {
                const on = selected.some(function (s) {
                    return s.path === btn.dataset.path;
                });
                btn.classList.toggle('is-selected', on);
            });
        }

        function toggleSelect(item) {
            clearUpload();
            if (currentInline) {
                currentInline.classList.add('d-none');
            }
            if (mode === 'single') {
                selected = [item];
                if (libraryInput) {
                    libraryInput.value = item.path;
                }
                // Show preview in selected area
                const selectedWrap = root.querySelector('[data-media-selected]');
                if (selectedWrap) {
                    selectedWrap.innerHTML =
                        '<div class="admin-media-picker__current d-flex align-items-center gap-2">' +
                        '<img src="' + item.url + '" alt="" class="admin-media-picker__thumb">' +
                        '<span class="small">Selected from library</span>' +
                        '<button type="button" class="btn btn-link btn-sm text-danger p-0 ms-auto" data-clear-selection>Clear</button>' +
                        '</div>';
                    const clearBtn = selectedWrap.querySelector('[data-clear-selection]');
                    if (clearBtn) {
                        clearBtn.addEventListener('click', function () {
                            selected = [];
                            if (libraryInput) {
                                libraryInput.value = '';
                            }
                            selectedWrap.innerHTML = '';
                            highlightGrid();
                        });
                    }
                }
            } else {
                const exists = selected.some(function (s) {
                    return s.path === item.path;
                });
                if (exists) {
                    selected = selected.filter(function (s) {
                        return s.path !== item.path;
                    });
                } else {
                    selected.push(item);
                }
                syncLibraryInputs();
                renderSelected();
            }
            highlightGrid();
        }

        function renderGrid(list) {
            if (!grid) {
                return;
            }
            grid.innerHTML = '';
            if (!list.length) {
                grid.innerHTML = '<div class="admin-media-picker__empty text-muted small py-4 text-center">No matching images found.</div>';
                return;
            }
            list.forEach(function (item) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'admin-media-picker__tile';
                btn.dataset.path = item.path;
                btn.title = item.label || item.path;
                btn.innerHTML =
                    '<span class="admin-media-picker__tile-media">' +
                    '<img alt="" loading="lazy">' +
                    '<span class="admin-media-picker__tile-check"><i class="fas fa-check"></i></span>' +
                    '</span>' +
                    '<span class="admin-media-picker__tile-label"></span>';
                btn.querySelector('img').src = item.url;
                btn.querySelector('.admin-media-picker__tile-label').textContent = item.label || item.path;
                btn.addEventListener('click', function () {
                    toggleSelect(item);
                });
                grid.appendChild(btn);
            });
            highlightGrid();
        }

        function fetchLibrary(q) {
            if (!libraryUrl || !grid) {
                return;
            }
            grid.innerHTML = '<div class="admin-media-picker__empty text-muted small py-4 text-center">Loading images…</div>';
            const url = libraryUrl + (q ? ('?q=' + encodeURIComponent(q)) : '');
            fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
                .then(function (res) {
                    if (!res.ok) {
                        throw new Error('Failed to load library');
                    }
                    return res.json();
                })
                .then(function (payload) {
                    items = payload.data || [];
                    loaded = true;
                    renderGrid(items);
                })
                .catch(function () {
                    grid.innerHTML =
                        '<div class="admin-media-picker__empty text-danger small py-4 text-center">Could not load media library.</div>';
                });
        }

        if (search) {
            search.addEventListener(
                'input',
                debounce(function () {
                    fetchLibrary(search.value.trim());
                }, 280)
            );
        }
    }

    function initAll(root) {
        (root || document).querySelectorAll('[data-media-picker]').forEach(initPicker);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initAll(document);
    });
    document.addEventListener('turbo:load', function () {
        initAll(document);
    });
    document.addEventListener('turbo:frame-load', function (e) {
        initAll(e.target);
    });
    document.addEventListener('shown.bs.modal', function (e) {
        initAll(e.target);
    });

    window.initAdminMediaPickers = initAll;
})();
