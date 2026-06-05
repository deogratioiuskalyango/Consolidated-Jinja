@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-team-line me-2 text-primary"></i>{{ __('Tenant Manager Dashboard') }}</h4>
                    <small class="text-muted">{{ __('Tenant operations desk') }}</small>
                </div>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;"><i class="ri-group-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total Tenants') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalTenants) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;"><i class="ri-user-smile-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Active Tenants') }}</div>
                        <div class="sh-stat-value">{{ number_format($activeTenants) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;"><i class="ri-door-open-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Vacant Units') }}</div>
                        <div class="sh-stat-value {{ $vacantUnits > 0 ? 'text-warning' : '' }}">{{ number_format($vacantUnits) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#ef4444;"><i class="ri-customer-service-2-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Open Tickets') }}</div>
                        <div class="sh-stat-value {{ $openTickets > 0 ? 'text-danger' : '' }}">{{ number_format($openTickets) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#8b5cf6;"><i class="ri-file-warning-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Pending Invoices') }}</div>
                        <div class="sh-stat-value {{ $pendingInvoices > 0 ? 'text-warning' : '' }}">{{ number_format($pendingInvoices) }}</div>
                    </div>
                </div>
            </div>

            {{-- Occupancy bar --}}
            @if($totalUnits > 0)
            @php $occPct = round(($occupiedUnits / $totalUnits) * 100); @endphp
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between mb-2" style="font-size:13px;">
                        <span class="fw-semibold">{{ __('Unit Occupancy') }}</span>
                        <span class="{{ $occPct >= 80 ? 'text-success' : ($occPct >= 50 ? 'text-warning' : 'text-danger') }} fw-semibold">{{ $occupiedUnits }}/{{ $totalUnits }} {{ __('units') }} &mdash; {{ $occPct }}%</span>
                    </div>
                    <div class="progress" style="height:10px;border-radius:5px;">
                        <div class="progress-bar {{ $occPct >= 80 ? 'bg-success' : ($occPct >= 50 ? 'bg-warning' : 'bg-danger') }}"
                             style="width:{{ $occPct }}%;" role="progressbar"
                             aria-valuenow="{{ $occPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row g-4">
                {{-- Expiring Leases --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Leases Expiring (60 days)') }}</h5>
                            <a href="{{ route('role.workbench', [$role, 'tenants']) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">{{ __('All Tenants') }}</a>
                        </div>
                        <div class="card-body p-0">
                            @forelse($expiringLeases as $tenant)
                            <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <img src="{{ $tenant->user?->image ?? asset('assets/images/no-image.jpg') }}" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;" alt="">
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold small text-truncate">{{ $tenant->user?->name ?? $tenant->name ?? '—' }}</div>
                                    <small class="text-muted">
                                        <i class="ri-calendar-close-line me-1"></i>
                                        {{ __('Expires') }}: {{ $tenant->lease_end_date ? \Carbon\Carbon::parse($tenant->lease_end_date)->format('d M Y') : '—' }}
                                        @if($tenant->lease_end_date)
                                            ({{ \Carbon\Carbon::parse($tenant->lease_end_date)->diffForHumans() }})
                                        @endif
                                    </small>
                                </div>
                                <span class="badge bg-warning text-dark" style="font-size:10px;">{{ __('Expiring') }}</span>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted small">
                                <i class="ri-calendar-check-line d-block fs-4 mb-1"></i>
                                {{ __('No leases expiring in the next 60 days.') }}
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Recent Tenants --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0">{{ __('Recently Added Tenants') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            @forelse($recentTenants as $tenant)
                            <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <img src="{{ $tenant->user?->image ?? asset('assets/images/no-image.jpg') }}" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;" alt="">
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold small text-truncate">{{ $tenant->user?->name ?? $tenant->name ?? '—' }}</div>
                                    <small class="text-muted">{{ $tenant->user?->email ?? '—' }}</small>
                                </div>
                                @if($tenant->status == TENANT_STATUS_ACTIVE)
                                    <span class="badge bg-success" style="font-size:10px;">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-secondary" style="font-size:10px;">{{ __('Inactive') }}</span>
                                @endif
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted small">
                                <i class="ri-user-line d-block fs-4 mb-1"></i>
                                {{ __('No tenants found.') }}
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
