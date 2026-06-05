@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-receipt-line me-2 text-warning"></i>{{ __('Expenses') }}</h4>
                    <small class="text-muted">{{ __('All expenses scoped to your workspace') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('This Month') }}</div>
                        <div class="sh-stat-value" style="font-size:1.2rem;">UGX {{ number_format($thisMonth) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#ef4444;">
                        <i class="ri-calendar-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('This Year') }}</div>
                        <div class="sh-stat-value" style="font-size:1.2rem;">UGX {{ number_format($thisYear) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;">
                        <i class="ri-bar-chart-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total All Time') }}</div>
                        <div class="sh-stat-value" style="font-size:1.2rem;">UGX {{ number_format($totalAmount) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('All Expenses') }}</h5>
                    <span class="badge bg-warning bg-opacity-10 text-warning">{{ $records->total() }} {{ __('total') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">{{ __('Name') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Responsibility') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $expense)
                                <tr>
                                    <td style="padding:10px 16px;">{{ $expense->name ?? '—' }}</td>
                                    <td><strong>UGX {{ number_format($expense->total_amount ?? 0) }}</strong></td>
                                    <td>
                                        @if($expense->responsibilities == 1)
                                            <span class="badge bg-info" style="font-size:10px;">{{ __('Tenant') }}</span>
                                        @else
                                            <span class="badge bg-secondary" style="font-size:10px;">{{ __('Owner') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $expense->created_at?->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="ri-receipt-line d-block fs-3 mb-1"></i>
                                        {{ __('No expenses found.') }}
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
