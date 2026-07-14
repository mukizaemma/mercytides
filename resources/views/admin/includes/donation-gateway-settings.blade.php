@php
    $gatewayEnabled = !empty($data->donation_gateway_enabled);
@endphp

<div class="card border border-warning mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
            <div>
                <h5 class="mb-1">Online card checkout (Stripe)</h5>
                <p class="text-muted small mb-0">Enable to show the card payment area on the donate page. Processing is not live yet — donors see a preview form and can still notify you via WhatsApp or Email.</p>
            </div>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" role="switch" id="donation_gateway_enabled"
                    name="donation_gateway_enabled" value="1" {{ $gatewayEnabled ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="donation_gateway_enabled">Show on donate page</label>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Stripe publishable key</label>
                <input type="text" class="form-control font-monospace small" name="donation_stripe_publishable_key"
                    value="{{ $data->donation_stripe_publishable_key ?? '' }}" placeholder="pk_live_… or pk_test_…" autocomplete="off">
            </div>
            <div class="col-md-6">
                <label class="form-label">Stripe secret key</label>
                <input type="password" class="form-control font-monospace small" name="donation_stripe_secret_key"
                    value="{{ $data->donation_stripe_secret_key ?? '' }}" placeholder="sk_live_… (stored for future use)" autocomplete="new-password">
                <small class="text-muted">Not used until checkout is implemented. Leave blank to keep existing secret.</small>
            </div>
            <div class="col-md-4">
                <label class="form-label">Default currency</label>
                <input type="text" class="form-control" name="donation_default_currency" maxlength="8"
                    value="{{ $data->donation_default_currency ?? 'USD' }}" placeholder="USD">
            </div>
            <div class="col-12">
                <label class="form-label">Message on donate page (optional)</label>
                <textarea class="form-control" rows="3" name="donation_gateway_notice" data-editor="rich" placeholder="Shown above the card form when checkout preview is visible.">{!! $data->donation_gateway_notice ?? '' !!}</textarea>
            </div>
        </div>
    </div>
</div>
