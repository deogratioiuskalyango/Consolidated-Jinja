@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-eye-line me-2 text-primary"></i>{{ __('Auditor Dashboard') }}</h4>
                    <small class="text-muted">{{ __('Audit and assurance workspace') }}</small>
                </div>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;"><i class="ri-list-check"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total Audit Entries') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalAuditLogs) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;"><i class="ri-calendar-today-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Today') }}</div>
                        <div class="sh-stat-value">{{ number_format($todayLogs) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#3b82f6;"><i class="ri-calendar-week-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('This Week') }}</div>
                        <div class="sh-stat-value">{{ number_format($weekLogs) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#059669;"><i class="ri-money-dollar-circle-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Revenue YTD') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($totalRevenue) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#ef4444;"><i class="ri-receipt-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Expenses YTD') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($totalExpenses) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;"><i class="ri-hourglass-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Pending Approvals') }}</div>
                        <div class="sh-stat-value {{ $pendingApprovals > 0 ? 'text-warning' : '' }}">{{ number_format($pendingApprovals) }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                {{-- Recent Audit Logs --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Recent Audit Entries') }}</h5>
                            <a href="{{ route('role.workbench', [$role, 'audit-logs']) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">{{ __('Full Trail') }}</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
                                <table class="table table-hover mb-0" style="font-size:12px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="padding:8px 16px;">{{ __('Action') }}</th>
                                            <th>{{ __('User') }}</th>
                                            <th>{{ __('Description') }}</th>
                                            <th>{{ __('Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentLogs as $log)
                                        <tr>
                                            <td style="padding:8px 16px;">
                                                <span class="badge bg-secondary" style="font-size:10px;">{{ str_replace('_', ' ', ucfirst($log->action ?? '—')) }}</span>
                                            </td>
                                            <td class="text-muted small">{{ $log->user?->name ?? '—' }}</td>
                                            <td>{{ Str::limit($log->description ?? '—', 45) }}</td>
                                            <td class="text-muted small">{{ $log->created_at?->format('d M H:i') }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center py-3 text-muted small">{{ __('No audit entries.') }}</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Summary --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0">{{ __('Activity Breakdown') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            @forelse($actionSummary as $item)
                            <div class="d-flex align-items-center justify-content-between px-4 py-2 border-bottom">
                                <span class="small text-truncate me-2">{{ str_replace('_', ' ', ucfirst($item->action ?? '—')) }}</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ number_format($item->total) }}</span>
                            </div>
                            @empty
                            <div class="text-center py-3 text-muted small">{{ __('No activity data.') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Largest Transactions --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ __('Largest Transactions') }}</h5>
                    <a href="{{ route('role.workbench', [$role, 'large-transactions']) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:13px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">{{ __('Invoice No') }}</th>
                                    <th>{{ __('Tenant') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Month') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($largeTransactions as $invoice)
                                <tr>
                                    <td style="padding:10px 16px;"><strong class="small">{{ $invoice->invoice_no ?? '—' }}</strong></td>
                                    <td class="text-muted small">{{ $invoice->tenant?->user?->name ?? $invoice->tenant?->name ?? '—' }}</td>
                                    <td><strong>UGX {{ number_format($invoice->amount ?? 0) }}</strong></td>
                                    <td class="text-muted small">{{ $invoice->month ?? '—' }}</td>
                                    <td class="text-muted small">{{ $invoice->created_at?->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-3 text-muted small">{{ __('No transactions.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
