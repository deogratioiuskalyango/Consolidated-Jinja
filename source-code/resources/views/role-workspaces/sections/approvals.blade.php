@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-checkbox-circle-line me-2 text-primary"></i>{{ __('Financial Approvals') }}</h4>
                    <small class="text-muted">{{ __('All financial approval requests in your workspace') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;">
                        <i class="ri-hourglass-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Pending') }}</div>
                        <div class="sh-stat-value {{ $pendingCount > 0 ? 'text-warning' : '' }}">{{ number_format($pendingCount) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Approved') }}</div>
                        <div class="sh-stat-value">{{ number_format($approvedCount) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#ef4444;">
                        <i class="ri-close-circle-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Rejected') }}</div>
                        <div class="sh-stat-value">{{ number_format($rejectedCount) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">{{ __('Approval Requests') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
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
                                @forelse($records as $approval)
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
                                    <td><strong>UGX {{ number_format($approval->amount ?? 0) }}</strong></td>
                                    <td><span class="badge {{ $st['class'] }}" style="font-size:10px;">{{ __($st['label']) }}</span></td>
                                    <td class="text-muted small">{{ $approval->created_at?->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="ri-checkbox-circle-line d-block fs-3 mb-1"></i>
                                        {{ __('No approval requests found.') }}
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
