@php
    $donationMethodsAll = \App\Support\DonationPaymentMethods::all($data);
@endphp

<p class="text-muted small mb-4">Configure payment options shown on the donate page. Donors pick a method first, then see the relevant details. MoMo and bank gifts are completed manually; card uses the Stripe section above or a payment link; WhatsApp/Email is used to notify your team when no online checkout is available.</p>

<div class="row g-4">
    @foreach($donationMethodsAll as $method)
        <div class="col-12">
            <div class="card border">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch"
                                id="donation_method_{{ $method['id'] }}_enabled"
                                name="donation_methods[{{ $method['id'] }}][enabled]" value="1"
                                {{ !empty($method['enabled']) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="donation_method_{{ $method['id'] }}_enabled">
                                Enable on donate page
                            </label>
                        </div>
                        <input type="hidden" name="donation_methods[{{ $method['id'] }}][id]" value="{{ $method['id'] }}">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Display label</label>
                            <input type="text" class="form-control" name="donation_methods[{{ $method['id'] }}][label]" value="{{ $method['label'] }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile money / account number</label>
                            <input type="text" class="form-control" name="donation_methods[{{ $method['id'] }}][number]" value="{{ $method['number'] }}" placeholder="e.g. 077…">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Account name</label>
                            <input type="text" class="form-control" name="donation_methods[{{ $method['id'] }}][account_name]" value="{{ $method['account_name'] }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bank name</label>
                            <input type="text" class="form-control" name="donation_methods[{{ $method['id'] }}][bank_name]" value="{{ $method['bank_name'] }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Branch</label>
                            <input type="text" class="form-control" name="donation_methods[{{ $method['id'] }}][branch]" value="{{ $method['branch'] }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">SWIFT / sort code</label>
                            <input type="text" class="form-control" name="donation_methods[{{ $method['id'] }}][swift]" value="{{ $method['swift'] }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Online payment URL</label>
                            <input type="url" class="form-control" name="donation_methods[{{ $method['id'] }}][payment_url]" value="{{ $method['payment_url'] }}" placeholder="https://">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Instructions for donors</label>
                            <textarea class="form-control" rows="3" name="donation_methods[{{ $method['id'] }}][instructions]" data-editor="rich">{!! $method['instructions'] !!}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
