@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-money-dollar-box-line me-2 text-primary"></i>{{ __('Finance Manager Dashboard') }}</h4>
                    <small class="text-muted">{{ __('Financial control room') }}</small>
                </div>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;"><i class="ri-money-dollar-circle-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Month Revenue') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($monthRevenue) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#ef4444;"><i class="ri-receipt-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Month Expenses') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($monthExpenses) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;"><i class="ri-line-chart-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Yearly Revenue') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($totalRevenue) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;"><i class="ri-bank-card-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Outstanding') }}</div>
                        <div class="sh-stat-value {{ $outstandingBalance > 0 ? 'text-danger' : '' }}" style="font-size:1.1rem;">UGX {{ number_format($outstandingBalance) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#8b5cf6;"><i class="ri-hourglass-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Pending Expenses') }}</div>
                        <div class="sh-stat-value {{ $pendingExpenses > 0 ? 'text-warning' : '' }}">{{ number_format($pendingExpenses) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:{{ $netProfit >= 0 ? '#10b981' : '#ef4444' }};"><i class="ri-arrow-up-circle-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Net Profit YTD') }}</div>
                        <div class="sh-stat-value {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:1.1rem;">UGX {{ number_format($netProfit) }}</div>
                    </div>
                </div>
            </div>

            {{-- Charts --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="ri-line-chart-line me-2 text-primary"></i>{{ __('Monthly Revenue') }} &mdash; {{ now()->year }}</h6>
                            <div id="fmRevenueChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="ri-receipt-line me-2 text-danger"></i>{{ __('Monthly Expenses') }}</h6>
                            <div id="fmExpenseChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Invoices + Expenses --}}
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Recent Invoices') }}</h5>
                            <a href="{{ route('role.workbench', [$role, 'invoices']) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">{{ __('View All') }}</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" style="font-size:13px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="padding:10px 16px;">{{ __('Invoice No') }}</th>
                                            <th>{{ __('Tenant') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentInvoices as $invoice)
                                        <tr>
                                            <td style="padding:10px 16px;"><strong class="small">{{ $invoice->invoice_no ?? '—' }}</strong></td>
                                            <td class="text-muted small">{{ $invoice->tenant?->user?->name ?? $invoice->tenant?->name ?? '—' }}</td>
                                            <td>UGX {{ number_format($invoice->amount ?? 0) }}</td>
                                            <td>
                                                @if($invoice->status == INVOICE_STATUS_PAID)
                                                    <span class="badge bg-success" style="font-size:10px;">{{ __('Paid') }}</span>
                                                @else
                                                    <span class="badge bg-warning text-dark" style="font-size:10px;">{{ __('Unpaid') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-3 text-muted small">{{ __('No invoices.') }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Recent Expenses') }}</h5>
                            <a href="{{ route('role.workbench', [$role, 'expenses']) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">{{ __('View All') }}</a>
                        </div>
                        <div class="card-body p-0">
                            @forelse($recentExpensesList as $expense)
                            <div class="d-flex align-items-center justify-content-between px-4 py-2 border-bottom">
                                <div>
                                    <div class="fw-semibold small">{{ Str::limit($expense->name ?? '—', 35) }}</div>
                                    <small class="text-muted">{{ $expense->created_at?->format('d M Y') }}</small>
                                </div>
                                <strong class="text-danger small">UGX {{ number_format($expense->total_amount ?? 0) }}</strong>
                            </div>
                            @empty
                            <div class="text-center py-3 text-muted small">{{ __('No expenses.') }}</div>
                            @endforelse
                        </div>
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

    if (document.querySelector('#fmRevenueChart')) {
        new ApexCharts(document.querySelector('#fmRevenueChart'), {
            series: [{ name: 'Revenue (UGX)', data: revenue }],
            chart: { type: 'area', height: 200, toolbar: { show: false } },
            colors: ['#10b981'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.05 } },
            xaxis: { categories: months },
            yaxis: { labels: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } },
            tooltip: { y: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } }
        }).render();
    }
    if (document.querySelector('#fmExpenseChart')) {
        new ApexCharts(document.querySelector('#fmExpenseChart'), {
            series: [{ name: 'Expenses (UGX)', data: expenses }],
            chart: { type: 'bar', height: 200, toolbar: { show: false } },
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
