@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-file-list-3-line me-2 text-primary"></i>{{ __('Invoices') }}</h4>
                    <small class="text-muted">{{ __('All invoices scoped to your workspace') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            {{-- KPI row --}}
            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Paid Invoices') }}</div>
                        <div class="sh-stat-value">{{ number_format($countPaid) }}</div>
                        <div class="sh-stat-sub">UGX {{ number_format($totalPaid) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#ef4444;">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Unpaid Invoices') }}</div>
                        <div class="sh-stat-value">{{ number_format($countUnpaid) }}</div>
                        <div class="sh-stat-sub">UGX {{ number_format($totalUnpaid) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('All Invoices') }}</h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $records->total() }} {{ __('total') }}</span>
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
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $invoice)
                                <tr>
                                    <td style="padding:10px 16px;"><strong class="small">{{ $invoice->invoice_no ?? '—' }}</strong></td>
                                    <td>{{ Str::limit($invoice->name ?? '—', 30) }}</td>
                                    <td>{{ $invoice->tenant?->user?->name ?? $invoice->tenant?->name ?? '—' }}</td>
                                    <td>{{ $invoice->month ?? '—' }}</td>
                                    <td><strong>UGX {{ number_format($invoice->amount ?? 0) }}</strong></td>
                                    <td>
                                        @if($invoice->status == INVOICE_STATUS_PAID)
                                            <span class="badge bg-success" style="font-size:10px;">{{ __('Paid') }}</span>
                                        @elseif($invoice->status == INVOICE_STATUS_UNPAID)
                                            <span class="badge bg-warning text-dark" style="font-size:10px;">{{ __('Unpaid') }}</span>
                                        @else
                                            <span class="badge bg-danger" style="font-size:10px;">{{ __('Overdue') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $invoice->created_at?->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="ri-file-list-line d-block fs-3 mb-1"></i>
                                        {{ __('No invoices found.') }}
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
