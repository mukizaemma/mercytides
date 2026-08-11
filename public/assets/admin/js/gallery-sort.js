/**
 * Drag-to-reorder gallery items shown on the public Gallery page.
 */
(function () {
    'use strict';

    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function bindSortable(tbody) {
        if (!tbody || tbody.dataset.sortBound === '1') {
            return;
        }
        tbody.dataset.sortBound = '1';

        let dragRow = null;

        tbody.addEventListener('dragstart', function (event) {
            if (!event.target.closest('.gallery-sort-handle')) {
                event.preventDefault();
                return;
            }
            const row = event.target.closest('tr[data-id]');
            if (!row) {
                return;
            }
            dragRow = row;
            row.classList.add('is-dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', row.dataset.id);
        });

        tbody.addEventListener('dragend', function () {
            if (dragRow) {
                dragRow.classList.remove('is-dragging');
            }
            dragRow = null;
            tbody.querySelectorAll('.is-drop-target').forEach(function (el) {
                el.classList.remove('is-drop-target');
            });
        });

        tbody.addEventListener('dragover', function (event) {
            event.preventDefault();
            const row = event.target.closest('tr[data-id]');
            if (!row || row === dragRow) {
                return;
            }
            tbody.querySelectorAll('.is-drop-target').forEach(function (el) {
                el.classList.remove('is-drop-target');
            });
            row.classList.add('is-drop-target');
            const rect = row.getBoundingClientRect();
            const after = (event.clientY - rect.top) > (rect.height / 2);
            if (after) {
                row.after(dragRow);
            } else {
                row.before(dragRow);
            }
        });

        tbody.addEventListener('drop', function (event) {
            event.preventDefault();
            saveOrder(tbody);
        });

        tbody.querySelectorAll('tr[data-id]').forEach(function (row) {
            row.setAttribute('draggable', 'true');
        });
    }

    function saveOrder(tbody) {
        const url = tbody.getAttribute('data-reorder-url');
        if (!url) {
            return;
        }
        const ids = Array.from(tbody.querySelectorAll('tr[data-id]')).map(function (row) {
            return parseInt(row.dataset.id, 10);
        }).filter(Boolean);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ ids: ids }),
        }).catch(function () {
            // Keep the new DOM order even if the save fails; reload will revert.
        });
    }

    function init() {
        document.querySelectorAll('[data-gallery-sortable]').forEach(bindSortable);
    }

    document.addEventListener('DOMContentLoaded', init);
    document.addEventListener('turbo:load', init);
})();
