@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-customer-service-2-line me-2 text-primary"></i>{{ __('Support Tickets') }}</h4>
                    <small class="text-muted">{{ __('All support tickets in your workspace') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#ef4444;">
                        <i class="ri-customer-service-2-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Open Tickets') }}</div>
                        <div class="sh-stat-value {{ $openCount > 0 ? 'text-danger' : '' }}">{{ number_format($openCount) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Closed Tickets') }}</div>
                        <div class="sh-stat-value">{{ number_format($closedCount) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">{{ __('All Tickets') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">{{ __('Ticket No') }}</th>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Property') }}</th>
                                    <th>{{ __('Submitted By') }}</th>
                                    <th>{{ __('Topic') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $ticket)
                                <tr>
                                    <td style="padding:10px 16px;"><strong class="small">{{ $ticket->ticket_no ?? '—' }}</strong></td>
                                    <td>{{ Str::limit($ticket->title ?? '—', 35) }}</td>
                                    <td class="text-muted small">{{ $ticket->property?->name ?? '—' }}</td>
                                    <td class="text-muted small">{{ $ticket->user?->name ?? '—' }}</td>
                                    <td class="text-muted small">{{ $ticket->topic?->name ?? '—' }}</td>
                                    <td>
                                        @if($ticket->status == TICKET_STATUS_CLOSE)
                                            <span class="badge bg-success" style="font-size:10px;">{{ __('Closed') }}</span>
                                        @elseif($ticket->status == TICKET_STATUS_OPEN ?? 1)
                                            <span class="badge bg-danger" style="font-size:10px;">{{ __('Open') }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark" style="font-size:10px;">{{ __('In Progress') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $ticket->created_at?->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="ri-customer-service-line d-block fs-3 mb-1"></i>
                                        {{ __('No tickets found.') }}
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
