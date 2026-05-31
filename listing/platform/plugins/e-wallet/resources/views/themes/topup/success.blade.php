@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', $isPending ? trans('plugins/e-wallet::e-wallet.topup.processing_title') : trans('plugins/e-wallet::e-wallet.topup.success_title'))

@section('content')
    <div class="bb-customer-content-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="bb-customer-card-list account-settings-cards">
                    <div class="bb-customer-card topup-result-card {{ $isPending ? 'topup-pending' : 'topup-success' }}">
                        <div class="bb-customer-card-body text-center py-5">
                            @if($isPending)
                                <div class="result-icon pending-icon mb-4">
                                    <x-core::icon name="ti ti-clock" />
                                </div>
                                <h2 class="result-title mb-3">{{ trans('plugins/e-wallet::e-wallet.topup.processing_title') }}</h2>
                                <p class="result-message text-muted mb-4">
                                    {{ trans('plugins/e-wallet::e-wallet.topup.processing_message') }}
                                </p>
                            @else
                                <div class="result-icon success-icon mb-4">
                                    <x-core::icon name="ti ti-circle-check" />
                                </div>
                                <h2 class="result-title mb-3">{{ trans('plugins/e-wallet::e-wallet.topup.success_title') }}</h2>
                                <p class="result-message text-muted mb-4">
                                    {{ trans('plugins/e-wallet::e-wallet.topup.success_message') }}
                                </p>
                            @endif

                            <div class="transaction-receipt mb-4">
                                <div class="bb-customer-card-info">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="info-item text-center">
                                                <span class="label">{{ trans('plugins/e-wallet::e-wallet.topup.code') }}</span>
                                                <span class="value">{{ $topup->code }}</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="info-item text-center">
                                                <span class="label">{{ trans('plugins/e-wallet::e-wallet.topup.amount') }}</span>
                                                <span class="value text-success">+{{ format_price($topup->converted_amount / 100) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="current-balance-display mb-4">
                                <div class="info-item text-center">
                                    <span class="label">{{ trans('plugins/e-wallet::e-wallet.wallet.current_balance') }}</span>
                                    <span class="value balance-value text-success">{{ $wallet->formatted_balance }}</span>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('customer.e-wallet.index') }}" class="btn btn-primary">
                                    <x-core::icon name="ti ti-wallet" class="me-2" />
                                    {{ trans('plugins/e-wallet::e-wallet.topup.back_to_wallet') }}
                                </a>
                                <a href="{{ route('customer.e-wallet.topup.create') }}" class="btn btn-outline-secondary">
                                    <x-core::icon name="ti ti-plus" class="me-2" />
                                    {{ trans('plugins/e-wallet::e-wallet.topup.topup_again') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
