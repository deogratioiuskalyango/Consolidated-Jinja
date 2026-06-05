@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-briefcase-4-line me-2 text-primary"></i>{{ __('Director Dashboard') }}</h4>
                    <small class="text-muted">{{ __('Executive command center') }}</small>
                </div>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;"><i class="ri-building-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Properties') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalProperties) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;"><i class="ri-home-4-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Occupancy Rate') }}</div>
                        <div class="sh-stat-value">{{ $occupancyRate }}<span class="sh-stat-unit">%</span></div>
                        <div class="sh-stat-sub">{{ number_format($occupiedUnits) }}/{{ number_format($totalUnits) }} {{ __('units') }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#3b82f6;"><i class="ri-money-dollar-circle-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Monthly Revenue') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($monthlyRevenue) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;"><i class="ri-line-chart-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Yearly Revenue') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($yearlyRevenue) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:{{ $netProfit >= 0 ? '#10b981' : '#ef4444' }};"><i class="ri-arrow-up-circle-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Net Profit YTD') }}</div>
                        <div class="sh-stat-value {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:1.1rem;">UGX {{ number_format($netProfit) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#ef4444;"><i class="ri-hourglass-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Pending Approvals') }}</div>
                        <div class="sh-stat-value {{ $pendingApprovals > 0 ? 'text-danger' : '' }}">{{ number_format($pendingApprovals) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#8b5cf6;"><i class="ri-discuss-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Open Resolutions') }}</div>
                        <div class="sh-stat-value {{ $openResolutions > 0 ? 'text-warning' : '' }}">{{ number_format($openResolutions) }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="ri-line-chart-line me-2 text-primary"></i>{{ __('Monthly Revenue') }} &mdash; {{ now()->year }}</h6>
                            <div id="directorRevenueChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Upcoming Meetings') }}</h5>
                            <a href="{{ route('role.workbench', [$role, 'meetings']) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">{{ __('All') }}</a>
                        </div>
                        <div class="card-body p-0" style="max-height:320px;overflow-y:auto;">
                            @forelse($upcomingMeetings as $meeting)
                            <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom">
                                <div style="min-width:44px;height:44px;border-radius:8px;background:#ede9fe;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;line-height:1.2;flex-shrink:0;">
                                    <strong style="font-size:16px;color:#6366f1;">{{ $meeting->scheduled_at ? \Carbon\Carbon::parse($meeting->scheduled_at)->format('d') : '?' }}</strong>
                                    <small style="font-size:9px;text-transform:uppercase;color:#6366f1;">{{ $meeting->scheduled_at ? \Carbon\Carbon::parse($meeting->scheduled_at)->format('M') : '' }}</small>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold small text-truncate">{{ $meeting->title ?? '—' }}</div>
                                    <div class="text-muted" style="font-size:11px;">
                                        <i class="ri-time-line me-1"></i>{{ $meeting->scheduled_at ? \Carbon\Carbon::parse($meeting->scheduled_at)->format('H:i') : __('TBD') }}
                                        @if($meeting->location) &bull; {{ Str::limit($meeting->location, 25) }} @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted small">
                                <i class="ri-calendar-check-line d-block fs-4 mb-1"></i>
                                {{ __('No upcoming meetings.') }}
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Recent Approvals') }}</h5>
                    <a href="{{ route('role.workbench', [$role, 'approvals']) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:13px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">{{ __('Title') }}</th>
                                    <th>{{ __('Requested By') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentApprovals as $approval)
                                @php
                                    $statusMap = [
                                        APPROVAL_STATUS_PENDING  => ['label' => 'Pending',  'class' => 'bg-warning text-dark'],
                                        APPROVAL_STATUS_APPROVED => ['label' => 'Approved',  'class' => 'bg-success'],
                                        APPROVAL_STATUS_REJECTED => ['label' => 'Rejected',  'class' => 'bg-danger'],
                                        APPROVAL_STATUS_EXPIRED  => ['label' => 'Expired',   'class' => 'bg-secondary'],
                                    ];
                                    $st = $statusMap[$approval->status] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                                @endphp
                                <tr>
                                    <td style="padding:10px 16px;">{{ Str::limit($approval->title ?? '—', 40) }}</td>
                                    <td class="text-muted small">{{ $approval->requestedBy?->name ?? '—' }}</td>
                                    <td>UGX {{ number_format($approval->amount ?? 0) }}</td>
                                    <td><span class="badge {{ $st['class'] }}" style="font-size:10px;">{{ __($st['label']) }}</span></td>
                                    <td class="text-muted small">{{ $approval->created_at?->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted small">{{ __('No recent approvals.') }}</td>
                                </tr>
                                @endforelse
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
    var months  = @json($months);
    var revenue = @json($monthlyRevenueChart);
    if (document.querySelector('#directorRevenueChart')) {
        new ApexCharts(document.querySelector('#directorRevenueChart'), {
            series: [{ name: 'Revenue (UGX)', data: revenue }],
            chart: { type: 'area', height: 220, toolbar: { show: false } },
            colors: ['#6366f1'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.05 } },
            xaxis: { categories: months },
            yaxis: { labels: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } },
            tooltip: { y: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } }
        }).render();
    }
})();
</script>
@endpush
