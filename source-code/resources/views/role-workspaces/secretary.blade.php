@extends('role-workspaces.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-calendar-todo-line me-2 text-primary"></i>{{ __('Secretary Dashboard') }}</h4>
                    <small class="text-muted">{{ __('Board administration desk') }}</small>
                </div>
            </div>

            <div class="sh-stat-grid mb-4">
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#6366f1;"><i class="ri-calendar-event-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Upcoming Meetings') }}</div>
                        <div class="sh-stat-value">{{ number_format($upcomingMeetings->count()) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#3b82f6;"><i class="ri-calendar-todo-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Total Meetings') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalMeetings) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#f59e0b;"><i class="ri-discuss-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Open Resolutions') }}</div>
                        <div class="sh-stat-value {{ $openResolutions->count() > 0 ? 'text-warning' : '' }}">{{ number_format($openResolutions->count()) }}</div>
                    </div>
                </div>
                <div class="sh-stat-card">
                    <div class="sh-stat-icon" style="background:#10b981;"><i class="ri-folder-2-line"></i></div>
                    <div class="sh-stat-body">
                        <div class="sh-stat-label">{{ __('Governance Documents') }}</div>
                        <div class="sh-stat-value">{{ number_format($totalDocuments) }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                {{-- Upcoming Meetings --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Upcoming Meetings') }}</h5>
                            <a href="{{ route('role.workbench', [$role, 'meetings']) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">{{ __('All') }}</a>
                        </div>
                        <div class="card-body p-0">
                            @forelse($upcomingMeetings as $meeting)
                            <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom">
                                <div style="min-width:44px;height:44px;border-radius:8px;background:#ede9fe;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;line-height:1.2;flex-shrink:0;">
                                    <strong style="font-size:16px;color:#6366f1;">{{ $meeting->scheduled_at ? \Carbon\Carbon::parse($meeting->scheduled_at)->format('d') : '?' }}</strong>
                                    <small style="font-size:9px;text-transform:uppercase;color:#6366f1;">{{ $meeting->scheduled_at ? \Carbon\Carbon::parse($meeting->scheduled_at)->format('M') : '' }}</small>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold small text-truncate">{{ $meeting->title ?? '—' }}</div>
                                    <div class="text-muted" style="font-size:11px;">
                                        <i class="ri-time-line me-1"></i>{{ $meeting->scheduled_at ? \Carbon\Carbon::parse($meeting->scheduled_at)->format('H:i') : __('TBD') }}
                                        @if($meeting->location) &bull; {{ Str::limit($meeting->location, 25) }} @endif
                                    </div>
                                    <span class="badge bg-primary" style="font-size:9px;">{{ __('Scheduled') }}</span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted small">
                                <i class="ri-calendar-check-line d-block fs-4 mb-1"></i>
                                {{ __('No upcoming meetings.') }}
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Open Resolutions --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Open Resolutions') }}</h5>
                            <a href="{{ route('role.workbench', [$role, 'resolutions']) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;">{{ __('All') }}</a>
                        </div>
                        <div class="card-body p-0">
                            @forelse($openResolutions as $resolution)
                            <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom">
                                <div style="width:36px;height:36px;border-radius:8px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="ri-discuss-line text-warning"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold small text-truncate">{{ Str::limit($resolution->title, 45) }}</div>
                                    <div class="text-muted" style="font-size:11px;">
                                        @if($resolution->voting_closes_at)
                                            {{ __('Closes') }}: {{ \Carbon\Carbon::parse($resolution->voting_closes_at)->format('d M Y') }}
                                            ({{ \Carbon\Carbon::parse($resolution->voting_closes_at)->diffForHumans() }})
                                        @else
                                            {{ __('Open for voting') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted small">{{ __('No open resolutions. All governance decisions are resolved.') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- This month's calendar --}}
            @if($calendarMeetings->isNotEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">{{ __('Meetings This Month') }} &mdash; {{ now()->format('F Y') }}</h5>
                </div>
                <div class="card-body p-0">
                    @foreach($calendarMeetings->sortBy('scheduled_at') as $meeting)
                    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                        <div style="width:36px;height:36px;border-radius:8px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="ri-calendar-line text-info"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold small text-truncate">{{ $meeting->title ?? '—' }}</div>
                            <small class="text-muted">
                                {{ $meeting->scheduled_at ? \Carbon\Carbon::parse($meeting->scheduled_at)->format('l, d M Y H:i') : '—' }}
                                @if($meeting->location) &bull; {{ $meeting->location }} @endif
                            </small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
