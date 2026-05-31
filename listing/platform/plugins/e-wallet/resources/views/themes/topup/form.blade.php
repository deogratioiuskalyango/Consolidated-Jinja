@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', trans('plugins/e-wallet::e-wallet.topup.title'))

@section('content')
    <div class="bb-customer-content-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="bb-customer-card-list wallet-cards">
                    <div class="bb-customer-card wallet-balance-section mb-4">
                        <div class="bb-customer-card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="info-item">
                                        <span class="label">{{ trans('plugins/e-wallet::e-wallet.topup.current_balance') }}</span>
                                        <span class="value {{ $wallet->balance >= 0 ? 'text-success' : 'text-danger' }}">{{ $wallet->formatted_balance }}</span>
                                    </div>
                                </div>
                                <div class="wallet-icon-wrapper">
                                    <x-core::icon name="ti ti-wallet" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bb-customer-card-list account-settings-cards">
                    <div class="bb-customer-card">
                        <div class="bb-customer-card-header">
                            <h4 class="bb-customer-card-title mb-0">
                                <x-core::icon name="ti ti-credit-card" class="me-2" />
                                {{ trans('plugins/e-wallet::e-wallet.topup.add_funds') }}
                            </h4>
                        </div>
                        <div class="bb-customer-card-body">
                            <form action="{{ route('customer.e-wallet.topup.store') }}" method="POST" id="topup-form">
                                @csrf

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">{{ trans('plugins/e-wallet::e-wallet.topup.quick_select') }}</label>
                                    <div class="row g-2">
                                        @foreach($predefinedAmounts as $amount)
                                            @if($amount >= $minAmount && $amount <= $maxAmount)
                                                <div class="col-6 col-sm-4 col-md-3">
                                                    <button type="button"
                                                            class="btn btn-outline-secondary w-100 amount-btn"
                                                            data-amount="{{ $amount }}">
                                                        <span class="d-block fw-semibold">{{ format_price($amount) }}</span>
                                                    </button>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">{{ trans('plugins/e-wallet::e-wallet.topup.custom_amount') }}</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">{{ cms_currency()->getApplicationCurrency()->symbol }}</span>
                                        <input type="number"
                                               name="amount"
                                               id="topup-amount"
                                               class="form-control @error('amount') is-invalid @enderror"
                                               min="{{ $minAmount }}"
                                               max="{{ $maxAmount }}"
                                               step="0.01"
                                               value="{{ old('amount') }}"
                                               placeholder="{{ trans('plugins/e-wallet::e-wallet.topup.enter_amount') }}"
                                               required>
                                    </div>
                                    @error('amount')
                                        <div class="text-danger small mt-2">
                                            <x-core::icon name="ti ti-alert-circle" class="me-1" />
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="text-muted small mt-2">
                                        <x-core::icon name="ti ti-info-circle" class="me-1" />
                                        {{ trans('plugins/e-wallet::e-wallet.topup.amount_range', [
                                            'min' => format_price($minAmount),
                                            'max' => format_price($maxAmount),
                                        ]) }}
                                    </div>
                                </div>

                                <div class="topup-summary mb-4" id="topup-summary" style="display: none;">
                                    <div class="summary-row">
                                        <span class="summary-label">{{ trans('plugins/e-wallet::e-wallet.topup.you_will_add') }}</span>
                                        <span class="summary-value amount" id="summary-amount">$0.00</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="summary-label">{{ trans('plugins/e-wallet::e-wallet.topup.new_balance') }}</span>
                                        <span class="summary-value" id="summary-new-balance">{{ $wallet->formatted_balance }}</span>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submit-btn" disabled>
                                        <x-core::icon name="ti ti-arrow-right" class="me-2" />
                                        {{ trans('plugins/e-wallet::e-wallet.topup.continue_to_payment') }}
                                    </button>
                                    <a href="{{ route('customer.e-wallet.index') }}" class="btn btn-outline-secondary">
                                        <x-core::icon name="ti ti-arrow-left" class="me-2" />
                                        {{ trans('plugins/e-wallet::e-wallet.topup.back_to_wallet') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('topup-amount');
    const submitBtn = document.getElementById('submit-btn');
    const summary = document.getElementById('topup-summary');
    const summaryAmount = document.getElementById('summary-amount');
    const summaryNewBalance = document.getElementById('summary-new-balance');
    const currentBalance = {{ $wallet->balance / 100 }};
    const currencySymbol = '{{ cms_currency()->getApplicationCurrency()->symbol }}';
    const minAmount = {{ $minAmount }};
    const maxAmount = {{ $maxAmount }};

    function formatPrice(amount) {
        return currencySymbol + amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function updateSummary() {
        const amount = parseFloat(amountInput.value) || 0;

        if (amount >= minAmount && amount <= maxAmount) {
            summary.style.display = 'block';
            summaryAmount.textContent = '+' + formatPrice(amount);
            summaryNewBalance.textContent = formatPrice(currentBalance + amount);
            submitBtn.disabled = false;
        } else {
            summary.style.display = 'none';
            submitBtn.disabled = true;
        }
    }

    amountInput.addEventListener('input', function() {
        document.querySelectorAll('.amount-btn').forEach(function(b) {
            b.classList.remove('active', 'btn-primary');
            b.classList.add('btn-outline-secondary');
        });
        updateSummary();
    });

    document.querySelectorAll('.amount-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.amount-btn').forEach(function(b) {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('active', 'btn-primary');

            amountInput.value = this.dataset.amount;
            updateSummary();
        });
    });

    updateSummary();
});
</script>
@stop
