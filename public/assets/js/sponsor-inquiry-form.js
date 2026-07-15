(function () {
    'use strict';

    function syncSelectableCards(form, cardSelector, inputSelector) {
        form.querySelectorAll(cardSelector).forEach(function (card) {
            var input = card.querySelector(inputSelector);
            if (input) {
                card.classList.toggle('is-selected', input.checked);
            }
        });
    }

    function syncPreferenceCards(form) {
        syncSelectableCards(form, '.sponsor-preference-card', '.js-sponsor-preference');
    }

    function syncFocusCards(form) {
        syncSelectableCards(form, '.sponsor-focus-card', '.js-sponsor-focus');
    }

    function syncAmountPanel(form) {
        var checked = form.querySelector('.js-sponsor-preference:checked');
        var panel = form.querySelector('.js-sponsor-amount-panel');
        if (!panel) {
            return;
        }
        var show = checked && ['monthly', 'one_time'].indexOf(checked.value) !== -1;
        panel.hidden = !show;
    }

    function setSupportFocus(form, focusKey) {
        if (!focusKey) {
            return;
        }
        var input = form.querySelector('.js-sponsor-focus[value="' + focusKey + '"]');
        if (!input) {
            return;
        }
        input.checked = true;
        syncFocusCards(form);
        var focusError = form.querySelector('.js-sponsor-focus-error');
        if (focusError) {
            focusError.hidden = true;
        }
    }

    function bindAmountChips(form) {
        var panel = form.querySelector('.js-sponsor-amount-panel');
        if (!panel) {
            return;
        }
        var amountInput = panel.querySelector('.js-sponsor-amount-input');
        var customWrap = panel.querySelector('.js-sponsor-custom-wrap');
        var chips = panel.querySelectorAll('.js-sponsor-amount-chip');

        function selectChip(chip) {
            chips.forEach(function (c) {
                c.classList.toggle('is-active', c === chip);
            });
            var amount = chip.getAttribute('data-amount');
            if (amount === 'custom') {
                customWrap.hidden = false;
                amountInput.focus();
            } else {
                customWrap.hidden = true;
                amountInput.value = amount;
            }
        }

        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                selectChip(chip);
            });
        });
    }

    function validateForm(form) {
        if (!form.querySelector('.js-sponsor-focus:checked')) {
            var focusError = form.querySelector('.js-sponsor-focus-error');
            if (focusError) {
                focusError.hidden = false;
            }
            return false;
        }

        if (!form.querySelector('.js-sponsor-preference:checked')) {
            var prefError = form.querySelector('.js-sponsor-preference-error');
            if (prefError) {
                prefError.hidden = false;
            }
            return false;
        }

        return true;
    }

    function bindForm(form) {
        if (form.dataset.sponsorInquiryBound === 'true') {
            syncPreferenceCards(form);
            syncFocusCards(form);
            syncAmountPanel(form);
            return;
        }
        form.dataset.sponsorInquiryBound = 'true';

        form.querySelectorAll('.js-sponsor-preference').forEach(function (input) {
            input.addEventListener('change', function () {
                syncPreferenceCards(form);
                syncAmountPanel(form);
                var prefError = form.querySelector('.js-sponsor-preference-error');
                if (prefError) {
                    prefError.hidden = true;
                }
            });
        });

        form.querySelectorAll('.js-sponsor-focus').forEach(function (input) {
            input.addEventListener('change', function () {
                syncFocusCards(form);
                var focusError = form.querySelector('.js-sponsor-focus-error');
                if (focusError) {
                    focusError.hidden = true;
                }
            });
        });

        bindAmountChips(form);
        syncPreferenceCards(form);
        syncFocusCards(form);
        syncAmountPanel(form);

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
                var focus = btn.getAttribute('data-support-focus') || '';
                if (focus) {
                    setSupportFocus(form, focus);
                }
            });
        });

        modalEl.addEventListener('shown.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            var form = modalEl.querySelector('.js-sponsor-inquiry-form');
            if (!form) {
                return;
            }
            if (trigger && trigger.getAttribute) {
                var focus = trigger.getAttribute('data-support-focus') || '';
                if (focus) {
                    setSupportFocus(form, focus);
                }
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
