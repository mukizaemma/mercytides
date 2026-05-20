<input type="hidden" name="started_at" value="{{ now()->timestamp }}">
<div class="site-hp-field" aria-hidden="true">
    <label for="website_{{ $formType ?? 'form' }}">Website</label>
    <input type="text" name="website" id="website_{{ $formType ?? 'form' }}" tabindex="-1" autocomplete="off">
</div>

<div class="col-12 public-form-delivery pt-1">
    @if(!empty($recaptchaSiteKey))
        <div class="mb-3">
            <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
        </div>
    @endif

    {{-- <p class="public-form-delivery__hint text-muted small mb-3">
        Review your details, complete the check below if shown, then send via <strong>WhatsApp</strong> or <strong>Email</strong>.
        We save your submission only when you open your chosen app — our team receives it on the organisation phone and email in Settings.
    </p> --}}
    <div class="d-flex flex-wrap gap-2 public-form-delivery__actions">
        <button type="button" class="btn btn-success btn-lg public-form-delivery__btn" data-channel="whatsapp">
            <i class="fab fa-whatsapp me-1" aria-hidden="true"></i> Send via WhatsApp
        </button>
        <button type="button" class="btn btn-dark btn-lg public-form-delivery__btn" data-channel="email">
            <i class="far fa-envelope me-1" aria-hidden="true"></i> Send via Email
        </button>
    </div>
    <div class="public-form-delivery__alert alert mt-3 d-none" role="status"></div>
</div>
