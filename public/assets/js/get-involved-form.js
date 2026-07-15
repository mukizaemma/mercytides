(function () {
    'use strict';

    function syncCardStates(form) {
        form.querySelectorAll('.get-involved-way-card--selectable').forEach(function (card) {
            var input = card.querySelector('.js-get-involved-way');
            if (!input) {
                return;
            }
            card.classList.toggle('is-selected', input.checked);
        });
    }

    function clearWayError(form) {
        var error = form.querySelector('.js-get-involved-way-error');
        if (error && form.querySelector('.js-get-involved-way:checked')) {
            error.hidden = true;
        }
    }

    function showWayError(form) {
        var error = form.querySelector('.js-get-involved-way-error');
        if (error) {
            error.hidden = false;
        }
        var firstCard = form.querySelector('.get-involved-way-card--selectable');
        if (firstCard) {
            firstCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function validateWays(form) {
        if (!form.querySelector('.js-get-involved-way:checked')) {
            showWayError(form);
            return false;
        }

        var message = form.querySelector('textarea[name="message"]');
        if (message && !String(message.value || '').trim()) {
            message.setCustomValidity('Please describe your commitment.');
            message.reportValidity();
            return false;
        }
        if (message) {
            message.setCustomValidity('');
        }

        return true;
    }

    function bindForm(form) {
        if (form.dataset.getInvolvedBound === 'true') {
            syncCardStates(form);
            return;
        }
        form.dataset.getInvolvedBound = 'true';

        form.querySelectorAll('.js-get-involved-way').forEach(function (input) {
            input.addEventListener('change', function () {
                syncCardStates(form);
                clearWayError(form);
            });
        });

        form.addEventListener('click', function (event) {
            if (!event.target.closest('.public-form-delivery__btn')) {
                return;
            }
            if (!validateWays(form)) {
                event.preventDefault();
                event.stopPropagation();
            }
        }, true);

        syncCardStates(form);
    }

    function init(root) {
        (root || document).querySelectorAll('.js-get-involved-form').forEach(bindForm);
    }

    document.addEventListener('DOMContentLoaded', function () {
        init(document);
    });
    document.addEventListener('turbo:load', function () {
        init(document);
    });
})();
