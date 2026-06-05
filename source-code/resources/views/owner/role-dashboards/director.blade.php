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
                                        {{ __('Director Dashboard') }}
                                        <span class="badge bg-primary text-white ms-2 font-13 fw-normal">{{ __('Executive View') }}</span>
                                    </h2>
                                    <p class="mb-0">{{ __('Welcome back') }}, {{ auth()->user()->name }} &mdash; {{ now()->format('l, d F Y') }}</p>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li class="breadcrumb-item active">{{ __('Director') }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 1: 8 KPI Cards (2 rows of 4) --}}
                    <div class="row">
                        {{-- Properties --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-building-line font-22" style="color:#3b82f6;"></i>
                                </div>
                                <p class="mt-2">{{ __('Properties') }}</p>
                                <h2 class="mt-1">{{ number_format($totalProperties, 0) }}</h2>
                            </div>
                        </div>
                        {{-- Units --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-home-4-line font-22" style="color:#8b5cf6;"></i>
                                </div>
                                <p class="mt-2">{{ __('Total Units') }}</p>
                                <h2 class="mt-1">{{ number_format($totalUnits, 0) }}</h2>
                            </div>
                        </div>
                        {{-- Occupancy Rate --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    @if ($occupancyRate >= 80)
                                        <i class="ri-checkbox-circle-line font-22 green-color"></i>
                                    @elseif ($occupancyRate >= 50)
                                        <i class="ri-checkbox-circle-line font-22 orange-color"></i>
                                    @else
                                        <i class="ri-checkbox-circle-line font-22" style="color:#e53e3e;"></i>
                                    @endif
                                </div>
                                <p class="mt-2">{{ __('Occupancy Rate') }}</p>
                                <h2 class="mt-1">
                                    {{ $occupancyRate }}%
                                    <span class="badge ms-1 font-11
                                        @if ($occupancyRate >= 80) bg-success
                                        @elseif ($occupancyRate >= 50) bg-warning text-dark
                                        @else bg-danger
                                        @endif">
                                        {{ $occupiedUnits }}/{{ $totalUnits }}
                                    </span>
                                </h2>
                            </div>
                        </div>
                        {{-- Monthly Revenue --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-money-dollar-circle-line font-22 green-color"></i>
                                </div>
                                <p class="mt-2">{{ __('Monthly Revenue') }}</p>
                                <h2 class="mt-1" style="font-size:1rem;">UGX {{ number_format($monthlyRevenue, 0) }}</h2>
                            </div>
                        </div>
                        {{-- Yearly Revenue --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-line-chart-line font-22" style="color:#3b82f6;"></i>
                                </div>
                                <p class="mt-2">{{ __('Yearly Revenue') }}</p>
                                <h2 class="mt-1" style="font-size:1rem;">UGX {{ number_format($yearlyRevenue, 0) }}</h2>
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
                        {{-- Pending Approvals --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <a href="#" class="text-decoration-none">
                                <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                    <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                        <i class="ri-time-line font-22 orange-color"></i>
                                    </div>
                                    <p class="mt-2 text-dark">{{ __('Pending Approvals') }}</p>
                                    <h2 class="mt-1 orange-color">{{ number_format($pendingApprovals, 0) }}</h2>
                                </div>
                            </a>
                        </div>
                        {{-- Open Resolutions --}}
                        <div class="col-6 col-sm-6 col-lg-3 col-xl-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-discuss-line font-22" style="color:#0d9488;"></i>
                                </div>
                                <p class="mt-2">{{ __('Open Resolutions') }}</p>
                                <h2 class="mt-1" style="color:#0d9488;">{{ number_format($openResolutions, 0) }}</h2>
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
                                        <i class="ri-check-double-line me-1"></i>{{ __('Approve Financial Requests') }}
                                    </a>
                                    <a href="#" class="btn btn-outline-secondary btn-sm px-3 py-2 font-13">
                                        <i class="ri-discuss-line me-1"></i>{{ __('View Resolutions') }}
                                    </a>
                                    <a href="#" class="btn btn-outline-secondary btn-sm px-3 py-2 font-13">
                                        <i class="ri-calendar-event-line me-1"></i>{{ __('Board Meetings') }}
                                    </a>
                                    <a href="#" class="btn btn-outline-secondary btn-sm px-3 py-2 font-13">
                                        <i class="ri-file-chart-line me-1"></i>{{ __('Executive Report') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Revenue Chart + Upcoming Meetings --}}
                    <div class="row">
                        {{-- Revenue Chart --}}
                        <div class="col-lg-8">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h4 class="mb-0"><i class="ri-bar-chart-2-line me-2"></i>{{ __('Annual Revenue Overview') }}</h4>
                                    <span class="font-13 text-muted">{{ date('Y') }}</span>
                                </div>
                                <div id="directorRevenueChart"></div>
                            </div>
                        </div>
                        {{-- Upcoming Meetings --}}
                        <div class="col-lg-4">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25 h-100">
                                <h4 class="mb-20"><i class="ri-calendar-event-line me-2"></i>{{ __('Upcoming Meetings') }}</h4>
                                @forelse ($upcomingMeetings as $meeting)
                                    <div class="d-flex align-items-start py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="bg-white radius-4 p-2 text-center" style="min-width:48px;">
                                                <div class="font-11 text-muted">{{ $meeting->scheduled_at ? $meeting->scheduled_at->format('M') : '—' }}</div>
                                                <div class="font-18 fw-bold primary-color">{{ $meeting->scheduled_at ? $meeting->scheduled_at->format('d') : '—' }}</div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $meeting->title ?? '—' }}</h6>
                                            <p class="font-12 text-muted mb-1">
                                                <i class="ri-time-line me-1"></i>
                                                {{ $meeting->scheduled_at ? $meeting->scheduled_at->format('H:i') : '—' }}
                                            </p>
                                            @php
                                                $statusClass = match($meeting->status ?? '') {
                                                    MEETING_STATUS_SCHEDULED => 'bg-primary',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} text-white font-11">{{ __('Scheduled') }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">
                                        <i class="ri-calendar-check-line font-30 d-block mb-2"></i>
                                        <p>{{ __('No upcoming meetings') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: Recent Approvals Table + Smart Alerts --}}
                    <div class="row">
                        {{-- Recent Financial Approvals --}}
                        <div class="col-lg-7">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="d-flex align-items-center justify-content-between mb-20">
                                    <h4 class="mb-0"><i class="ri-secure-payment-line me-2"></i>{{ __('Recent Financial Approvals') }}</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table theme-border">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th>{{ __('Requested By') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($recentApprovals as $approval)
                                                <tr>
                                                    <td>{{ Str::limit($approval->title ?? '—', 30) }}</td>
                                                    <td>UGX {{ number_format($approval->amount ?? 0, 0) }}</td>
                                                    <td>{{ $approval->requested_by ?? $approval->requestedBy?->name ?? '—' }}</td>
                                                    <td>
                                                        @php
                                                            $aBadge = match((int)($approval->status ?? -1)) {
                                                                defined('APPROVAL_STATUS_PENDING') ? APPROVAL_STATUS_PENDING : 0 => 'bg-warning text-dark',
                                                                defined('APPROVAL_STATUS_APPROVED') ? APPROVAL_STATUS_APPROVED : 1 => 'bg-success',
                                                                defined('APPROVAL_STATUS_REJECTED') ? APPROVAL_STATUS_REJECTED : 2 => 'bg-danger',
                                                                default => 'bg-secondary',
                                                            };
                                                            $aLabel = match((int)($approval->status ?? -1)) {
                                                                defined('APPROVAL_STATUS_PENDING') ? APPROVAL_STATUS_PENDING : 0 => __('Pending'),
                                                                defined('APPROVAL_STATUS_APPROVED') ? APPROVAL_STATUS_APPROVED : 1 => __('Approved'),
                                                                defined('APPROVAL_STATUS_REJECTED') ? APPROVAL_STATUS_REJECTED : 2 => __('Rejected'),
                                                                default => __('Unknown'),
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $aBadge }} text-white">{{ $aLabel }}</span>
                                                    </td>
                                                    <td>{{ $approval->created_at ? $approval->created_at->format('d M Y') : '—' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">{{ __('No approvals found') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{-- Smart Alerts --}}
                        <div class="col-lg-5">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <h4 class="mb-20"><i class="ri-alert-line me-2"></i>{{ __('Smart Alerts') }}</h4>
                                {{-- Pending approvals alert --}}
                                <div class="alert mb-3" style="background:#fff7ed;border:1px solid #fdba74;color:#9a3412;border-radius:6px;">
                                    <i class="ri-time-line me-2"></i>
                                    <strong>{{ $pendingApprovals }}</strong> {{ __('approval(s) awaiting your review') }}
                                </div>
                                {{-- Open resolutions alert --}}
                                <div class="alert mb-3" style="background:#f0fdfa;border:1px solid #5eead4;color:#134e4a;border-radius:6px;">
                                    <i class="ri-discuss-line me-2"></i>
                                    <strong>{{ $openResolutions }}</strong> {{ __('resolution(s) open for voting') }}
                                </div>
                                {{-- Next meeting --}}
                                <div class="alert mb-0" style="background:#eff6ff;border:1px solid #93c5fd;color:#1e3a5f;border-radius:6px;">
                                    <i class="ri-calendar-event-line me-2"></i>
                                    {{ __('Next meeting:') }}
                                    <strong>{{ $upcomingMeetings->first()?->title ?? __('None scheduled') }}</strong>
                                    @if ($upcomingMeetings->first()?->scheduled_at)
                                        &mdash; {{ $upcomingMeetings->first()->scheduled_at->format('d M Y H:i') }}
                                    @endif
                                </div>
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
        const DIRECTOR_MONTHS = @json($months);
        const DIRECTOR_MONTHLY_REVENUE = @json($monthlyRevenueChart);
    </script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        var directorRevenueChart = new ApexCharts(document.querySelector("#directorRevenueChart"), {
            series: [{ name: 'Revenue (UGX)', data: DIRECTOR_MONTHLY_REVENUE }],
            chart: { type: 'area', height: 280, toolbar: { show: false } },
            colors: ['#6366f1'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.1 } },
            xaxis: { categories: DIRECTOR_MONTHS },
            yaxis: { labels: { formatter: function(val) { return 'UGX ' + val.toLocaleString(); } } },
            tooltip: { y: { formatter: function(val) { return 'UGX ' + val.toLocaleString(); } } }
        });
        directorRevenueChart.render();
    </script>
@endpush
