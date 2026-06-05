@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-search-eye-line me-2 text-primary"></i>{{ __('Large Transactions') }}</h4>
                    <small class="text-muted">{{ __('Paid invoices sorted by amount — highest first') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total Paid Revenue') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($totalAmount) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">{{ __('Transactions by Value') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">#</th>
                                    <th>{{ __('Invoice No') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Tenant') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Month') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $i => $invoice)
                                <tr>
                                    <td style="padding:10px 16px;" class="text-muted small">{{ $records->firstItem() + $loop->index }}</td>
                                    <td><strong class="small">{{ $invoice->invoice_no ?? '—' }}</strong></td>
                                    <td>{{ Str::limit($invoice->name ?? '—', 30) }}</td>
                                    <td class="text-muted small">{{ $invoice->tenant?->user?->name ?? $invoice->tenant?->name ?? '—' }}</td>
                                    <td>
                                        <strong class="{{ ($invoice->amount ?? 0) >= 1000000 ? 'text-danger' : (($invoice->amount ?? 0) >= 500000 ? 'text-warning' : 'text-success') }}">
                                            UGX {{ number_format($invoice->amount ?? 0) }}
                                        </strong>
                                    </td>
                                    <td class="text-muted small">{{ $invoice->month ?? '—' }}</td>
                                    <td class="text-muted small">{{ $invoice->created_at?->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="ri-money-dollar-circle-line d-block fs-3 mb-1"></i>
                                        {{ __('No paid transactions found.') }}
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
