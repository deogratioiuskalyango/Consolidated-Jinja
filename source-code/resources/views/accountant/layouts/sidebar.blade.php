<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">

                {{-- Dashboard --}}
                <li class="{{ @$navDashboardActiveClass }}">
                    <a href="{{ route('accountant.dashboard') }}">
                        <i class="ri-dashboard-line"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>

                {{-- Rent Collections --}}
                <li class="{{ @$navCollectionsActiveClass }}">
                    <a href="{{ route('accountant.collections.index') }}">
                        <i class="ri-hand-coin-line"></i>
                        <span>{{ __('Rent Collections') }}</span>
                    </a>
                </li>

                {{-- Expenses --}}
                <li class="{{ @$navExpensesActiveClass }}">
                    <a href="{{ route('accountant.expenses.index') }}">
                        <i class="ri-receipt-line"></i>
                        <span>{{ __('Expenses') }}</span>
                    </a>
                </li>

                {{-- Reports --}}
                <li class="{{ @$navReportsActiveClass }}">
                    <a href="{{ route('accountant.reports.index') }}">
                        <i class="ri-file-chart-line"></i>
                        <span>{{ __('Reports') }}</span>
                    </a>
                </li>

                {{-- Reconciliation --}}
                <li class="{{ @$navReconciliationActiveClass }}">
                    <a href="{{ route('accountant.reconciliation.index') }}">
                        <i class="ri-scales-3-line"></i>
                        <span>{{ __('Reconciliation') }}</span>
                    </a>
                </li>

                {{-- Tenant Balances --}}
                <li class="{{ @$navBalancesActiveClass }}">
                    <a href="{{ route('accountant.balances') }}">
                        <i class="ri-wallet-3-line"></i>
                        <span>{{ __('Tenant Balances') }}</span>
                    </a>
                </li>

                {{-- Audit Logs --}}
                <li class="{{ @$navAuditLogsActiveClass }}">
                    <a href="{{ route('accountant.audit-logs') }}">
                        <i class="ri-history-line"></i>
                        <span>{{ __('Audit Logs') }}</span>
                    </a>
                </li>

                {{-- Profile Sub-menu --}}
                <li class="{{ @$navProfileMMShowClass }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="ri-user-settings-line"></i>
                        <span>{{ __('Account') }}</span>
                    </a>
                    <ul class="sub-menu {{ @$navProfileMMShowClass }}" aria-expanded="false">
                        <li class="{{ @$navProfileActiveClass }}">
                            <a href="{{ route('accountant.profile') }}" class="{{ @$navProfileActiveClass }}">
                                {{ __('My Profile') }}
                            </a>
                        </li>
                        <li class="{{ @$navChangePasswordActiveClass }}">
                            <a href="{{ route('accountant.change-password') }}" class="{{ @$navChangePasswordActiveClass }}">
                                {{ __('Change Password') }}
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</div>
