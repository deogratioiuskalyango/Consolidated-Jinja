@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-home-8-line me-2 text-primary"></i>{{ __('Landlord Dashboard') }}</h4>
                    <small class="text-muted">{{ __('Property performance hub') }}</small>
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
                    <div class="sh-stat-icon" style="background:#3b82f6;"><i class="ri-home-4-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total Units') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalUnits) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;"><i class="ri-user-smile-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Active Tenants') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalTenants) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#059669;"><i class="ri-money-dollar-circle-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Monthly Income') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($monthlyIncome) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;"><i class="ri-line-chart-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Yearly Income') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($yearlyIncome) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#ef4444;"><i class="ri-bank-card-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Pending Payments') }}</div>
                        <div class="sh-stat-value {{ $pendingPayments > 0 ? 'text-danger' : '' }}">{{ number_format($pendingPayments) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#d97706;"><i class="ri-tools-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Maintenance Cost') }}</div>
                        <div class="sh-stat-value text-warning" style="font-size:1.1rem;">UGX {{ number_format($maintenanceCost) }}</div>
                    </div>
                </div>
                @php $netEarnings = $yearlyIncome - $maintenanceCost; @endphp
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:{{ $netEarnings >= 0 ? '#10b981' : '#ef4444' }};"><i class="ri-arrow-up-circle-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Net Earnings YTD') }}</div>
                        <div class="sh-stat-value {{ $netEarnings >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:1.1rem;">UGX {{ number_format($netEarnings) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Property Portfolio') }}</h5>
                    <a href="{{ route('role.workbench', [$role, 'properties']) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">{{ __('All Properties') }}</a>
                </div>
                <div class="card-body">
                    @if($properties->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="ri-building-4-line d-block fs-3 mb-1"></i>
                        {{ __('No properties found. Add your first property to get started.') }}
                    </div>
                    @else
                    <div class="row g-3">
                        @foreach($properties as $property)
                        @php
                            $units    = $property->propertyUnits;
                            $total    = $units->count();
                            $occupied = $units->filter(fn($u) => $u->activeTenant)->count();
                            $vacPct   = $total > 0 ? round(($occupied / $total) * 100) : 0;
                        @endphp
                        <div class="col-md-6 col-lg-4">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div style="width:36px;height:36px;border-radius:8px;background:#ede9fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="ri-building-line text-primary small"></i>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-semibold small text-truncate">{{ $property->name }}</div>
                                        <small class="text-muted">{{ Str::limit($property->address ?? '—', 40) }}</small>
                                    </div>
                                </div>
                                @if($total > 0)
                                <div class="d-flex justify-content-between mb-1" style="font-size:11px;">
                                    <span class="text-muted">{{ $total }} {{ __('units') }}</span>
                                    <span class="{{ $vacPct >= 80 ? 'text-success' : ($vacPct >= 50 ? 'text-warning' : 'text-danger') }} fw-semibold">{{ $occupied }}/{{ $total }} ({{ $vacPct }}%)</span>
                                </div>
                                <div class="progress" style="height:5px;border-radius:3px;">
                                    <div class="progress-bar {{ $vacPct >= 80 ? 'bg-success' : ($vacPct >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                         style="width:{{ $vacPct }}%;" role="progressbar"
                                         aria-valuenow="{{ $vacPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
