@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-alert-line me-2 text-warning"></i>{{ __('Compliance Alerts') }}</h4>
                    <small class="text-muted">{{ __('Active governance and compliance issues requiring attention') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="row g-4">
                @forelse($alerts as $alert)
                @php
                    $alertCss = match($alert['type'] ?? 'warning') {
                        'danger'  => 'border-danger',
                        'success' => 'border-success',
                        default   => 'border-warning',
                    };
                    $iconCss = match($alert['type'] ?? 'warning') {
                        'danger'  => 'text-danger',
                        'success' => 'text-success',
                        default   => 'text-warning',
                    };
                    $bgCss = match($alert['type'] ?? 'warning') {
                        'danger'  => 'bg-danger bg-opacity-10',
                        'success' => 'bg-success bg-opacity-10',
                        default   => 'bg-warning bg-opacity-10',
                    };
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm border-start border-4 {{ $alertCss }} h-100">
                        <div class="card-body d-flex align-items-start gap-3">
                            <div class="flex-shrink-0">
                                <div style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;" class="{{ $bgCss }}">
                                    <i class="{{ $alert['icon'] ?? 'ri-alert-line' }} {{ $iconCss }} fs-5"></i>
                                </div>
                            </div>
                            <div>
                                <p class="mb-1 fw-semibold small">{{ $alert['message'] }}</p>
                                <span class="badge {{ $alert['type'] === 'danger' ? 'bg-danger' : ($alert['type'] === 'success' ? 'bg-success' : 'bg-warning text-dark') }}" style="font-size:10px;">
                                    {{ ucfirst($alert['type'] ?? 'warning') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="ri-shield-check-line d-block fs-1 text-success mb-2"></i>
                        <h5 class="text-muted">{{ __('No active alerts') }}</h5>
                        <p class="text-muted small">{{ __('All governance controls are in order.') }}</p>
                    </div>
                </div>
                @endforelse
            </div>

        </div>
    </div>
</div>
@endsection
