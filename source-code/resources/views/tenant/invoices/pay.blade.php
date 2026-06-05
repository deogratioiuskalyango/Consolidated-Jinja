@extends('tenant.layouts.app')

@push('style')
<style>
/* ─── Payment Page ─────────────────────────────────────────── */
.pay-page-wrap {
    max-width: 1060px;
    margin: 0 auto;
}

/* Amount hero card */
.pay-hero {
    background: linear-gradient(135deg, #1e40af 0%, #3730a3 50%, #4f46e5 100%);
    border-radius: 16px;
    padding: 36px 40px;
    color: #fff;
    position: relative;
    overflow: hidden;
    margin-bottom: 28px;
}
.pay-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: rgba(255,255,255,.07);
    border-radius: 50%;
}
.pay-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; left: 20%;
    width: 300px; height: 300px;
    background: rgba(255,255,255,.04);
    border-radius: 50%;
}
.pay-hero-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    opacity: .75;
    margin-bottom: 6px;
}
.pay-hero-amount {
    font-size: 3rem;
    font-weight: 800;
    letter-spacing: -.02em;
    line-height: 1;
    margin-bottom: 8px;
}
.pay-hero-meta {
    opacity: .7;
    font-size: 13.5px;
}
.pay-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 11.5px;
    font-weight: 600;
}
.pay-hero-overdue {
    background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 50%, #b91c1c 100%);
}

