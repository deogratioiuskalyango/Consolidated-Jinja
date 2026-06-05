@php $navDashboardActiveClass = 'mm-active' @endphp
@extends('owner.layouts.app')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="page-content-wrapper bg-white p-30 radius-20">

                    {{-- Page Header --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex flex-column flex-sm-row align-items-sm-center justify-content-between g-20 border-bottom mb-25">
                                <div class="page-title-left">
                                    <h2 class="mb-sm-0">
                                        {{ __('Finance Manager Dashboard') }}
                                        <span class="badge bg-success text-white ms-2 font-13 fw-normal">{{ __('Finance View') }}</span>
                                    </h2>
                                    <p class="mb-0">{{ __('Welcome back') }}, {{ auth()->user()->name }} &mdash; {{ now()->format('l, d F Y') }}</p>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li class="breadcrumb-item active">{{ __('Finance Manager') }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 1: 8 KPI Cards --}}
                    <div class="row">
                        {{-- Monthly Revenue --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-money-dollar-circle-line font-22 green-color"></i>
                                </div>
                                <p class="mt-2">{{ __('Monthly Revenue') }}</p>
                                <h2 class="mt-1 green-color" style="font-size:1rem;">UGX {{ number_format($monthRevenue, 0) }}</h2>
                            </div>
                        </div>
                        {{-- Monthly Expenses --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-arrow-up-circle-line font-22" style="color:#e53e3e;"></i>
                                </div>
                                <p class="mt-2">{{ __('Monthly Expenses') }}</p>
                                <h2 class="mt-1" style="color:#e53e3e;font-size:1rem;">UGX {{ number_format($monthExpenses, 0) }}</h2>
                            </div>
                        </div>
                        {{-- Net Profit (Month) --}}
                        @php $monthNetProfit = $monthRevenue - $monthExpenses; @endphp
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    @if ($monthNetProfit >= 0)
                                        <i class="ri-bar-chart-grouped-line font-22 green-color"></i>
                                    @else
                                        <i class="ri-bar-chart-grouped-line font-22" style="color:#e53e3e;"></i>
                                    @endif
                                </div>
                                <p class="mt-2">{{ __('Net Profit (Month)') }}</p>
                                <h2 class="mt-1 {{ $monthNetProfit >= 0 ? 'green-color' : '' }}" style="{{ $monthNetProfit < 0 ? 'color:#e53e3e;' : '' }} font-size:1rem;">
                                    UGX {{ number_format($monthNetProfit, 0) }}
                                </h2>
                            </div>
                        </div>
                        {{-- Yearly Revenue --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-line-chart-line font-22" style="color:#3b82f6;"></i>
                                </div>
                                <p class="mt-2">{{ __('Yearly Revenue') }}</p>
                                <h2 class="mt-1" style="color:#3b82f6;font-size:1rem;">UGX {{ number_format($totalRevenue, 0) }}</h2>
                            </div>
                        </div>
                        {{-- Total Expenses YTD --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-bill-line font-22 orange-color"></i>
                                </div>
                                <p class="mt-2">{{ __('Total Expenses YTD') }}</p>
                                <h2 class="mt-1 orange-color" style="font-size:1rem;">UGX {{ number_format($totalExpenses, 0) }}</h2>
                            </div>
                        </div>
                        {{-- Net Profit YTD --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    @if ($netProfit >= 0)
                                        <i class="ri-arrow-up-circle-line font-22 green-color"></i>
                                    @else
                                        <i class="ri-arrow-down-circle-line font-22" style="color:#e53e3e;"></i>
                                    @endif
                                </div>
                                <p class="mt-2">{{ __('Net Profit YTD') }}</p>
                                <h2 class="mt-1 {{ $netProfit >= 0 ? 'green-color' : '' }}" style="{{ $netProfit < 0 ? 'color:#e53e3e;' : '' }} font-size:1rem;">
                                    UGX {{ number_format($netProfit, 0) }}
                                </h2>
                            </div>
                        </div>
                        {{-- Outstanding Invoices --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-file-warning-line font-22" style="color:#dc2626;"></i>
                                </div>
                                <p class="mt-2">{{ __('Outstanding Balance') }}</p>
                                <h2 class="mt-1" style="color:#dc2626;font-size:1rem;">UGX {{ number_format($outstandingBalance, 0) }}</h2>
                            </div>
                        </div>
                        {{-- Pending Expenses --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-time-line font-22" style="color:#d97706;"></i>
                                </div>
                                <p class="mt-2">{{ __('Pending Expenses') }}</p>
                                <h2 class="mt-1" style="color:#d97706;">{{ number_format($pendingExpenses, 0) }}</h2>
                            </div>
                        </div>
                    </div>
                    {{-- End KPI Cards --}}

                    {{-- Quick Actions Bar --}}
                    <div class="row mb-25">
                        <div class="col-12">
                            <div class="bg-off-white theme-border radius-4 p-20">
                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    <h5 class="mb-0 me-3"><i class="ri-flashlight-line me-1"></i>{{ __('Quick Actions') }}</h5>
                                    <a href="#" class="theme-btn btn-sm px-3 py-2 font-13">
                                        <i class="ri-money-cny-circle-line me-1"></i>{{ __('Record Payment') }}
                                    </a>
                                    <a href="#" class="btn btn-outline-secondary btn-sm px-3 py-2 font-13">
                                        <i class="ri-add-circle-line me-1"></i>{{ __('Add Expense') }}
                                    </a>
                                    <a href="#" class="btn btn-outline-secondary btn-sm px-3 py-2 font-13">
                                        <i class="ri-file-chart-line me-1"></i>{{ __('Generate Report') }}
                                    </a>
                                    <a href="#" class="btn btn-outline-secondary btn-sm px-3 py-2 font-13">
                                        <i class="ri-refund-2-line me-1"></i>{{ __('Reconcile') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Revenue vs Expenses Column Chart --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h4 class="mb-0"><i class="ri-bar-chart-grouped-line me-2"></i>{{ __('Revenue vs Expenses') }}</h4>
                                    <span class="font-13 text-muted">{{ date('Y') }}</span>
                                </div>
                                <div id="financeRevenueExpenseChart"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: Recent Invoices + Recent Expenses --}}
                    <div class="row">
                        {{-- Recent Invoices --}}
                        <div class="col-lg-7">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="d-flex align-items-center justify-content-between mb-20">
                                    <h4 class="mb-0"><i class="ri-file-list-3-line me-2"></i>{{ __('Recent Invoices') }}</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table theme-border">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Tenant') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($recentInvoices as $invoice)
                                                <tr>
                                                    <td>{{ $invoice->tenant?->name ?? '—' }}</td>
                                                    <td>UGX {{ number_format($invoice->amount ?? 0, 0) }}</td>
                                                    <td>
                                                        @php
                                                            $sBadge = match((int)($invoice->status ?? 0)) {
                                                                INVOICE_STATUS_PAID    => 'bg-success',
                                                                INVOICE_STATUS_UNPAID  => 'bg-danger',
                                                                INVOICE_STATUS_PARTIAL => 'bg-warning text-dark',
                                                                default                => 'bg-secondary',
                                                            };
                                                            $sLabel = match((int)($invoice->status ?? 0)) {
                                                                INVOICE_STATUS_PAID    => __('Paid'),
                                                                INVOICE_STATUS_UNPAID  => __('Unpaid'),
                                                                INVOICE_STATUS_PARTIAL => __('Partial'),
                                                                default                => __('Unknown'),
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $sBadge }} text-white">{{ $sLabel }}</span>
                                                    </td>
                                                    <td>{{ $invoice->created_at ? $invoice->created_at->format('d M Y') : '—' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">{{ __('No invoices found') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{-- Recent Expenses --}}
                        <div class="col-lg-5">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <h4 class="mb-20"><i class="ri-shopping-bag-3-line me-2"></i>{{ __('Recent Expenses') }}</h4>
                                @forelse ($recentExpensesList as $expense)
                                    <div class="d-flex align-items-center justify-content-between py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div>
                                            <h6 class="mb-1">{{ Str::limit($expense->title ?? $expense->name ?? '—', 30) }}</h6>
                                            <p class="font-12 text-muted mb-0">
                                                {{ $expense->created_at ? $expense->created_at->format('d M Y') : '—' }}
                                            </p>
                                        </div>
                                        <span class="font-14 fw-bold" style="color:#e53e3e;">
                                            UGX {{ number_format($expense->amount ?? 0, 0) }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-center text-muted mt-3">{{ __('No expenses found') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    {{-- End Row 3 --}}

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const FINANCE_MONTHS = @json($months);
        const FINANCE_REVENUE_CHART = @json($revenueChart);
        const FINANCE_EXPENSE_CHART = @json($expenseChart);
    </script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        var financeChart = new ApexCharts(document.querySelector("#financeRevenueExpenseChart"), {
            series: [
                { name: 'Revenue (UGX)', data: FINANCE_REVENUE_CHART },
                { name: 'Expenses (UGX)', data: FINANCE_EXPENSE_CHART }
            ],
            chart: { type: 'bar', height: 300, toolbar: { show: false } },
            colors: ['#22c55e', '#ef4444'],
            plotOptions: { bar: { columnWidth: '55%', borderRadius: 4 } },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: { categories: FINANCE_MONTHS },
            yaxis: { labels: { formatter: function(val) { return 'UGX ' + val.toLocaleString(); } } },
            tooltip: { y: { formatter: function(val) { return 'UGX ' + val.toLocaleString(); } } },
            legend: { position: 'top' }
        });
        financeChart.render();
    </script>
@endpush
