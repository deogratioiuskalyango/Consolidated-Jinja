@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-home-4-line me-2 text-primary"></i>{{ __('Units') }}</h4>
                    <small class="text-muted">{{ __('All property units in your workspace') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;">
                        <i class="ri-home-4-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total Units') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalUnits) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;">
                        <i class="ri-user-smile-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Occupied') }}</div>
                        <div class="sh-stat-value">{{ number_format($occupiedUnits) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;">
                        <i class="ri-door-open-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Vacant') }}</div>
                        <div class="sh-stat-value {{ $vacantUnits > 0 ? 'text-warning' : '' }}">{{ number_format($vacantUnits) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">{{ __('All Units') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">{{ __('Unit') }}</th>
                                    <th>{{ __('Property') }}</th>
                                    <th>{{ __('Rent') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Tenant') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $unit)
                                <tr>
                                    <td style="padding:10px 16px;"><strong>{{ $unit->name ?? $unit->unit_number ?? '—' }}</strong></td>
                                    <td class="text-muted small">{{ $unit->property_name ?? '—' }}</td>
                                    <td>UGX {{ number_format($unit->rent ?? 0) }}</td>
                                    <td>
                                        @if($unit->activeTenant)
                                            <span class="badge bg-success" style="font-size:10px;">{{ __('Occupied') }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark" style="font-size:10px;">{{ __('Vacant') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $unit->activeTenant?->user?->name ?? $unit->activeTenant?->name ?? '—' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="ri-home-line d-block fs-3 mb-1"></i>
                                        {{ __('No units found.') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($records->hasPages())
                    <div class="px-4 py-3 border-top">{{ $records->links() }}</div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
