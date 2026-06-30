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

    function syncPanels(form) {
        var checked = new Set();
        form.querySelectorAll('.js-get-involved-way:checked').forEach(function (input) {
            checked.add(input.getAttribute('data-way'));
        });

        form.querySelectorAll('.js-get-involved-panel').forEach(function (panel) {
            var key = panel.getAttribute('data-panel');
            var show = checked.has(key);
            panel.hidden = !show;

            panel.querySelectorAll('input, textarea, select').forEach(function (field) {
                if (!show) {
                    field.removeAttribute('required');
                } else if (key !== 'visit_mothers' && field.getAttribute('name') !== 'message') {
                    if (key === 'donation' && field.classList.contains('js-donation-amount-input')) {
                        var customWrap = panel.querySelector('.js-donation-custom-wrap');
                        var customVisible = customWrap && !customWrap.hidden;
                        if (customVisible) {
                            field.setAttribute('required', 'required');
                        } else {
                            field.removeAttribute('required');
                        }
                    }
                    if (key === 'volunteer' && field.name === 'volunteer_experience') {
                        field.setAttribute('required', 'required');
                    }
                    if (key === 'partner' && field.name === 'partnership_details') {
                        field.setAttribute('required', 'required');
                    }
                }
            });
        });

        syncCardStates(form);
        clearWayError(form);
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

    function showDonationError(form, show) {
        var error = form.querySelector('.js-donation-amount-error');
        if (error) {
            error.hidden = !show;
        }
    }

    function clearDonationError(form) {
        showDonationError(form, false);
    }

    function validateWays(form) {
        if (!form.querySelector('.js-get-involved-way:checked')) {
            showWayError(form);
            showDonationError(form, false);
            return false;
        }

        if (form.querySelector('.js-get-involved-way[data-way="donation"]:checked')) {
            var amountInput = form.querySelector('.js-donation-amount-input');
            if (!amountInput || !amountInput.value || parseFloat(amountInput.value) < 1) {
                showDonationError(form, true);
                var donationPanel = form.querySelector('.js-get-involved-panel[data-panel="donation"]');
                if (donationPanel) {
                    donationPanel.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
        }

        showDonationError(form, false);
        return true;
    }

    function bindDonationChips(form) {
        var panel = form.querySelector('.js-get-involved-panel[data-panel="donation"]');
        if (!panel) {
            return;
        }

        var amountInput = panel.querySelector('.js-donation-amount-input');
        var customWrap = panel.querySelector('.js-donation-custom-wrap');
        var chips = panel.querySelectorAll('.js-donation-chip');

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

            clearDonationError(form);
        }

        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                selectChip(chip);
            });
        });

        var preset = amountInput.value;
        if (preset && ['25', '50', '100'].indexOf(String(preset)) !== -1) {
            chips.forEach(function (chip) {
                if (chip.getAttribute('data-amount') === String(preset)) {
                    selectChip(chip);
                }
            });
        } else if (preset) {
            chips.forEach(function (chip) {
                if (chip.getAttribute('data-amount') === 'custom') {
                    selectChip(chip);
                }
            });
        }
    }

    function bindDeliveryValidation(form) {
        form.addEventListener('click', function (event) {
            if (!event.target.closest('.public-form-delivery__btn')) {
                return;
            }
            if (!validateWays(form)) {
                event.preventDefault();
                event.stopPropagation();
            }
        }, true);
    }

    function bindIntroToggle() {
        var toggle = document.querySelector('.get-involved-page__read-more');
        var target = document.getElementById('getInvolvedIntroMore');
        if (!toggle || !target) {
            return;
        }
        target.addEventListener('show.bs.collapse', function () {
            toggle.innerHTML = 'Show less <i class="fas fa-chevron-up ms-1" aria-hidden="true"></i>';
        });
        target.addEventListener('hide.bs.collapse', function () {
            toggle.innerHTML = 'Read full story <i class="fas fa-chevron-down ms-1" aria-hidden="true"></i>';
        });
    }

    function bindSmoothScroll() {
        document.querySelectorAll('.js-smooth-scroll[href^="#"]').forEach(function (link) {
            link.addEventListener('click', function (event) {
                var id = link.getAttribute('href');
                if (!id || id === '#') {
                    return;
                }
                var target = document.querySelector(id);
                if (!target) {
                    return;
                }
                event.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    }

    function scrollToFormIfPreselected(form) {
        var checked = form.querySelector('.js-get-involved-way:checked');
        if (!checked) {
            return;
        }
        var section = form.closest('[id]');
        var hasPreselect = window.location.search.indexOf('way=') !== -1;
        var hasHash = window.location.hash === '#get-involved-form' || window.location.hash === '#contact-form';
        if (!hasPreselect && !hasHash) {
            return;
        }
        window.setTimeout(function () {
            var target = section || document.getElementById('get-involved-form') || document.getElementById('contact-form');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 120);
    }

    function bindSingleSelect(form) {
        if (form.dataset.singleWay !== 'true') {
            return;
        }

        form.querySelectorAll('.js-get-involved-way').forEach(function (input) {
            input.addEventListener('change', function () {
                if (!input.checked) {
                    return;
                }
                form.querySelectorAll('.js-get-involved-way').forEach(function (other) {
                    if (other !== input) {
                        other.checked = false;
                    }
                });
                syncPanels(form);
            });
        });
    }

    function bindForm(form) {
        if (form.dataset.getInvolvedBound === 'true') {
            syncPanels(form);
            return;
        }
        form.dataset.getInvolvedBound = 'true';

        var isSingleSelect = form.dataset.singleWay === 'true';

        form.querySelectorAll('.js-get-involved-way').forEach(function (input) {
            if (!isSingleSelect) {
                input.addEventListener('change', function () {
                    syncPanels(form);
                });
            }
        });

        if (isSingleSelect) {
            bindSingleSelect(form);
        }

        bindDonationChips(form);
        bindDeliveryValidation(form);
        syncPanels(form);
        scrollToFormIfPreselected(form);
    }

    function initGetInvolvedForms(root) {
        (root || document).querySelectorAll('.js-get-involved-form').forEach(bindForm);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initGetInvolvedForms(document);
        bindIntroToggle();
        bindSmoothScroll();
    });
    document.addEventListener('turbo:load', function () {
        initGetInvolvedForms(document);
        bindIntroToggle();
        bindSmoothScroll();
    });
})();
