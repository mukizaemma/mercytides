(function () {
    'use strict';

    var endpoint = document.querySelector('meta[name="form-submissions-url"]');
    if (!endpoint) {
        return;
    }

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function showAlert(wrap, message, type) {
        var box = wrap.querySelector('.public-form-delivery__alert');
        if (!box) {
            return;
        }
        box.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
        box.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');
        box.textContent = message;
    }

    function setLoading(buttons, loading) {
        buttons.forEach(function (btn) {
            btn.disabled = loading;
        });
    }

    function collectPayload(form) {
        var data = new FormData(form);
        var payload = {};
        data.forEach(function (value, key) {
            if (key === 'website' || key === 'started_at' || key === '_token') {
                return;
            }
            if (key.endsWith('[]')) {
                var base = key.slice(0, -2);
                if (!payload[base]) {
                    payload[base] = [];
                }
                payload[base].push(value);
                return;
            }
            payload[key] = value;
        });
        return payload;
    }

    function validateForm(form) {
        if (typeof form.reportValidity === 'function' && !form.reportValidity()) {
            return false;
        }
        return true;
    }

    function getRecaptchaResponse(wrap) {
        if (typeof window.grecaptcha === 'undefined') {
            return '';
        }
        var widget = wrap.querySelector('.g-recaptcha');
        if (!widget) {
            return '';
        }
        var response = window.grecaptcha.getResponse();
        return response || '';
    }

    document.querySelectorAll('form.js-public-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
        });

        var wrap = form.querySelector('.public-form-delivery');
        if (!wrap) {
            return;
        }

        var formType = form.getAttribute('data-form-type');
        var buttons = wrap.querySelectorAll('.public-form-delivery__btn');

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                if (!validateForm(form)) {
                    return;
                }

                var channel = button.getAttribute('data-channel');
                var body = {
                    form_type: formType,
                    channel: channel,
                    website: (form.querySelector('[name="website"]') || {}).value || '',
                    started_at: (form.querySelector('[name="started_at"]') || {}).value || '',
                    'g-recaptcha-response': getRecaptchaResponse(wrap),
                };

                var payload = collectPayload(form);
                Object.keys(payload).forEach(function (key) {
                    body[key] = payload[key];
                });

                setLoading(buttons, true);
                showAlert(wrap, 'Sending…', 'success');

                fetch(endpoint.getAttribute('content'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(body),
                })
                    .then(function (res) {
                        return res.json().then(function (data) {
                            return { ok: res.ok, data: data };
                        });
                    })
                    .then(function (result) {
                        setLoading(buttons, false);
                        if (!result.ok || !result.data.success) {
                            var msg = result.data.message || 'Could not send. Please check the form and try again.';
                            if (result.data.errors) {
                                var first = Object.values(result.data.errors)[0];
                                if (Array.isArray(first) && first[0]) {
                                    msg = first[0];
                                }
                            }
                            showAlert(wrap, msg, 'error');
                            if (typeof window.grecaptcha !== 'undefined') {
                                window.grecaptcha.reset();
                            }
                            return;
                        }

                        showAlert(wrap, result.data.message, 'success');
                        if (result.data.open_url) {
                            var openInNewTab = form.getAttribute('data-channel-new-tab') === 'true';
                            if (openInNewTab) {
                                var opened = window.open(result.data.open_url, '_blank', 'noopener,noreferrer');
                                if (!opened) {
                                    window.location.href = result.data.open_url;
                                }
                            } else {
                                window.location.href = result.data.open_url;
                            }
                        }
                    })
                    .catch(function () {
                        setLoading(buttons, false);
                        showAlert(wrap, 'Network error. Please try again.', 'error');
                    });
            });
        });
    });
})();
