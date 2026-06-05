@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-calendar-event-line me-2 text-primary"></i>{{ __('Meetings') }}</h4>
                    <small class="text-muted">{{ __('All governance meetings in your workspace') }}</small>
                </div>
                <a href="{{ route('role.' . str_replace('_', '-', $role) . '.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ri-arrow-left-line me-1"></i>{{ __('Dashboard') }}
                </a>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;">
                        <i class="ri-calendar-event-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total Meetings') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalCount) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;">
                        <i class="ri-calendar-todo-line"></i>
                    </div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Upcoming') }}</div>
                        <div class="sh-stat-value">{{ number_format($upcomingCount) }}</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">{{ __('All Meetings') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="padding:10px 16px;">{{ __('Title') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Scheduled') }}</th>
                                    <th>{{ __('Location') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $meeting)
                                @php
                                    $typeLabels = [1 => 'AGM', 2 => 'Board', 3 => 'EGM', 4 => 'Other'];
                                    $isPast = $meeting->scheduled_at && \Carbon\Carbon::parse($meeting->scheduled_at)->isPast();
                                    $isUpcoming = $meeting->status == MEETING_STATUS_SCHEDULED && !$isPast;
                                @endphp
                                <tr>
                                    <td style="padding:10px 16px;"><strong class="small">{{ $meeting->title ?? '—' }}</strong></td>
                                    <td class="text-muted small">{{ $typeLabels[$meeting->meeting_type ?? 4] ?? '—' }}</td>
                                    <td class="small">
                                        @if($meeting->scheduled_at)
                                            <span class="{{ $isUpcoming ? 'text-primary fw-semibold' : 'text-muted' }}">
                                                {{ \Carbon\Carbon::parse($meeting->scheduled_at)->format('d M Y, H:i') }}
                                            </span>
                                        @else
                                            <span class="text-muted">{{ __('TBD') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ Str::limit($meeting->location ?? '—', 30) }}</td>
                                    <td>
                                        @if($isUpcoming)
                                            <span class="badge bg-primary" style="font-size:10px;">{{ __('Upcoming') }}</span>
                                        @elseif($isPast)
                                            <span class="badge bg-secondary" style="font-size:10px;">{{ __('Past') }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark" style="font-size:10px;">{{ __('Scheduled') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="ri-calendar-event-line d-block fs-3 mb-1"></i>
                                        {{ __('No meetings found.') }}
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
