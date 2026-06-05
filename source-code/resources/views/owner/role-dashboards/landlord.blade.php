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
                                        {{ __('Landlord Dashboard') }}
                                        <span class="badge bg-warning text-dark ms-2 font-13 fw-normal">{{ __('Property View') }}</span>
                                    </h2>
                                    <p class="mb-0">{{ __('Welcome back') }}, {{ auth()->user()->name }} &mdash; {{ now()->format('l, d F Y') }}</p>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li class="breadcrumb-item active">{{ __('Landlord') }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 1: 6 KPI Cards --}}
                    <div class="row">
                        {{-- Properties --}}
                        <div class="col-6 col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-building-line font-22" style="color:#3b82f6;"></i>
                                </div>
                                <p class="mt-2">{{ __('Properties') }}</p>
                                <h2 class="mt-1">{{ number_format($totalProperties, 0) }}</h2>
                            </div>
                        </div>
                        {{-- Units --}}
                        <div class="col-6 col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-home-4-line font-22" style="color:#8b5cf6;"></i>
                                </div>
                                <p class="mt-2">{{ __('Total Units') }}</p>
                                <h2 class="mt-1">{{ number_format($totalUnits, 0) }}</h2>
                            </div>
                        </div>
                        {{-- Active Tenants --}}
                        <div class="col-6 col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-group-line font-22 orange-color"></i>
                                </div>
                                <p class="mt-2">{{ __('Active Tenants') }}</p>
                                <h2 class="mt-1">{{ number_format($totalTenants, 0) }}</h2>
                            </div>
                        </div>
                        {{-- Monthly Income --}}
                        <div class="col-6 col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-money-dollar-circle-line font-22 green-color"></i>
                                </div>
                                <p class="mt-2">{{ __('Monthly Income') }}</p>
                                <h2 class="mt-1 green-color" style="font-size:0.95rem;">UGX {{ number_format($monthlyIncome, 0) }}</h2>
                            </div>
                        </div>
                        {{-- Yearly Income --}}
                        <div class="col-6 col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-line-chart-line font-22" style="color:#3b82f6;"></i>
                                </div>
                                <p class="mt-2">{{ __('Yearly Income') }}</p>
                                <h2 class="mt-1" style="color:#3b82f6;font-size:0.95rem;">UGX {{ number_format($yearlyIncome, 0) }}</h2>
                            </div>
                        </div>
                        {{-- Pending Payments --}}
                        <div class="col-6 col-sm-6 col-lg-4 col-xl-2">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-file-warning-line font-22" style="color:#dc2626;"></i>
                                </div>
                                <p class="mt-2">{{ __('Pending Payments') }}</p>
                                <h2 class="mt-1" style="color:#dc2626;">{{ number_format($pendingPayments, 0) }}</h2>
                            </div>
                        </div>
                    </div>
                    {{-- End KPI Cards --}}

                    {{-- Net Earnings summary bar --}}
                    @php $netEarnings = $yearlyIncome - $maintenanceCost; @endphp
                    <div class="row mb-25">
                        <div class="col-12">
                            <div class="bg-off-white theme-border radius-4 p-20">
                                <div class="row align-items-center text-center text-md-start">
                                    <div class="col-md-4 border-end mb-3 mb-md-0">
                                        <p class="font-13 text-muted mb-1">{{ __('Yearly Income') }}</p>
                                        <h4 class="green-color mb-0">UGX {{ number_format($yearlyIncome, 0) }}</h4>
                                    </div>
                                    <div class="col-md-4 border-end mb-3 mb-md-0">
                                        <p class="font-13 text-muted mb-1">{{ __('Maintenance / Expenses') }}</p>
                                        <h4 style="color:#e53e3e;" class="mb-0">UGX {{ number_format($maintenanceCost, 0) }}</h4>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="font-13 text-muted mb-1">{{ __('Net Earnings YTD') }}</p>
                                        <h4 class="{{ $netEarnings >= 0 ? 'green-color' : '' }} mb-0" style="{{ $netEarnings < 0 ? 'color:#e53e3e;' : '' }}">
                                            UGX {{ number_format($netEarnings, 0) }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Properties Grid --}}
                    <div class="row mb-10">
                        <div class="col-12">
                            <h4 class="mb-20"><i class="ri-building-2-line me-2"></i>{{ __('My Properties') }}</h4>
                        </div>
                        @forelse ($properties as $property)
                            <div class="col-md-6 col-lg-4">
                                <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h5 class="mb-0 theme-text-color">{{ $property->name }}</h5>
                                        <span class="badge bg-primary text-white font-11">
                                            {{ $property->propertyUnits->count() }} {{ __('Units') }}
                                        </span>
                                    </div>
                                    <p class="font-13 text-muted mb-2">
                                        <i class="ri-map-pin-line me-1"></i>{{ Str::limit($property->address ?? '—', 40) }}
                                    </p>
                                    <div class="d-flex align-items-center justify-content-between">
                                        @php
                                            $occupied = $property->propertyUnits->filter(fn($unit) => $unit->activeTenant)->count();
                                            $total    = $property->propertyUnits->count();
                                            $vacancyPct = $total > 0 ? round(($occupied / $total) * 100) : 0;
                                        @endphp
                                        <div>
                                            <span class="font-12 text-muted">{{ __('Occupied:') }}</span>
                                            <strong class="ms-1">{{ $occupied }}/{{ $total }}</strong>
                                        </div>
                                        <span class="badge {{ $vacancyPct >= 80 ? 'bg-success' : ($vacancyPct >= 50 ? 'bg-warning text-dark' : 'bg-danger') }} font-11">
                                            {{ $vacancyPct }}%
                                        </span>
                                    </div>
                                    <div class="mt-2 progress" style="height:6px;border-radius:3px;">
                                        <div class="progress-bar {{ $vacancyPct >= 80 ? 'bg-success' : ($vacancyPct >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                             style="width:{{ $vacancyPct }}%;"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted text-center">{{ __('No properties found') }}</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Row 2: Monthly Income Chart + Recent Payments --}}
                    <div class="row">
                        {{-- Income Chart --}}
                        <div class="col-lg-6">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h4 class="mb-0"><i class="ri-bar-chart-line me-2"></i>{{ __('Monthly Income') }}</h4>
                                    <span class="font-13 text-muted">{{ date('Y') }}</span>
                                </div>
                                <div id="landlordIncomeChart"></div>
                            </div>
                        </div>
                        {{-- Recent Payments --}}
                        <div class="col-lg-6">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="d-flex align-items-center justify-content-between mb-20">
                                    <h4 class="mb-0"><i class="ri-money-cny-circle-line me-2"></i>{{ __('Recent Payments') }}</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table theme-border">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Tenant') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th>{{ __('Date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($recentPayments as $payment)
                                                <tr>
                                                    <td>{{ $payment->tenant?->name ?? '—' }}</td>
                                                    <td><strong class="green-color">UGX {{ number_format($payment->amount ?? 0, 0) }}</strong></td>
                                                    <td>{{ $payment->created_at ? $payment->created_at->format('d M Y') : '—' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">{{ __('No payments found') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- End Row 2 --}}

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const LANDLORD_MONTHS = @json($months);
        const LANDLORD_INCOME_CHART = @json($incomeChart);
    </script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        var landlordChart = new ApexCharts(document.querySelector("#landlordIncomeChart"), {
            series: [{ name: 'Income (UGX)', data: LANDLORD_INCOME_CHART }],
            chart: { type: 'bar', height: 260, toolbar: { show: false } },
            colors: ['#f59e0b'],
            plotOptions: { bar: { columnWidth: '55%', borderRadius: 4 } },
            dataLabels: { enabled: false },
            xaxis: { categories: LANDLORD_MONTHS },
            yaxis: { labels: { formatter: function(val) { return 'UGX ' + val.toLocaleString(); } } },
            tooltip: { y: { formatter: function(val) { return 'UGX ' + val.toLocaleString(); } } }
        });
        landlordChart.render();
    </script>
@endpush
