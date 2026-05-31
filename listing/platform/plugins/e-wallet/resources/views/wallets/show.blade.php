@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="max-width-1200">
        <div class="ui-layout">
            <div class="flexbox-layout-sections">
                <div class="flexbox-layout-section-primary mt-20">
                    {{-- Wallet Info Card --}}
                    <x-core::card class="mb-3">
                        <x-core::card.header>
                            <x-core::card.title>
                                <x-core::icon name="ti ti-wallet" />
                                {{ trans('plugins/e-wallet::e-wallet.wallet.details') }}
                            </x-core::card.title>
                        </x-core::card.header>
                        <x-core::card.body>
                            <div class="row g-3">
                                {{-- Customer Info Column --}}
                                <div class="col-12 col-md-6">
                                    <x-core::datagrid>
                                        <x-core::datagrid.item class="mb-3">
                                            <x-slot:title>
                                                <x-core::icon name="ti ti-user" />
                                                {{ trans('plugins/ecommerce::customer.name') }}
                                            </x-slot:title>
                                            <span class="fw-semibold">{{ $wallet->customer?->name ?? '—' }}</span>
                                        </x-core::datagrid.item>

                                        <x-core::datagrid.item class="mb-3">
                                            <x-slot:title>
                                                <x-core::icon name="ti ti-mail" />
                                                {{ trans('plugins/ecommerce::customer.email') }}
                                            </x-slot:title>
                                            @if($wallet->customer?->email)
                                                <a href="mailto:{{ $wallet->customer->email }}" class="text-decoration-none">
                                                    {{ $wallet->customer->email }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </x-core::datagrid.item>
                                    </x-core::datagrid>
                                </div>

                                {{-- Balance Info Column --}}
                                <div class="col-12 col-md-6">
                                    <x-core::datagrid>
                                        <x-core::datagrid.item class="mb-3">
                                            <x-slot:title>
                                                <x-core::icon name="ti ti-coins" />
                                                {{ trans('plugins/e-wallet::e-wallet.wallet.balance') }}
                                            </x-slot:title>
                                            <x-core::badge
                                                :color="$wallet->balance >= 0 ? 'success' : 'danger'"
                                                :label="$wallet->formatted_balance"
                                                class="fs-5"
                                            />
                                        </x-core::datagrid.item>

                                        <x-core::datagrid.item class="mb-3">
                                            <x-slot:title>
                                                <x-core::icon name="ti ti-currency-dollar" />
                                                {{ trans('plugins/e-wallet::e-wallet.wallet.currency') }}
                                            </x-slot:title>
                                            <span class="badge bg-secondary-lt">{{ $wallet->currency_code }}</span>
                                        </x-core::datagrid.item>
                                    </x-core::datagrid>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex gap-2 mt-3 pt-3 border-top">
                                <a href="{{ route('e-wallet.wallets.adjust', $wallet->id) }}" class="btn btn-primary">
                                    <x-core::icon name="ti ti-adjustments-horizontal" />
                                    {{ trans('plugins/e-wallet::e-wallet.adjustment.title') }}
                                </a>
                                <a href="{{ route('e-wallet.wallets.index') }}" class="btn btn-secondary">
                                    <x-core::icon name="ti ti-arrow-left" />
                                    {{ trans('plugins/e-wallet::e-wallet.forms.back') }}
                                </a>
                            </div>
                        </x-core::card.body>
                    </x-core::card>

                    {{-- Transaction History --}}
                    <x-core::card>
                        <x-core::card.header>
                            <x-core::card.title>
                                <x-core::icon name="ti ti-history" />
                                {{ trans('plugins/e-wallet::e-wallet.transaction.history') }}
                            </x-core::card.title>
                        </x-core::card.header>
                        <x-core::card.body class="p-0">
                            {!! $transactionsTable->render('core/table::base-table') !!}
                        </x-core::card.body>
                    </x-core::card>
                </div>
            </div>
        </div>
    </div>
@endsection
