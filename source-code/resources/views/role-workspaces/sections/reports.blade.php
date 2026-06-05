@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-bar-chart-box-line me-2 text-primary"></i>{{ __('Financial Reports') }} &mdash; {{ now()->year }}</h4>
                    <small class="text-muted">{{ __('Revenue, expenses and net profit for your workspace') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('This Month Revenue') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($monthRevenue) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#ef4444;">
                        <i class="ri-receipt-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('This Month Expenses') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($monthExpenses) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;">
                        <i class="ri-line-chart-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Yearly Revenue') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($totalRevenue) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;">
                        <i class="ri-bar-chart-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Net Profit YTD') }}</div>
                        <div class="sh-stat-value {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:1.1rem;">UGX {{ number_format($netProfit) }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="ri-line-chart-line me-2 text-primary"></i>{{ __('Monthly Revenue') }}</h6>
                            <div id="revenueChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="ri-receipt-line me-2 text-danger"></i>{{ __('Monthly Expenses') }}</h6>
                            <div id="expenseChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">{{ __('Monthly Summary') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">{{ __('Month') }}</th>
                                    <th>{{ __('Revenue') }}</th>
                                    <th>{{ __('Expenses') }}</th>
                                    <th>{{ __('Net') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($months as $i => $month)
                                @php
                                    $rev = $revenueChart[$i] ?? 0;
                                    $exp = $expenseChart[$i] ?? 0;
                                    $net = $rev - $exp;
                                @endphp
                                <tr>
                                    <td style="padding:10px 16px;"><strong class="small">{{ $month }}</strong></td>
                                    <td class="text-success">UGX {{ number_format($rev) }}</td>
                                    <td class="text-danger">UGX {{ number_format($exp) }}</td>
                                    <td class="{{ $net >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">UGX {{ number_format($net) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
(function() {
    var months   = @json($months);
    var revenue  = @json($revenueChart);
    var expenses = @json($expenseChart);

    if (document.querySelector('#revenueChart')) {
        new ApexCharts(document.querySelector('#revenueChart'), {
            series: [{ name: 'Revenue (UGX)', data: revenue }],
            chart: { type: 'area', height: 220, toolbar: { show: false } },
            colors: ['#10b981'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.05 } },
            xaxis: { categories: months },
            yaxis: { labels: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } },
            tooltip: { y: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } }
        }).render();
    }

    if (document.querySelector('#expenseChart')) {
        new ApexCharts(document.querySelector('#expenseChart'), {
            series: [{ name: 'Expenses (UGX)', data: expenses }],
            chart: { type: 'bar', height: 220, toolbar: { show: false } },
            colors: ['#ef4444'],
            plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
            dataLabels: { enabled: false },
            xaxis: { categories: months },
            yaxis: { labels: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } },
            tooltip: { y: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } }
        }).render();
    }
})();
</script>
@endpush
