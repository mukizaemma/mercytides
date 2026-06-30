(function () {
    'use strict';

    function syncPreferenceCards(form) {
        form.querySelectorAll('.sponsor-preference-card').forEach(function (card) {
            var input = card.querySelector('.js-sponsor-preference');
            if (input) {
                card.classList.toggle('is-selected', input.checked);
            }
        });
    }

    function syncAmountPanel(form) {
        var checked = form.querySelector('.js-sponsor-preference:checked');
        var panel = form.querySelector('.js-sponsor-amount-panel');
        if (!panel) {
            return;
        }
        var show = checked && ['monthly', 'one_time'].indexOf(checked.value) !== -1;
        panel.hidden = !show;
        if (!show) {
            var amountInput = form.querySelector('.js-sponsor-amount-input');
            if (amountInput) {
                amountInput.removeAttribute('required');
            }
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
                amountInput.setAttribute('required', 'required');
                amountInput.focus();
            } else {
                customWrap.hidden = true;
                amountInput.value = amount;
                amountInput.removeAttribute('required');
            }
            hideAmountError(form);
        }

        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                selectChip(chip);
            });
        });
    }

    function hideAmountError(form) {
        var error = form.querySelector('.js-sponsor-amount-error');
        if (error) {
            error.hidden = true;
        }
    }

    function validateForm(form) {
        if (!form.querySelector('.js-sponsor-preference:checked')) {
            var prefError = form.querySelector('.js-sponsor-preference-error');
            if (prefError) {
                prefError.hidden = false;
            }
            return false;
        }

        var preference = form.querySelector('.js-sponsor-preference:checked').value;
        if (['monthly', 'one_time'].indexOf(preference) !== -1) {
            var amountInput = form.querySelector('.js-sponsor-amount-input');
            if (!amountInput || !amountInput.value || parseFloat(amountInput.value) < 1) {
                var amountError = form.querySelector('.js-sponsor-amount-error');
                if (amountError) {
                    amountError.hidden = false;
                }
                var panel = form.querySelector('.js-sponsor-amount-panel');
                if (panel) {
                    panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
        }

        return true;
    }

    function bindForm(form) {
        if (form.dataset.sponsorInquiryBound === 'true') {
            syncPreferenceCards(form);
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

        bindAmountChips(form);
        syncPreferenceCards(form);
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

    function init(root) {
        (root || document).querySelectorAll('.js-sponsor-inquiry-form').forEach(bindForm);
    }

    document.addEventListener('DOMContentLoaded', function () {
        init(document);
    });
    document.addEventListener('turbo:load', function () {
        init(document);
    });
})();
