(function () {
    'use strict';

    function setSelectedFocus(form, focusKey, label, text, icon) {
        var valueInput = form.querySelector('.js-sponsor-focus-value');
        var panel = form.querySelector('.js-sponsor-selected-focus');
        var title = form.querySelector('.js-sponsor-modal-title');
        var textEl = form.querySelector('.js-sponsor-selected-text');
        var iconEl = form.querySelector('.js-sponsor-selected-icon');
        var focusError = form.querySelector('.js-sponsor-focus-error');

        if (valueInput) {
            valueInput.value = focusKey || '';
        }

        if (panel) {
            panel.classList.toggle('d-none', !focusKey);
        }

        if (title && label) {
            title.textContent = label;
        }

        if (textEl) {
            textEl.textContent = text || '';
        }

        if (iconEl) {
            iconEl.className = 'fas js-sponsor-selected-icon ' + (icon || 'fa-heart');
        }

        if (focusError) {
            focusError.hidden = !!focusKey;
        }
    }

    function validateForm(form) {
        var valueInput = form.querySelector('.js-sponsor-focus-value');
        if (!valueInput || !String(valueInput.value || '').trim()) {
            var focusError = form.querySelector('.js-sponsor-focus-error');
            if (focusError) {
                focusError.hidden = false;
            }
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
        if (form.dataset.sponsorInquiryBound === 'true') {
            return;
        }
        form.dataset.sponsorInquiryBound = 'true';

        form.addEventListener('click', function (event) {
            if (!event.target.closest('.public-form-delivery__btn')) {
                return;
            }
            if (!validateForm(form)) {
                event.preventDefault();
                event.stopPropagation();
            }
        }, true);
    }

    function bindCommitmentTriggers() {
        var modalEl = document.getElementById('sponsorCommitmentModal');
        if (!modalEl) {
            return;
        }

        document.querySelectorAll('.js-open-sponsor-commitment').forEach(function (btn) {
            if (btn.dataset.sponsorTriggerBound === 'true') {
                return;
            }
            btn.dataset.sponsorTriggerBound = 'true';

            btn.addEventListener('click', function () {
                var form = modalEl.querySelector('.js-sponsor-inquiry-form');
                if (!form) {
                    return;
                }
                setSelectedFocus(
                    form,
                    btn.getAttribute('data-support-focus') || '',
                    btn.getAttribute('data-support-label') || '',
                    btn.getAttribute('data-support-text') || '',
                    btn.getAttribute('data-support-icon') || 'fa-heart'
                );
            });
        });

        modalEl.addEventListener('shown.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            var form = modalEl.querySelector('.js-sponsor-inquiry-form');
            if (!form) {
                return;
            }
            if (trigger && trigger.getAttribute) {
                setSelectedFocus(
                    form,
                    trigger.getAttribute('data-support-focus') || '',
                    trigger.getAttribute('data-support-label') || '',
                    trigger.getAttribute('data-support-text') || '',
                    trigger.getAttribute('data-support-icon') || 'fa-heart'
                );
            }
            var firstEmpty = form.querySelector('input[name="full_name"]');
            if (firstEmpty && !firstEmpty.value) {
                firstEmpty.focus();
            }
        });
    }

    function init(root) {
        (root || document).querySelectorAll('.js-sponsor-inquiry-form').forEach(bindForm);
        bindCommitmentTriggers();
    }

    document.addEventListener('DOMContentLoaded', function () {
        init(document);
    });
    document.addEventListener('turbo:load', function () {
        init(document);
    });
})();
