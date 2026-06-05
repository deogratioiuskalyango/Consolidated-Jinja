@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-building-line me-2 text-primary"></i>{{ __('Properties') }}</h4>
                    <small class="text-muted">{{ __('All properties in your portfolio') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;">
                        <i class="ri-building-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total Properties') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalCount) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;">
                        <i class="ri-home-4-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total Units') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalUnits) }}</div>
                        <div class="sh-stat-sub">{{ number_format($occupiedUnits) }} {{ __('occupied') }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;">
                        <i class="ri-percent-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Occupancy Rate') }}</div>
                        <div class="sh-stat-value">{{ $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0 }}<span class="sh-stat-unit">%</span></div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                @forelse($records as $property)
                @php
                    $units    = $property->propertyUnits;
                    $total    = $units->count();
                    $occupied = $units->filter(fn($u) => $u->activeTenant)->count();
                    $vacPct   = $total > 0 ? round(($occupied / $total) * 100) : 0;
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div style="width:44px;height:44px;border-radius:10px;background:#ede9fe;display:flex;align-items:center;justify-content:center;">
                                        <i class="ri-building-line text-primary fs-5"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-1 fw-semibold text-truncate">{{ $property->name }}</h6>
                                    <p class="text-muted small mb-2">
                                        <i class="ri-map-pin-line me-1"></i>{{ Str::limit($property->address ?? '—', 45) }}
                                    </p>
                                    <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:12px;">
                                        <span class="text-muted">{{ $total }} {{ __('units') }}</span>
                                        <span class="{{ $vacPct >= 80 ? 'text-success' : ($vacPct >= 50 ? 'text-warning' : 'text-danger') }} fw-semibold">{{ $occupied }}/{{ $total }} {{ __('occupied') }}</span>
                                    </div>
                                    @if($total > 0)
                                    <div class="progress" style="height:6px;border-radius:3px;">
                                        <div class="progress-bar {{ $vacPct >= 80 ? 'bg-success' : ($vacPct >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                             style="width:{{ $vacPct }}%;" role="progressbar"
                                             aria-valuenow="{{ $vacPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="ri-building-4-line d-block fs-1 text-muted mb-2"></i>
                        <p class="text-muted">{{ __('No properties found.') }}</p>
                    </div>
                </div>
                @endforelse
            </div>

            @if($records->hasPages())
            <div class="mt-4">{{ $records->links() }}</div>
            @endif

        </div>
    </div>
</div>
@endsection
