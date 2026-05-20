(function () {
    'use strict';

    var NOTIFY_HINTS = {
        manual: 'After you send your gift, notify our team via <strong>WhatsApp</strong> or <strong>Email</strong> so we can confirm receipt.',
        external_link: 'Already paid or paying later? Send your pledge via <strong>WhatsApp</strong> or <strong>Email</strong>.',
        gateway_ui: 'Until card checkout is live, notify our team via <strong>WhatsApp</strong> or <strong>Email</strong> with your pledge.',
        notify_only: 'Send your pledge via <strong>WhatsApp</strong> or <strong>Email</strong> and we will follow up with payment details.',
    };

    var NOTIFY_TITLES = {
        manual: 'Notify us you have given',
        external_link: 'Notify us about your gift',
        gateway_ui: 'Notify our team',
        notify_only: 'Send your pledge',
    };

    function getConfig() {
        var el = document.getElementById('donate-payment-config');
        if (!el) {
            return null;
        }
        try {
            return JSON.parse(el.textContent);
        } catch (e) {
            return null;
        }
    }

    function findMethod(config, id) {
        if (!config || !config.methods) {
            return null;
        }
        return config.methods.find(function (m) {
            return m.id === id;
        }) || null;
    }

    function initDonateForm(form) {
        if (form.getAttribute('data-donate-flow-ready') === '1') {
            return;
        }
        form.setAttribute('data-donate-flow-ready', '1');

        var config = getConfig();
        var panelsHost = form.querySelector('[data-donate-method-panels]');
        var completionStep = form.querySelector('[data-donate-completion-step]');
        var notifyBlock = form.querySelector('[data-donate-notify-block]');
        var notifyHint = form.querySelector('.donate-notify-block__hint');
        var notifyTitle = form.querySelector('.donate-notify-block__title');
        var radios = form.querySelectorAll('input[name="payment_method"]');

        function getPanels() {
            if (!panelsHost) {
                return [];
            }
            return panelsHost.querySelectorAll('[data-method-panel]');
        }

        function getSelectedId() {
            var checked = form.querySelector('input[name="payment_method"]:checked');
            return checked ? checked.value : '';
        }

        function getCompletion(id) {
            var method = config ? findMethod(config, id) : null;
            if (method) {
                return method.completion;
            }
            var radio = form.querySelector('input[name="payment_method"][value="' + id + '"]');
            return radio ? radio.getAttribute('data-completion') || 'manual' : 'manual';
        }

        function showPanel(id) {
            getPanels().forEach(function (panel) {
                var active = panel.getAttribute('data-method-panel') === id;
                panel.classList.toggle('is-active', active);
                panel.hidden = !active;
            });
        }

        function updateUI() {
            var id = getSelectedId();
            if (!id) {
                if (completionStep) {
                    completionStep.hidden = true;
                }
                if (notifyBlock) {
                    notifyBlock.hidden = true;
                }
                getPanels().forEach(function (panel) {
                    panel.classList.remove('is-active');
                    panel.hidden = true;
                });
                return;
            }

            var completion = getCompletion(id);

            if (completionStep) {
                completionStep.hidden = false;
            }
            showPanel(id);

            if (notifyBlock) {
                notifyBlock.hidden = false;
            }
            if (notifyHint && NOTIFY_HINTS[completion]) {
                notifyHint.innerHTML = NOTIFY_HINTS[completion];
            }
            if (notifyTitle && NOTIFY_TITLES[completion]) {
                notifyTitle.textContent = NOTIFY_TITLES[completion];
            }

            form.setAttribute('data-donate-completion', completion);
        }

        radios.forEach(function (radio) {
            radio.addEventListener('change', updateUI);
            radio.addEventListener('click', updateUI);
        });

        form.addEventListener('click', function (event) {
            var tile = event.target.closest('.donate-payment-method-choice--tile');
            if (!tile) {
                return;
            }
            var input = tile.querySelector('input[name="payment_method"]');
            if (input) {
                input.checked = true;
                updateUI();
            }
        });

        form.querySelectorAll('.donate-copy-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var value = btn.getAttribute('data-copy-target') || '';
                if (!value || !navigator.clipboard || !navigator.clipboard.writeText) {
                    return;
                }
                navigator.clipboard.writeText(value).then(function () {
                    var label = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check me-1" aria-hidden="true"></i> Copied';
                    setTimeout(function () {
                        btn.innerHTML = label;
                    }, 2000);
                });
            });
        });

        updateUI();
    }

    function boot() {
        document.querySelectorAll('form.js-donate-form').forEach(initDonateForm);
    }

    document.addEventListener('DOMContentLoaded', boot);
    document.addEventListener('turbo:load', boot);
    document.addEventListener('turbo:before-render', function () {
        document.querySelectorAll('form.js-donate-form[data-donate-flow-ready="1"]').forEach(function (form) {
            form.removeAttribute('data-donate-flow-ready');
        });
    });
})();
