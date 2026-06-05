@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-discuss-line me-2 text-primary"></i>{{ __('Resolutions') }}</h4>
                    <small class="text-muted">{{ __('All governance resolutions in your workspace') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;">
                        <i class="ri-discuss-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Open') }}</div>
                        <div class="sh-stat-value {{ $openCount > 0 ? 'text-warning' : '' }}">{{ number_format($openCount) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Passed') }}</div>
                        <div class="sh-stat-value text-success">{{ number_format($passedCount) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#ef4444;">
                        <i class="ri-close-circle-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Failed') }}</div>
                        <div class="sh-stat-value">{{ number_format($failedCount) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">{{ __('All Resolutions') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">{{ __('Title') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Voting Closes') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $resolution)
                                @php
                                    $statusMap = [
                                        RESOLUTION_STATUS_DRAFT  => ['label' => 'Draft',  'class' => 'bg-secondary'],
                                        RESOLUTION_STATUS_OPEN   => ['label' => 'Open',   'class' => 'bg-warning text-dark'],
                                        RESOLUTION_STATUS_CLOSED => ['label' => 'Closed', 'class' => 'bg-info'],
                                        RESOLUTION_STATUS_PASSED => ['label' => 'Passed', 'class' => 'bg-success'],
                                        RESOLUTION_STATUS_FAILED => ['label' => 'Failed', 'class' => 'bg-danger'],
                                    ];
                                    $st = $statusMap[$resolution->status] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                                @endphp
                                <tr>
                                    <td style="padding:10px 16px;"><strong class="small">{{ Str::limit($resolution->title ?? '—', 45) }}</strong></td>
                                    <td class="text-muted small">{{ $resolution->type ?? '—' }}</td>
                                    <td class="small">
                                        @if($resolution->voting_closes_at)
                                            {{ \Carbon\Carbon::parse($resolution->voting_closes_at)->format('d M Y') }}
                                            <span class="text-muted">({{ \Carbon\Carbon::parse($resolution->voting_closes_at)->diffForHumans() }})</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td><span class="badge {{ $st['class'] }}" style="font-size:10px;">{{ __($st['label']) }}</span></td>
                                    <td class="text-muted small">{{ $resolution->created_at?->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="ri-discuss-line d-block fs-3 mb-1"></i>
                                        {{ __('No resolutions found.') }}
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
