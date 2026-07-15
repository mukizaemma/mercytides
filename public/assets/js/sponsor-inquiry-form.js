(function () {
    'use strict';

    var pendingFocus = null;

    function readTriggerFocus(trigger) {
        if (!trigger || !trigger.getAttribute) {
            return null;
        }

        return {
            key: trigger.getAttribute('data-support-focus') || '',
            label: trigger.getAttribute('data-support-label') || '',
            text: trigger.getAttribute('data-support-text') || '',
            why: trigger.getAttribute('data-support-why') || '',
            icon: trigger.getAttribute('data-support-icon') || 'fa-heart'
        };
    }

    function setSelectedFocus(form, focus) {
        if (!form || !focus) {
            return;
        }

        var valueInput = form.querySelector('.js-sponsor-focus-value');
        var labelEl = form.querySelector('.js-sponsor-selected-label');
        var whyEl = form.querySelector('.js-sponsor-why-message');
        var focusError = form.querySelector('.js-sponsor-focus-error');
        var key = String(focus.key || '').trim();
        var label = String(focus.label || '').trim();
        var why = String(focus.why || focus.text || '').trim();

        if (valueInput) {
            valueInput.value = key;
        }

        if (labelEl) {
            labelEl.textContent = label;
            labelEl.classList.toggle('d-none', !label);
        }

        if (whyEl && why) {
            whyEl.textContent = why;
        }

        if (focusError) {
            focusError.hidden = !!key;
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

    function bindTriggerButton(btn, modalEl) {
        if (btn.dataset.sponsorTriggerBound === 'true') {
            return;
        }
        btn.dataset.sponsorTriggerBound = 'true';

        btn.addEventListener('click', function () {
            pendingFocus = readTriggerFocus(btn);
            var form = modalEl.querySelector('.js-sponsor-inquiry-form');
            if (form && pendingFocus) {
                setSelectedFocus(form, pendingFocus);
            }
        });
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
            bindTriggerButton(btn, modalEl);
        });

        if (modalEl.dataset.sponsorModalBound === 'true') {
            return;
        }
        modalEl.dataset.sponsorModalBound = 'true';

        ['show.bs.modal', 'shown.bs.modal'].forEach(function (eventName) {
            modalEl.addEventListener(eventName, function (event) {
                var form = modalEl.querySelector('.js-sponsor-inquiry-form');
                if (!form) {
                    return;
                }

                var focus = pendingFocus || readTriggerFocus(event.relatedTarget);
                if (focus && focus.key) {
                    setSelectedFocus(form, focus);
                }

                if (eventName === 'shown.bs.modal') {
                    var firstEmpty = form.querySelector('input[name="full_name"]');
                    if (firstEmpty && !firstEmpty.value) {
                        firstEmpty.focus();
                    }
                }
            });
        });

        modalEl.addEventListener('hidden.bs.modal', function () {
            pendingFocus = null;
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
