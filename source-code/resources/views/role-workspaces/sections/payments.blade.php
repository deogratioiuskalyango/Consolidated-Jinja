@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-bank-card-line me-2 text-success"></i>{{ __('Payments Received') }}</h4>
                    <small class="text-muted">{{ __('All paid invoices in your workspace') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;">
                        <i class="ri-calendar-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('This Month') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($thisMonth) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;">
                        <i class="ri-line-chart-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('This Year') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($thisYear) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;">
                        <i class="ri-bar-chart-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('All Time') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($totalAmount) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">{{ __('Payment Records') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">{{ __('Invoice No') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Tenant') }}</th>
                                    <th>{{ __('Month') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $invoice)
                                <tr>
                                    <td style="padding:10px 16px;"><strong class="small">{{ $invoice->invoice_no ?? '—' }}</strong></td>
                                    <td>{{ Str::limit($invoice->name ?? '—', 30) }}</td>
                                    <td class="text-muted small">{{ $invoice->tenant?->user?->name ?? $invoice->tenant?->name ?? '—' }}</td>
                                    <td class="text-muted small">{{ $invoice->month ?? '—' }}</td>
                                    <td><strong class="text-success">UGX {{ number_format($invoice->amount ?? 0) }}</strong></td>
                                    <td class="text-muted small">{{ $invoice->created_at?->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="ri-bank-card-line d-block fs-3 mb-1"></i>
                                        {{ __('No payment records found.') }}
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
