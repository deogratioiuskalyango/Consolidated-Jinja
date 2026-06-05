@extends('accountant.layouts.app')
@php $navDashboardActiveClass = 'active mm-active'; @endphp
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page Title --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex flex-column flex-sm-row align-items-sm-center justify-content-between g-20">
                            <div class="page-title-left">
                                <h2 class="mb-sm-0">{{ __('Financial Dashboard') }}</h2>
                                <p class="text-muted mb-0 mt-1">
                                    {{ __('Welcome back,') }}
                                    <strong>{{ auth()->user()->name }}</strong>
                                    &mdash; {{ __('Real Estate Accounting Overview') }}
                                </p>
                            </div>
                            <div class="page-title-right mt-2 mt-sm-0">
                                <a href="{{ route('accountant.collections.create') }}" class="btn btn-primary me-2">
                                    <i class="ri-add-line me-1"></i> {{ __('Record Payment') }}
                                </a>
                                <a href="{{ route('accountant.reports.index') }}" class="btn btn-outline-primary me-2">
                                    <i class="ri-file-chart-line me-1"></i> {{ __('Reports') }}
                                </a>
                                <a href="{{ route('accountant.reconciliation.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-scales-3-line me-1"></i> {{ __('Reconciliation') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Row --}}
                <div class="row g-3 mt-2">

                    {{-- Today's Collections --}}
                    <div class="col-xl-2 col-sm-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 avatar-md bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-money-dollar-circle-line text-success fs-4"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">{{ __("Today's Collections") }}</p>
                                    <h4 class="mb-0">{{ number_format($todayCollections, 0) }}</h4>
                                    <small class="text-muted">UGX</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- This Week --}}
                    <div class="col-xl-2 col-sm-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 avatar-md bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-calendar-check-line text-primary fs-4"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">{{ __('This Week') }}</p>
                                    <h4 class="mb-0">{{ number_format($weekCollections, 0) }}</h4>
                                    <small class="text-muted">UGX</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- This Month --}}
                    <div class="col-xl-2 col-sm-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 avatar-md bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(111,66,193,.1);">
                                    <i class="ri-bar-chart-line fs-4" style="color:#6f42c1;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">{{ __('This Month') }}</p>
                                    <h4 class="mb-0">{{ number_format($monthCollections, 0) }}</h4>
                                    <small class="text-muted">UGX</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pending Expenses --}}
                    <div class="col-xl-2 col-sm-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 avatar-md bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-receipt-line text-warning fs-4"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">{{ __('Pending Expenses') }}</p>
                                    <h4 class="mb-0">{{ $pendingExpenses }}</h4>
                                    <small class="text-muted">{{ __('items') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total Tenants --}}
                    <div class="col-xl-2 col-sm-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 avatar-md bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(32,201,151,.1);">
                                    <i class="ri-team-line fs-4" style="color:#20c997;"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">{{ __('Total Tenants') }}</p>
                                    <h4 class="mb-0">{{ $totalTenants }}</h4>
                                    <small class="text-muted">{{ __('active') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Overdue Balances --}}
                    <div class="col-xl-2 col-sm-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 avatar-md bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ri-alert-line text-danger fs-4"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 small">{{ __('Overdue Balances') }}</p>
                                    <h4 class="mb-0">{{ $overdueBalances }}</h4>
                                    <small class="text-muted">{{ __('tenants') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- end stats row --}}

                {{-- Analytics Charts Row --}}
                <div class="row mb-4 mt-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0"><i class="ri-bar-chart-2-line me-2 text-primary"></i>Collections vs Expenses — {{ now()->year }}</h6>
                                    <span class="badge bg-light text-dark">Annual</span>
                                </div>
                                <div id="collectionsVsExpensesChart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="card-title mb-0"><i class="ri-pie-chart-2-line me-2 text-success"></i>By Payment Method</h6>
                                </div>
                                <div id="paymentMethodPieChart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top Properties Annual Row --}}
                @if($topPropertiesAnnual->count())
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title mb-3"><i class="ri-building-4-line me-2 text-warning"></i>Top Properties by Collections ({{ now()->year }})</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead class="table-light"><tr><th>#</th><th>Property</th><th class="text-end">Total Collected (UGX)</th></tr></thead>
                                        <tbody>
                                            @foreach($topPropertiesAnnual as $i => $prop)
                                            <tr>
                                                <td>{{ $i+1 }}</td>
                                                <td>{{ $prop->property->name ?? 'Property #'.$prop->property_id }}</td>
                                                <td class="text-end fw-bold">{{ number_format($prop->total, 0) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Collections & Expenses Tables --}}
                <div class="row mt-4">

                    {{-- Recent Rent Collections --}}
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">
                                    <i class="ri-money-dollar-circle-line me-2 text-success"></i>{{ __('Recent Rent Collections') }}
                                </h5>
                                <a href="{{ route('accountant.collections.index') }}" class="btn btn-sm btn-outline-success">
                                    {{ __('View All Collections') }}
                                </a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('Receipt #') }}</th>
                                                <th>{{ __('Tenant') }}</th>
                                                <th>{{ __('Property') }}</th>
                                                <th>{{ __('Amount (UGX)') }}</th>
                                                <th>{{ __('Method') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentCollections as $c)
                                                @php
                                                    $collectionStatusMap = [
                                                        'Confirmed' => 'success',
                                                        'confirmed' => 'success',
                                                        'Reversed'  => 'danger',
                                                        'reversed'  => 'danger',
                                                        'Pending'   => 'warning',
                                                        'pending'   => 'warning',
                                                    ];
                                                    $collectionBadge = $collectionStatusMap[$c->status] ?? 'secondary';
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('accountant.collections.show', $c) }}" class="fw-semibold text-primary">
                                                            {{ $c->receipt_number ?? '#' . $c->id }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $c->tenant->full_name ?? ($c->tenant->name ?? '-') }}</td>
                                                    <td>{{ $c->property->name ?? ($c->unit->property->name ?? '-') }}</td>
                                                    <td>{{ number_format($c->amount, 0) }}</td>
                                                    <td>{{ $c->payment_method ?? '-' }}</td>
                                                    <td>{{ $c->payment_date ? \Carbon\Carbon::parse($c->payment_date)->format('M d, Y') : ($c->created_at ? $c->created_at->format('M d, Y') : '-') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $collectionBadge }}">
                                                            {{ ucfirst($c->status ?? 'Pending') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">
                                                        <i class="ri-inbox-line d-block fs-3 mb-1"></i>
                                                        {{ __('No recent collections found.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if($recentCollections->isNotEmpty())
                            <div class="card-footer bg-transparent text-end">
                                <a href="{{ route('accountant.collections.index') }}" class="text-success small fw-semibold">
                                    {{ __('View All Collections') }} <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Recent Expenses --}}
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">
                                    <i class="ri-receipt-line me-2 text-warning"></i>{{ __('Recent Expenses') }}
                                </h5>
                                <a href="{{ route('accountant.expenses.index') }}" class="btn btn-sm btn-outline-warning">
                                    {{ __('View All Expenses') }}
                                </a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('Reference') }}</th>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Category') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentExpenses as $e)
                                                @php
                                                    $expenseStatusMap = [
                                                        'Approved'  => 'success',
                                                        'approved'  => 'success',
                                                        'Pending'   => 'warning',
                                                        'pending'   => 'warning',
                                                        'Rejected'  => 'danger',
                                                        'rejected'  => 'danger',
                                                        'Paid'      => 'info',
                                                        'paid'      => 'info',
                                                    ];
                                                    $expenseBadge = $expenseStatusMap[$e->status] ?? 'secondary';
                                                @endphp
                                                <tr>
                                                    <td class="text-muted small">{{ $e->reference ?? ('#' . $e->id) }}</td>
                                                    <td>{{ $e->title ?? $e->name ?? '-' }}</td>
                                                    <td>{{ $e->category ?? ($e->expense_category ?? '-') }}</td>
                                                    <td>{{ number_format($e->amount ?? 0, 0) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $expenseBadge }}">
                                                            {{ ucfirst($e->status ?? 'Pending') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        <i class="ri-inbox-line d-block fs-3 mb-1"></i>
                                                        {{ __('No recent expenses found.') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if($recentExpenses->isNotEmpty())
                            <div class="card-footer bg-transparent text-end">
                                <a href="{{ route('accountant.expenses.index') }}" class="text-warning small fw-semibold">
                                    {{ __('View All Expenses') }} <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>

                </div>{{-- end collections & expenses row --}}

                {{-- Top Properties by Collections --}}
                @if(!empty($topProperties) && $topProperties->isNotEmpty())
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">
                                    <i class="ri-building-line me-2 text-primary"></i>{{ __('Top Properties by Collections') }}
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('Property') }}</th>
                                                <th>{{ __('Total Collected (UGX)') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topProperties as $index => $tp)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $tp->property->name ?? ('Property #' . $tp->property_id) }}</td>
                                                    <td class="fw-semibold">{{ number_format($tp->total ?? 0, 0) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>{{-- end page-content-wrapper --}}
        </div>{{-- end container-fluid --}}
    </div>{{-- end page-content --}}
</div>{{-- end main-content --}}
@endsection

@push('style')
{{-- Additional styles for accountant dashboard if needed --}}
@endpush

@push('script')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
(function() {
    var months      = @json($chartMonths);
    var collections = @json($monthlyCollections);
    var expenses    = @json($monthlyExpenses);
    var pieLabels   = @json($pieLabels);
    var pieValues   = @json($pieValues);

    // Collections vs Expenses bar chart
    if (document.querySelector('#collectionsVsExpensesChart')) {
        new ApexCharts(document.querySelector('#collectionsVsExpensesChart'), {
            series: [
                { name: 'Collections (UGX)', data: collections },
                { name: 'Expenses (UGX)',    data: expenses }
            ],
            chart: { type: 'bar', height: 280, toolbar: { show: false } },
            colors: ['#10b981', '#ef4444'],
            plotOptions: { bar: { columnWidth: '55%', borderRadius: 4 } },
            dataLabels: { enabled: false },
            xaxis: { categories: months },
            yaxis: { labels: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } },
            legend: { position: 'top' },
            tooltip: { y: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } }
        }).render();
    }

    // Payment method pie chart
    if (document.querySelector('#paymentMethodPieChart') && pieLabels.length) {
        new ApexCharts(document.querySelector('#paymentMethodPieChart'), {
            series: pieValues,
            chart: { type: 'donut', height: 280 },
            labels: pieLabels,
            colors: ['#6366f1','#10b981','#f59e0b','#ef4444','#3b82f6','#8b5cf6'],
            legend: { position: 'bottom', fontSize: '12px' },
            plotOptions: { pie: { donut: { size: '65%' } } },
            dataLabels: { formatter: function(val) { return val.toFixed(1) + '%'; } }
        }).render();
    }
})();
</script>
@endpush
