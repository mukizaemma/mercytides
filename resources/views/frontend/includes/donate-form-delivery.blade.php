<div class="col-12 donate-notify-block" data-donate-notify-block>
    <hr class="my-2">
    <h3 class="h6 text-uppercase letter-spacing-sm text-muted mb-2 donate-notify-block__title">Notify our team</h3>
    {{-- <p class="public-form-delivery__hint text-muted small mb-3 donate-notify-block__hint">
        Review your details, complete the check below if shown, then send via <strong>WhatsApp</strong> or <strong>Email</strong>.
        We save your submission only when you open your chosen app.
    </p> --}}
    @include('frontend.includes.public-form-delivery', ['formType' => 'donate'])
</div>
