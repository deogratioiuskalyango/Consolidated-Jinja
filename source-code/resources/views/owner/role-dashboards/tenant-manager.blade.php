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
                                        {{ __('Tenant Manager Dashboard') }}
                                        <span class="badge bg-primary text-white ms-2 font-13 fw-normal">{{ __('Tenant Oversight') }}</span>
                                    </h2>
                                    <p class="mb-0">{{ now()->format('l, d F Y') }}</p>
                                </div>
                                <div class="page-title-right d-flex align-items-center gap-2 flex-wrap">
                                    <a href="{{ route('owner.tenant.create') ?? '#' }}" class="btn btn-primary btn-sm">
                                        <i class="ri-user-add-line me-1"></i>{{ __('Add Tenant') }}
                                    </a>
                                    <a href="{{ route('owner.tenant.index') ?? '#' }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="ri-file-list-3-line me-1"></i>{{ __('View All Leases') }}
                                    </a>
                                    <a href="{{ route('owner.ticket.index') ?? '#' }}" class="btn btn-outline-danger btn-sm">
                                        <i class="ri-customer-service-2-line me-1"></i>{{ __('Handle Tickets') }}
                                    </a>
                                    <a href="{{ route('owner.noticeboard.index') ?? '#' }}" class="btn btn-outline-info btn-sm">
                                        <i class="ri-megaphone-line me-1"></i>{{ __('Send Notice') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 1: 6 KPI Cards --}}
                    <div class="row">

                        {{-- Total Tenants --}}
                        <div class="col-xl-2 col-md-4 col-6">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-team-line font-22" style="color:#3182ce;"></i>
                                </div>
                                <p class="mt-2">{{ __('Total Tenants') }}</p>
                                <h2 class="mt-1">{{ number_format($totalTenants, 0) }}</h2>
                            </div>
                        </div>

                        {{-- Active Tenants --}}
                        <div class="col-xl-2 col-md-4 col-6">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-user-check-line font-22 green-color"></i>
                                </div>
                                <p class="mt-2">{{ __('Active Tenants') }}</p>
                                <h2 class="mt-1">{{ number_format($activeTenants, 0) }}</h2>
                            </div>
                        </div>

                        {{-- Vacant Units --}}
                        <div class="col-xl-2 col-md-4 col-6">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25 {{ $vacantUnits > 0 ? 'border-warning' : '' }}">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-home-line font-22 orange-color"></i>
                                </div>
                                <p class="mt-2">{{ __('Vacant Units') }}</p>
                                <h2 class="mt-1">{{ number_format($vacantUnits, 0) }}</h2>
                                @if ($vacantUnits > 0)
                                    <small class="text-warning"><i class="ri-alert-line me-1"></i>{{ __('Needs attention') }}</small>
                                @endif
                            </div>
                        </div>

                        {{-- Expiring Leases --}}
                        <div class="col-xl-2 col-md-4 col-6">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25 {{ $expiringLeases->count() > 0 ? 'border-warning' : '' }}">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-calendar-close-line font-22" style="color:#d69e2e;"></i>
                                </div>
                                <p class="mt-2">{{ __('Expiring Leases') }}</p>
                                <h2 class="mt-1">{{ number_format($expiringLeases->count(), 0) }}</h2>
                                <small class="text-muted">{{ __('within 30 days') }}</small>
                            </div>
                        </div>

                        {{-- Open Tickets --}}
                        <div class="col-xl-2 col-md-4 col-6">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25 {{ $openTickets > 5 ? 'border-danger' : '' }}">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-ticket-line font-22" style="color:#e53e3e;"></i>
                                </div>
                                <p class="mt-2">{{ __('Open Tickets') }}</p>
                                <h2 class="mt-1">{{ number_format($openTickets, 0) }}</h2>
                            </div>
                        </div>

                        {{-- Pending Invoices --}}
                        <div class="col-xl-2 col-md-4 col-6">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-bill-line font-22" style="color:#805ad5;"></i>
                                </div>
                                <p class="mt-2">{{ __('Pending Invoices') }}</p>
                                <h2 class="mt-1">{{ number_format($pendingInvoices, 0) }}</h2>
                            </div>
                        </div>

                    </div>
                    {{-- End KPI Cards --}}

                    {{-- Occupancy Progress Bar --}}
                    @if ($totalUnits > 0)
                    @php $occupancyPct = round(($occupiedUnits / $totalUnits) * 100); @endphp
                    <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0"><i class="ri-building-line me-2"></i>{{ __('Occupancy Rate') }}</h6>
                            <span class="font-14 fw-semibold">{{ $occupiedUnits }} / {{ $totalUnits }} {{ __('units') }} &mdash; {{ $occupancyPct }}%</span>
                        </div>
                        <div class="progress" style="height:14px;border-radius:8px;">
                            <div class="progress-bar {{ $occupancyPct >= 80 ? 'bg-success' : ($occupancyPct >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                 role="progressbar"
                                 style="width: {{ $occupancyPct }}%;"
                                 aria-valuenow="{{ $occupancyPct }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $occupancyPct }}%
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Row 2: Recent Tenants + Expiring Leases --}}
                    <div class="row">

                        {{-- Recent Tenants Table --}}
                        <div class="col-lg-7">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="d-flex align-items-center justify-content-between mb-20">
                                    <h4 class="mb-0"><i class="ri-team-line me-2"></i>{{ __('Recent Tenants') }}</h4>
                                    <a href="{{ route('owner.tenant.index') ?? '#' }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table theme-border">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Unit / Property') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Move-in Date') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($recentTenants as $tenant)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="avatar-xs bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:12px;">
                                                                {{ strtoupper(substr($tenant->name ?? 'T', 0, 1)) }}
                                                            </div>
                                                            <span>{{ $tenant->name ?? ($tenant->user?->name ?? '—') }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="font-13">{{ $tenant->unit?->name ?? '—' }}</span><br>
                                                        <small class="text-muted">{{ $tenant->property?->name ?? '' }}</small>
                                                    </td>
                                                    <td>
                                                        @if ($tenant->status == TENANT_STATUS_ACTIVE)
                                                            <span class="badge bg-success text-white">{{ __('Active') }}</span>
                                                        @else
                                                            <span class="badge bg-secondary text-white">{{ __('Inactive') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $tenant->created_at ? $tenant->created_at->format('d M Y') : '—' }}</td>
                                                    <td>
                                                        <a href="{{ route('owner.tenant.details', $tenant->id) }}" class="btn btn-xs btn-outline-primary">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        <i class="ri-team-line font-24 d-block mb-2"></i>
                                                        {{ __('No tenants found') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Expiring Leases --}}
                        <div class="col-lg-5">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <h4 class="mb-20">
                                    <i class="ri-calendar-close-line me-2" style="color:#d69e2e;"></i>
                                    {{ __('Leases Expiring Within 30 Days') }}
                                </h4>

                                @forelse ($expiringLeases as $lease)
                                    @php
                                        $daysLeft = (int) now()->diffInDays($lease->lease_end_date, false);
                                        $badgeClass = $daysLeft > 15 ? 'bg-success' : ($daysLeft > 7 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                        <div>
                                            <p class="mb-0 font-14 fw-semibold">{{ $lease->name ?? ($lease->user?->name ?? '—') }}</p>
                                            <small class="text-muted">
                                                {{ $lease->unit?->name ?? '—' }}
                                                &mdash; {{ $lease->lease_end_date ? \Carbon\Carbon::parse($lease->lease_end_date)->format('d M Y') : '—' }}
                                            </small>
                                        </div>
                                        <span class="badge {{ $badgeClass }} text-white ms-2">
                                            {{ $daysLeft }} {{ __('days') }}
                                        </span>
                                    </div>
                                @empty
                                    <div class="alert" style="background:#f0fff4;border:1px solid #9ae6b4;color:#276749;border-radius:6px;" role="alert">
                                        <i class="ri-checkbox-circle-line me-2"></i>
                                        {{ __('No leases expiring soon') }}
                                    </div>
                                @endforelse

                                @if ($expiringLeases->count() > 0)
                                    <div class="mt-3">
                                        <a href="{{ route('owner.tenant.index') ?? '#' }}" class="btn btn-sm btn-warning w-100">
                                            <i class="ri-send-plane-line me-1"></i>{{ __('Send Renewal Notices') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                    {{-- End Row 2 --}}

                    {{-- Row 3: Recent Tickets + Smart Alerts --}}
                    <div class="row">

                        {{-- Recent Support Tickets --}}
                        <div class="col-lg-6">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="d-flex align-items-center justify-content-between mb-20">
                                    <h4 class="mb-0"><i class="ri-customer-service-2-line me-2"></i>{{ __('Recent Support Tickets') }}</h4>
                                    <a href="{{ route('owner.ticket.index') ?? '#' }}" class="btn btn-sm btn-outline-danger">{{ __('View All') }}</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table theme-border">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Tenant') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Date') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($recentTickets as $ticket)
                                                <tr>
                                                    <td>
                                                        <span title="{{ $ticket->subject ?? $ticket->title ?? '' }}">
                                                            {{ \Illuminate\Support\Str::limit($ticket->subject ?? $ticket->title ?? '—', 35) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $ticket->user?->name ?? '—' }}</td>
                                                    <td>
                                                        @php
                                                            $statusMap = [
                                                                TICKET_STATUS_OPEN       => ['label' => 'Open',        'class' => 'bg-primary'],
                                                                TICKET_STATUS_INPROGRESS => ['label' => 'In Progress', 'class' => 'bg-info'],
                                                                TICKET_STATUS_CLOSE      => ['label' => 'Closed',      'class' => 'bg-secondary'],
                                                                TICKET_STATUS_REOPEN     => ['label' => 'Re-opened',   'class' => 'bg-warning'],
                                                                TICKET_STATUS_RESOLVED   => ['label' => 'Resolved',    'class' => 'bg-success'],
                                                            ];
                                                            $st = $statusMap[$ticket->status] ?? ['label' => ucfirst($ticket->status ?? '—'), 'class' => 'bg-secondary'];
                                                        @endphp
                                                        <span class="badge {{ $st['class'] }} text-white">{{ __($st['label']) }}</span>
                                                    </td>
                                                    <td>{{ $ticket->created_at ? $ticket->created_at->format('d M Y') : '—' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">
                                                        <i class="ri-ticket-line font-24 d-block mb-2"></i>
                                                        {{ __('No tickets found') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Smart Alerts --}}
                        <div class="col-lg-6">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <h4 class="mb-20"><i class="ri-notification-3-line me-2"></i>{{ __('Smart Alerts') }}</h4>

                                @php $hasAlerts = false; @endphp

                                @if ($vacantUnits > 0)
                                    @php $hasAlerts = true; @endphp
                                    <div class="alert alert-warning d-flex align-items-start gap-2 mb-3" role="alert">
                                        <i class="ri-home-line mt-1"></i>
                                        <div>
                                            <strong>{{ __('Vacant Units') }}</strong><br>
                                            {{ $vacantUnits }} {{ __('unit(s) vacant — consider new tenant applications') }}
                                        </div>
                                    </div>
                                @endif

                                @if ($expiringLeases->count() > 0)
                                    @php $hasAlerts = true; @endphp
                                    <div class="alert alert-warning d-flex align-items-start gap-2 mb-3" role="alert">
                                        <i class="ri-calendar-close-line mt-1"></i>
                                        <div>
                                            <strong>{{ __('Expiring Leases') }}</strong><br>
                                            {{ $expiringLeases->count() }} {{ __('lease(s) expiring within 30 days — send renewal notices') }}
                                        </div>
                                    </div>
                                @endif

                                @if ($openTickets > 5)
                                    @php $hasAlerts = true; @endphp
                                    <div class="alert alert-danger d-flex align-items-start gap-2 mb-3" role="alert">
                                        <i class="ri-ticket-line mt-1"></i>
                                        <div>
                                            <strong>{{ __('High Ticket Volume') }}</strong><br>
                                            {{ $openTickets }} {{ __('open tickets — review and assign') }}
                                        </div>
                                    </div>
                                @endif

                                @if (!$hasAlerts)
                                    <div class="alert" style="background:#f0fff4;border:1px solid #9ae6b4;color:#276749;border-radius:6px;" role="alert">
                                        <i class="ri-checkbox-circle-line me-2"></i>
                                        <strong>{{ __('All good!') }}</strong>
                                        {{ __('System running normally') }}
                                    </div>
                                @endif

                                {{-- Summary Stats --}}
                                <div class="mt-3 pt-3 border-top">
                                    <p class="font-13 text-muted mb-2">{{ __('Quick Summary') }}</p>
                                    <div class="d-flex justify-content-between font-13 py-1">
                                        <span>{{ __('Occupancy Rate') }}</span>
                                        @php $pct = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0; @endphp
                                        <strong class="{{ $pct >= 80 ? 'text-success' : ($pct >= 50 ? 'text-warning' : 'text-danger') }}">{{ $pct }}%</strong>
                                    </div>
                                    <div class="d-flex justify-content-between font-13 py-1">
                                        <span>{{ __('Inactive Tenants') }}</span>
                                        <strong>{{ number_format($inactiveTenants, 0) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between font-13 py-1">
                                        <span>{{ __('Pending Invoices') }}</span>
                                        <strong class="{{ $pendingInvoices > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($pendingInvoices, 0) }}</strong>
                                    </div>
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
