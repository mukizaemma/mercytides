@php
    use App\Support\DonationPaymentMethods as DPM;
    $setting = $setting ?? \App\Models\Setting::firstOrEmpty();
    $gateway = DPM::gatewayConfig($setting);
@endphp

<div class="donate-completion-step col-12" data-donate-completion-step>
    <h3 class="h6 text-uppercase letter-spacing-sm text-muted mb-3 donate-completion-step__heading">Complete your gift</h3>

    <div class="donate-method-panels-host" data-donate-method-panels>
        @foreach($donationPaymentMethods as $method)
            @php
                $completion = DPM::completionMode($method, $setting);
                $channel = DPM::channel($method);
                $isFirst = $loop->first;
            @endphp
            <div class="donate-method-panel{{ $isFirst ? ' is-active' : '' }}" data-method-panel="{{ $method['id'] }}" data-channel="{{ $channel }}" data-completion="{{ $completion }}" @if(!$isFirst) hidden @endif>
                @if($completion === DPM::COMPLETION_MANUAL)
                    <div class="donate-method-panel__details">
                        @if($channel === DPM::CHANNEL_MOBILE_MONEY)
                            @if(!empty($method['number']))
                                <div class="donate-momo-highlight mb-3">
                                    <span class="donate-momo-highlight__label text-muted small d-block mb-1">Send to this number</span>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <strong class="donate-momo-highlight__number fs-4">{{ $method['number'] }}</strong>
                                        <button type="button" class="btn btn-sm btn-outline-dark donate-copy-btn" data-copy-target="{{ $method['number'] }}" aria-label="Copy number">
                                            <i class="far fa-copy me-1" aria-hidden="true"></i> Copy
                                        </button>
                                    </div>
                                    @if(!empty($method['account_name']))
                                        <span class="small text-muted d-block mt-1">Account name: {{ $method['account_name'] }}</span>
                                    @endif
                                </div>
                            @endif
                            @if(!empty($method['instructions']))
                                <p class="small text-muted mb-3">{{ $method['instructions'] }}</p>
                            @endif
                        @elseif($channel === DPM::CHANNEL_BANK)
                            <dl class="donate-bank-details mb-3">
                                @if(!empty($method['bank_name']))
                                    <div class="donate-bank-details__row"><dt>Bank</dt><dd>{{ $method['bank_name'] }}</dd></div>
                                @endif
                                @if(!empty($method['branch']))
                                    <div class="donate-bank-details__row"><dt>Branch</dt><dd>{{ $method['branch'] }}</dd></div>
                                @endif
                                @if(!empty($method['number']))
                                    <div class="donate-bank-details__row"><dt>Account number</dt><dd>{{ $method['number'] }}</dd></div>
                                @endif
                                @if(!empty($method['account_name']))
                                    <div class="donate-bank-details__row"><dt>Account name</dt><dd>{{ $method['account_name'] }}</dd></div>
                                @endif
                                @if(!empty($method['swift']))
                                    <div class="donate-bank-details__row"><dt>SWIFT</dt><dd>{{ $method['swift'] }}</dd></div>
                                @endif
                            </dl>
                            @if(!empty($method['instructions']))
                                <p class="small text-muted mb-3">{{ $method['instructions'] }}</p>
                            @endif
                        @endif
                        <p class="small text-muted mb-0 donate-method-panel__hint donate-method-panel__hint--manual">
                            After you send your gift, notify our team below so we can confirm and thank you.
                        </p>
                    </div>
                @elseif($completion === DPM::COMPLETION_EXTERNAL_LINK)
                    <div class="donate-method-panel__details mb-3">
                        @if(!empty($method['instructions']))
                            <p class="mb-3">{{ $method['instructions'] }}</p>
                        @endif
                        <a href="{{ $method['payment_url'] }}" class="btn btn-lg tp-btn donate-pay-now-btn" target="_blank" rel="noopener noreferrer">
                            <i class="fas fa-external-link-alt me-2" aria-hidden="true"></i> Pay now securely
                        </a>
                    </div>
                    <p class="small text-muted mb-0 donate-method-panel__hint donate-method-panel__hint--link">
                        Already paid or need help? Notify our team below with your pledge details.
                    </p>
                @elseif($completion === DPM::COMPLETION_GATEWAY_UI)
                    <div class="alert alert-info small py-2 mb-3 donate-gateway-notice">{{ $gateway['notice'] }}</div>
                    <div class="donate-card-preview mb-3" aria-disabled="true">
                        <div class="donate-card-preview__badge">
                            <i class="fas fa-lock me-1" aria-hidden="true"></i> Preview — checkout not active yet
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Card number</label>
                                <input type="text" class="form-control" disabled placeholder="4242 4242 4242 4242" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Expiry</label>
                                <input type="text" class="form-control" disabled placeholder="MM / YY" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CVC</label>
                                <input type="text" class="form-control" disabled placeholder="123" autocomplete="off">
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-lg w-100 donate-card-preview__pay" disabled>
                                    Pay with card ({{ $gateway['currency'] }})
                                </button>
                            </div>
                        </div>
                    </div>
                    <p class="small text-muted mb-0 donate-method-panel__hint donate-method-panel__hint--gateway">
                        Until card checkout goes live, complete your gift via WhatsApp or Email below.
                    </p>
                @else
                    <p class="mb-3">{{ $method['instructions'] ?? 'Online card payment is not configured yet. Send your pledge to our team and we will share payment details.' }}</p>
                    <p class="small text-muted mb-0 donate-method-panel__hint donate-method-panel__hint--notify">
                        Send your pledge via WhatsApp or Email below.
                    </p>
                @endif
            </div>
        @endforeach
    </div>
</div>
