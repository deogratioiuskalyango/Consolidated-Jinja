@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-tools-line me-2 text-warning"></i>{{ __('Maintenance Requests') }}</h4>
                    <small class="text-muted">{{ __('All maintenance requests across your properties') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;">
                        <i class="ri-time-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Pending Requests') }}</div>
                        <div class="sh-stat-value {{ $pendingCount > 0 ? 'text-warning' : '' }}">{{ number_format($pendingCount) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#ef4444;">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total Cost') }}</div>
                        <div class="sh-stat-value" style="font-size:1.1rem;">UGX {{ number_format($totalCost) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">{{ __('Maintenance Requests') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">{{ __('ID') }}</th>
                                    <th>{{ __('Property') }}</th>
                                    <th>{{ __('Details') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $req)
                                <tr>
                                    <td style="padding:10px 16px;"><strong class="small">{{ $req->request_id ?? $req->id }}</strong></td>
                                    <td class="text-muted small">{{ $req->property_name ?? '—' }}</td>
                                    <td>{{ Str::limit($req->details ?? '—', 40) }}</td>
                                    <td>UGX {{ number_format($req->amount ?? 0) }}</td>
                                    <td>
                                        @php
                                            $statusMap = [
                                                0 => ['label' => 'Pending',     'class' => 'bg-warning text-dark'],
                                                1 => ['label' => 'In Progress', 'class' => 'bg-info'],
                                                2 => ['label' => 'Completed',   'class' => 'bg-success'],
                                                3 => ['label' => 'Cancelled',   'class' => 'bg-danger'],
                                            ];
                                            $st = $statusMap[$req->status] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                                        @endphp
                                        <span class="badge {{ $st['class'] }}" style="font-size:10px;">{{ __($st['label']) }}</span>
                                    </td>
                                    <td class="text-muted small">{{ $req->created_at?->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="ri-tools-line d-block fs-3 mb-1"></i>
                                        {{ __('No maintenance requests found.') }}
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