/* Main cards */
.pay-card {
    background: var(--t-bg-card, #fff);
    border: 1px solid var(--t-border, #e2e8f0);
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 20px;
    transition: box-shadow .2s;
}
.pay-card:hover {
    box-shadow: 0 4px 24px rgba(0,0,0,.07);
}
body[data-bs-theme="dark"] .pay-card {
    background: rgba(15,23,42,.6);
    border-color: rgba(255,255,255,.08);
    backdrop-filter: blur(12px);
}
.pay-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--t-border, #e2e8f0);
    display: flex;
    align-items: center;
    gap: 10px;
}
body[data-bs-theme="dark"] .pay-card-header {
    border-color: rgba(255,255,255,.07);
}
.pay-card-header-icon {
    width: 34px; height: 34px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.pay-card-header-icon.blue  { background: #eff6ff; color: #2563eb; }
.pay-card-header-icon.green { background: #f0fdf4; color: #16a34a; }
body[data-bs-theme="dark"] .pay-card-header-icon.blue  { background: rgba(37,99,235,.18); color: #60a5fa; }
body[data-bs-theme="dark"] .pay-card-header-icon.green { background: rgba(22,163,74,.18); color: #4ade80; }
.pay-card-title {
    font-size: 14.5px;
    font-weight: 700;
    margin: 0;
    color: var(--t-text-primary, #1e293b);
}
body[data-bs-theme="dark"] .pay-card-title { color: #f1f5f9; }
.pay-card-body { padding: 20px 24px; }

/* Invoice row items */
.inv-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 11px 0;
    border-bottom: 1px solid var(--t-border, #f1f5f9);
}
body[data-bs-theme="dark"] .inv-row { border-color: rgba(255,255,255,.06); }
.inv-row:last-child { border-bottom: none; }
.inv-row-label {
    font-size: 13px;
    color: var(--t-text-muted, #64748b);
    display: flex;
    align-items: center;
    gap: 8px;
}
.inv-row-label i { font-size: 15px; opacity: .6; }
.inv-row-value {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--t-text-primary, #1e293b);
    text-align: right;
}
body[data-bs-theme="dark"] .inv-row-value { color: #f1f5f9; }
.inv-row-value.danger { color: #ef4444 !important; }

/* Currency table */
.currency-table-wrap .table { margin-bottom: 0; }
.currency-table-wrap .table td { padding: 10px 0; border-color: var(--t-border, #f1f5f9); }
body[data-bs-theme="dark"] .currency-table-wrap .table td { border-color: rgba(255,255,255,.06); }

/* Payment method cards */
.pay-methods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}
.pay-method-card {
    border: 2px solid var(--t-border, #e2e8f0);
    border-radius: 12px;
    padding: 16px;
    cursor: pointer;
    transition: all .2s ease;
    position: relative;
    background: var(--t-bg-surface, #f8fafc);
    display: flex;
    flex-direction: column;
    gap: 10px;
    user-select: none;
}
body[data-bs-theme="dark"] .pay-method-card {
    background: rgba(15,23,42,.4);
    border-color: rgba(255,255,255,.1);
}
.pay-method-card:hover {
    border-color: #3b82f6;
    background: #eff6ff;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(59,130,246,.15);
}
body[data-bs-theme="dark"] .pay-method-card:hover {
    background: rgba(59,130,246,.1);
    border-color: #3b82f6;
}
.pay-method-card.active {
    border-color: #3b82f6;
    background: #eff6ff;
    box-shadow: 0 0 0 4px rgba(59,130,246,.12);
}
body[data-bs-theme="dark"] .pay-method-card.active {
    background: rgba(59,130,246,.12);
}
.pay-method-check {
    position: absolute;
    top: 10px; right: 10px;
    width: 20px; height: 20px;
    border-radius: 50%;
    border: 2px solid var(--t-border, #cbd5e1);
    background: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px;
    color: transparent;
    transition: all .2s;
}
body[data-bs-theme="dark"] .pay-method-check { background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.2); }
.pay-method-card.active .pay-method-check {
    background: #3b82f6;
    border-color: #3b82f6;
    color: #fff;
}
.pay-method-img {
    height: 36px;
    display: flex;
    align-items: center;
}
.pay-method-img img {
    max-height: 36px;
    max-width: 100px;
    object-fit: contain;
}
.pay-method-name {
    font-size: 13px;
    font-weight: 700;
    color: var(--t-text-primary, #1e293b);
}
body[data-bs-theme="dark"] .pay-method-name { color: #f1f5f9; }
.pay-method-note {
    font-size: 11px;
    color: var(--t-text-muted, #64748b);
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
}

/* Bank form panel */
.pay-form-panel {
    background: var(--t-bg-surface, #f8fafc);
    border: 1px solid var(--t-border, #e2e8f0);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    display: none;
}
body[data-bs-theme="dark"] .pay-form-panel {
    background: rgba(255,255,255,.03);
    border-color: rgba(255,255,255,.08);
}
.pay-form-panel.show { display: block; }
.pay-form-panel .form-label {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--t-text-muted, #64748b);
    margin-bottom: 6px;
}
.pay-form-panel .form-control {
    border-radius: 8px;
    border-color: var(--t-border, #e2e8f0);
    font-size: 13.5px;
    padding: 10px 14px;
}
.pay-form-panel .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}

/* Bank details box */
.bank-details-box {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 12px 14px;
    font-size: 13px;
    color: #1e40af;
    margin-bottom: 0;
}
body[data-bs-theme="dark"] .bank-details-box {
    background: rgba(59,130,246,.1);
    border-color: rgba(59,130,246,.3);
    color: #93c5fd;
}

/* Pay button */
.pay-btn {
    width: 100%;
    padding: 15px 24px;
    border-radius: 12px;
    border: none;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: .01em;
    cursor: pointer;
    transition: all .2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, #2563eb, #4f46e5);
    color: #fff;
    box-shadow: 0 4px 16px rgba(59,130,246,.4);
}
.pay-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 24px rgba(59,130,246,.5);
    filter: brightness(1.05);
}
.pay-btn:active {
    transform: translateY(0);
}
.pay-btn:disabled {
    opacity: .6;
    cursor: not-allowed;
    transform: none;
}

/* Trust strip */
.pay-trust-strip {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    padding: 14px 0 4px;
    flex-wrap: wrap;
}
.pay-trust-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    color: var(--t-text-muted, #94a3b8);
    font-weight: 500;
}
.pay-trust-item i { font-size: 13px; }

/* Empty gateways */
.pay-no-methods {
    text-align: center;
    padding: 40px 20px;
    color: var(--t-text-muted, #64748b);
}
.pay-no-methods i { font-size: 40px; margin-bottom: 12px; display: block; opacity: .4; }

/* Responsive */
@media (max-width: 991px) {
    .pay-hero-amount { font-size: 2.2rem; }
    .pay-hero { padding: 28px 24px; }
}
</style>
@endpush

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Breadcrumb --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-1" style="font-weight:800;">{{ $pageTitle }}</h4>
                                <ol class="breadcrumb mb-0" style="font-size:12.5px;">
                                    <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('tenant.invoice.index') }}">{{ __('Invoices') }}</a></li>
                                    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                                </ol>
                            </div>
                            <a href="{{ route('tenant.invoice.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="ri-arrow-left-line me-1"></i>{{ __('Back') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="pay-page-wrap">

                    {{-- ── Amount Hero ── --}}
                    <div class="pay-hero {{ $invoice->due_date < date('Y-m-d') ? 'pay-hero-overdue' : '' }}">
                        <div class="row align-items-center">
                            <div class="col-md-7 col-12 mb-3 mb-md-0" style="position:relative;z-index:1;">
                                <div class="pay-hero-label">{{ __('Total Amount Due') }}</div>
                                <div class="pay-hero-amount">
                                    @if ($invoice->due_date < date('Y-m-d'))
                                        {{ currencyPrice($invoice->amount + $invoice->late_fee) }}
                                    @else
                                        {{ currencyPrice($invoice->amount) }}
                                    @endif
                                </div>
                                <div class="pay-hero-meta mb-3">
                                    {{ __('Invoice') }} #{{ $invoice->invoice_no }}
                                    &nbsp;·&nbsp; {{ __('Due') }} {{ $invoice->due_date }}
                                </div>
                                @if ($invoice->due_date < date('Y-m-d'))
                                    <span class="pay-hero-badge">
                                        <i class="ri-error-warning-line"></i>
                                        {{ __('Overdue — late fee included') }}
                                    </span>
                                @else
                                    <span class="pay-hero-badge">
                                        <i class="ri-checkbox-circle-line"></i>
                                        {{ __('Payment pending') }}
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-5 col-12 text-md-end" style="position:relative;z-index:1;">
                                <div style="display:inline-flex;flex-direction:column;gap:8px;text-align:left;">
                                    <div class="pay-hero-badge" style="justify-content:flex-start;">
                                        <i class="ri-building-4-line"></i>
                                        <span style="font-weight:500;opacity:.9;">{{ $invoice->name }}</span>
                                    </div>
                                    <div class="pay-hero-badge" style="justify-content:flex-start;">
                                        <i class="ri-calendar-line"></i>
                                        <span style="font-weight:500;opacity:.9;">{{ __('Issued') }} {{ $invoice->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Two-column layout ── --}}
                    <div class="row g-4">

                        {{-- Left: Invoice Details + Currency --}}
                        <div class="col-lg-5">

                            {{-- Invoice Summary --}}
                            <div class="pay-card">
                                <div class="pay-card-header">
                                    <div class="pay-card-header-icon blue">
                                        <i class="ri-file-list-3-line"></i>
                                    </div>
                                    <h6 class="pay-card-title">{{ __('Invoice Summary') }}</h6>
                                </div>
                                <div class="pay-card-body" style="padding-top:12px;padding-bottom:12px;">
                                    <div class="inv-row">
                                        <span class="inv-row-label"><i class="ri-hashtag"></i>{{ __('Invoice No.') }}</span>
                                        <span class="inv-row-value">#{{ $invoice->invoice_no }}</span>
                                    </div>
                                    <div class="inv-row">
                                        <span class="inv-row-label"><i class="ri-building-4-line"></i>{{ __('Description') }}</span>
                                        <span class="inv-row-value" style="max-width:160px;">{{ $invoice->name }}</span>
                                    </div>
                                    <div class="inv-row">
                                        <span class="inv-row-label"><i class="ri-calendar-event-line"></i>{{ __('Issue Date') }}</span>
                                        <span class="inv-row-value">{{ $invoice->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="inv-row">
                                        <span class="inv-row-label"><i class="ri-calendar-close-line"></i>{{ __('Due Date') }}</span>
                                        <span class="inv-row-value {{ $invoice->due_date < date('Y-m-d') ? 'danger' : '' }}">
                                            {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <div class="inv-row">
                                        <span class="inv-row-label"><i class="ri-money-dollar-circle-line"></i>{{ __('Base Amount') }}</span>
                                        <span class="inv-row-value">{{ currencyPrice($invoice->amount) }}</span>
                                    </div>
                                    @if ($invoice->due_date < date('Y-m-d'))
                                    <div class="inv-row">
                                        <span class="inv-row-label danger" style="color:#ef4444;"><i class="ri-alarm-warning-line"></i>{{ __('Late Fee') }}</span>
                                        <span class="inv-row-value danger">+ {{ currencyPrice($invoice->late_fee) }}</span>
                                    </div>
                                    @endif
                                    <div class="inv-row" style="padding-top:14px;border-top:2px solid var(--t-border,#e2e8f0);margin-top:4px;">
                                        <span class="inv-row-label" style="font-weight:700;font-size:14px;color:var(--t-text-primary,#1e293b);">{{ __('Total Due') }}</span>
                                        <span class="inv-row-value" style="font-size:17px;color:#2563eb;">
                                            @if ($invoice->due_date < date('Y-m-d'))
                                                {{ currencyPrice($invoice->amount + $invoice->late_fee) }}
                                            @else
                                                {{ currencyPrice($invoice->amount) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Currency selector (populated by JS) --}}
                            <div class="pay-card" id="currencyCard" style="display:none;">
                                <div class="pay-card-header">
                                    <div class="pay-card-header-icon green">
                                        <i class="ri-exchange-dollar-line"></i>
                                    </div>
                                    <h6 class="pay-card-title">{{ __('Select Currency') }}</h6>
                                </div>
                                <div class="pay-card-body currency-table-wrap" style="padding-top:8px;padding-bottom:8px;">
                                    <table class="table table-borderless mb-0">
                                        <tbody id="currencyAppend"></tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        {{-- Right: Payment method + form --}}
                        <div class="col-lg-7">
                            <form action="{{ route('payment.checkout') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                <input type="hidden" id="selectGateway" name="gateway">
                                <input type="hidden" id="selectCurrency" name="currency">

                                <div class="pay-card">
                                    <div class="pay-card-header">
                                        <div class="pay-card-header-icon blue">
                                            <i class="ri-bank-card-line"></i>
                                        </div>
                                        <h6 class="pay-card-title">{{ __('Payment Method') }}</h6>
                                    </div>
                                    <div class="pay-card-body">

                                        @if($gateways->isEmpty())
                                            <div class="pay-no-methods">
                                                <i class="ri-bank-card-2-line"></i>
                                                <p class="mb-0">{{ __('No payment methods are currently configured.') }}<br>
                                                <small>{{ __('Please contact your property manager.') }}</small></p>
                                            </div>
                                        @else

                                        {{-- Gateway cards --}}
                                        <div class="pay-methods-grid" id="gatewayGrid">
                                            @foreach ($gateways as $gateway)
                                            <div class="pay-method-card paymentGateway"
                                                 data-bs-target="#{{ $gateway->slug }}"
                                                 data-gateway="{{ $gateway->slug }}"
                                                 data-id="{{ $gateway->id }}"
                                                 role="button"
                                                 tabindex="0">
                                                <div class="pay-method-check">
                                                    <i class="ri-check-line"></i>
                                                </div>
                                                <div class="pay-method-img">
                                                    <img src="{{ asset($gateway->image) }}" alt="{{ $gateway->title }}"
                                                         onerror="this.closest('.pay-method-img').innerHTML='<i class=\'ri-secure-payment-line\' style=\'font-size:28px;color:#3b82f6;\'></i>'">
                                                </div>
                                                <div>
                                                    <div class="pay-method-name">{{ $gateway->title }}</div>
                                                    @if(in_array($gateway->slug, ['cash','bank']))
                                                    <div class="pay-method-note">
                                                        <i class="ri-time-line"></i>{{ __('Requires approval') }}
                                                    </div>
                                                    @else
                                                    <div class="pay-method-note">
                                                        <i class="ri-shield-check-line"></i>{{ __('Secure checkout') }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                        {{-- Bank deposit panel --}}
                                        <div class="pay-form-panel" id="bankPanel">
                                            <div class="d-flex align-items-center gap-2 mb-16" style="margin-bottom:16px;">
                                                <i class="ri-bank-line" style="font-size:18px;color:#2563eb;"></i>
                                                <strong style="font-size:14px;">{{ __('Bank Deposit Details') }}</strong>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label">{{ __('Select Bank') }}</label>
                                                    <select name="bank_id" class="form-control" id="bank_id">
                                                        <option value="">— {{ __('Choose a bank') }} —</option>
                                                        @foreach ($banks as $bank)
                                                            <option value="{{ $bank->id }}" data-details="{{ $bank->details }}">
                                                                {{ $bank->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('bank_id')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12 d-none" id="bankDetails">
                                                    <div class="bank-details-box">
                                                        <i class="ri-information-line me-1"></i>
                                                        <span id="bankDetailsText"></span>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">{{ __('Deposited By') }}</label>
                                                    <input type="text" class="form-control" name="deposit_by"
                                                           placeholder="{{ __('Full name on receipt') }}">
                                                    @error('deposit_by')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">{{ __('Upload Deposit Slip') }}</label>
                                                    <input type="file" class="form-control" name="bank_slip" accept="image/*,.pdf">
                                                    @error('bank_slip')
                                                        <span class="text-danger small">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        @endif

                                    </div>
                                </div>

                                {{-- Pay button --}}
                                <button type="button" class="pay-btn" id="payBtn">
                                    <i class="ri-secure-payment-line" style="font-size:20px;"></i>
                                    <span>{{ __('Pay Now') }}</span>
                                    <span id="gatewayCurrencyAmount" style="opacity:.85;font-weight:500;font-size:14px;"></span>
                                </button>

                                {{-- Trust strip --}}
                                <div class="pay-trust-strip">
                                    <div class="pay-trust-item">
                                        <i class="ri-shield-keyhole-line" style="color:#16a34a;"></i>
                                        {{ __('SSL Secured') }}
                                    </div>
                                    <div class="pay-trust-item">
                                        <i class="ri-lock-2-line"></i>
                                        {{ __('Encrypted Payment') }}
                                    </div>
                                    <div class="pay-trust-item">
                                        <i class="ri-customer-service-2-line"></i>
                                        {{ __('24/7 Support') }}
                                    </div>
                                </div>

                            </form>
                        </div>

                    </div>{{-- /row --}}
                </div>{{-- /pay-page-wrap --}}

            </div>
        </div>
    </div>

    {{-- Hidden values for JS --}}
    @if ($invoice->due_date < date('Y-m-d'))
        <input type="hidden" id="invoiceAmount" value="{{ $invoice->amount + $invoice->late_fee }}">
    @else
        <input type="hidden" id="invoiceAmount" value="{{ $invoice->amount }}">
    @endif
    <input type="hidden" id="getCurrencyByGatewayRoute" value="{{ route('tenant.invoice.get.currency') }}">
@endsection

@push('script')
<script>
"use strict";

// Gateway card click
$(document).on('click keypress', '.paymentGateway', function (e) {
    if (e.type === 'keypress' && e.which !== 13) return;

    // Update active state
    $('.paymentGateway').removeClass('active');
    $(this).addClass('active');

    var slug = $(this).data('gateway');
    $('#selectGateway').val(slug);
    $('#selectCurrency').val('');
    $('#gatewayCurrencyAmount').text('');

    // Show bank panel only for bank gateway
    if (slug === 'bank') {
        $('#bankPanel').addClass('show');
    } else {
        $('#bankPanel').removeClass('show');
    }

    // Fetch currencies
    var id = $(this).data('id');
    commonAjax('GET', $('#getCurrencyByGatewayRoute').val(), getCurrencyRes, getCurrencyRes, { 'id': id });
});

function getCurrencyRes(response) {
    var html = '';
    var invoiceAmount = parseFloat($('#invoiceAmount').val()).toFixed(2);
    var entries = Object.entries(response.data);

    if (entries.length === 0) {
        $('#currencyCard').hide();
        return;
    }

    entries.forEach(function([key, curr]) {
        var currencyAmount = curr.conversion_rate * invoiceAmount;
        var formatted = gatewayCurrencyPrice(currencyAmount, curr.symbol);
        html += `
        <tr>
            <td>
                <div class="custom-radiobox gatewayCurrencyAmount">
                    <input type="radio" name="gateway_currency_amount" id="curr_${curr.id}" value="${formatted}">
                    <label for="curr_${curr.id}" style="font-weight:600;">${curr.currency}</label>
                </div>
            </td>
            <td class="text-end">
                <span style="font-size:15px;font-weight:700;color:#2563eb;">${formatted}</span>
            </td>
        </tr>`;
    });

    $('#currencyAppend').html(html);
    $('#currencyCard').show();
}

// Currency selection
$(document).on('click', '.gatewayCurrencyAmount', function () {
    var val = $(this).find('input').val();
    $('#gatewayCurrencyAmount').text('(' + val + ')');
    $('#selectCurrency').val($(this).find('label').text().trim());
});

// Bank dropdown
$('#bank_id').on('change', function () {
    var details = $(this).find(':selected').data('details');
    if (details) {
        $('#bankDetailsText').text(details);
        $('#bankDetails').removeClass('d-none');
    } else {
        $('#bankDetails').addClass('d-none');
    }
});

// Pay button
$('#payBtn').on('click', function () {
    var gateway = $('#selectGateway').val();
    var currency = $('#selectCurrency').val();
    if (!gateway) {
        toastr.error("{{ __('Please select a payment method') }}");
        return;
    }
    if (!currency) {
        toastr.error("{{ __('Please select a currency') }}");
        return;
    }
    $(this).prop('disabled', true);
    $(this).html('<i class="ri-loader-4-line"></i> {{ __("Processing...") }}');
    $(this).closest('form')[0].submit();
});
</script>
@endpush
