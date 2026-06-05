@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-user-3-line me-2 text-primary"></i>{{ __('Tenants') }}</h4>
                    <small class="text-muted">{{ __('All tenants in your workspace') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;">
                        <i class="ri-group-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total Tenants') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalCount) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;">
                        <i class="ri-user-smile-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Active') }}</div>
                        <div class="sh-stat-value">{{ number_format($activeCount) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;">
                        <i class="ri-calendar-close-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Leases Expiring (60d)') }}</div>
                        <div class="sh-stat-value {{ $expiringCount > 0 ? 'text-warning' : '' }}">{{ number_format($expiringCount) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">{{ __('All Tenants') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Lease Ends') }}</th>
                                    <th>{{ __('Joined') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $tenant)
                                <tr>
                                    <td style="padding:10px 16px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $tenant->user?->image ?? asset('assets/images/no-image.jpg') }}" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;" alt="">
                                            <span class="fw-semibold small">{{ $tenant->user?->name ?? $tenant->name ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $tenant->user?->email ?? '—' }}</td>
                                    <td class="text-muted small">{{ $tenant->user?->phone ?? $tenant->phone ?? '—' }}</td>
                                    <td>
                                        @if($tenant->status == TENANT_STATUS_ACTIVE)
                                            <span class="badge bg-success" style="font-size:10px;">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-secondary" style="font-size:10px;">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="small">
                                        @if($tenant->lease_end_date)
                                            @php $leaseEnd = \Carbon\Carbon::parse($tenant->lease_end_date); @endphp
                                            <span class="{{ $leaseEnd->isPast() ? 'text-danger' : ($leaseEnd->diffInDays(now()) <= 60 ? 'text-warning' : 'text-muted') }}">
                                                {{ $leaseEnd->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $tenant->created_at?->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="ri-user-line d-block fs-3 mb-1"></i>
                                        {{ __('No tenants found.') }}
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
