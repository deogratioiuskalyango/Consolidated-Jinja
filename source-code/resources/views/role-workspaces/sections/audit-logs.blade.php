@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-shield-check-line me-2 text-primary"></i>{{ __('Audit Trail') }}</h4>
                    <small class="text-muted">{{ __('Full financial audit log for your workspace') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;">
                        <i class="ri-list-check"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total Entries') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalCount) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;">
                        <i class="ri-calendar-today-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Today') }}</div>
                        <div class="sh-stat-value">{{ number_format($todayCount) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">{{ __('Audit Log') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:13px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">{{ __('Action') }}</th>
                                    <th>{{ __('User') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('IP Address') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $log)
                                <tr>
                                    <td style="padding:10px 16px;">
                                        <span class="badge bg-secondary" style="font-size:10px;">
                                            {{ str_replace('_', ' ', ucfirst($log->action ?? '—')) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $log->user?->name ?? '—' }}</td>
                                    <td>{{ Str::limit($log->description ?? '—', 55) }}</td>
                                    <td class="text-muted small">{{ $log->ip_address ?? '—' }}</td>
                                    <td class="text-muted small">{{ $log->created_at?->format('d M Y, H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="ri-shield-check-line d-block fs-3 mb-1"></i>
                                        {{ __('No audit entries found.') }}
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
