@extends('owner.layouts.app')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="page-content-wrapper bg-white p-30 radius-20">

                    {{-- Page Header --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex flex-column flex-sm-row align-items-sm-center justify-content-between g-20 border-bottom mb-25">
                                <div class="page-title-left">
                                    <h2 class="mb-sm-0">{{ __('Secretary Dashboard') }}</h2>
                                    <p class="mb-0">{{ now()->format('l, d F Y') }}</p>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li class="breadcrumb-item active">{{ __('Secretary') }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 1: 4 KPI Cards --}}
                    <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-group-line primary-color font-22"></i>
                                </div>
                                <p class="mt-2">{{ __('Total Meetings') }}</p>
                                <h2 class="mt-1">{{ number_format($totalMeetings, 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-calendar-event-line orange-color font-22"></i>
                                </div>
                                <p class="mt-2">{{ __('Upcoming Meetings') }}</p>
                                <h2 class="mt-1">{{ $upcomingMeetings->count() }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-survey-line green-color font-22"></i>
                                </div>
                                <p class="mt-2">{{ __('Total Resolutions') }}</p>
                                <h2 class="mt-1">{{ number_format($totalResolutions, 0) }}</h2>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="dashboard-feature-item bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="dashboard-feature-item-icon-wrap font-20 d-flex align-items-center justify-content-center bg-white radius-4">
                                    <i class="ri-file-text-line" style="color:#805ad5;font-size:22px;"></i>
                                </div>
                                <p class="mt-2">{{ __('Total Documents') }}</p>
                                <h2 class="mt-1">{{ number_format($totalDocuments, 0) }}</h2>
                            </div>
                        </div>
                    </div>
                    {{-- End KPI Cards --}}

                    {{-- Quick Actions --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <h5 class="mb-15"><i class="ri-flashlight-line me-2"></i>{{ __('Quick Actions') }}</h5>
                                <div class="d-flex flex-wrap gap-2">
                                    @if (Route::has('owner.governance.meetings.create'))
                                        <a href="{{ route('owner.governance.meetings.create') }}" class="theme-btn">
                                            <i class="ri-calendar-add-line me-1"></i>{{ __('Schedule Meeting') }}
                                        </a>
                                    @else
                                        <button class="theme-btn" disabled><i class="ri-calendar-add-line me-1"></i>{{ __('Schedule Meeting') }}</button>
                                    @endif
                                    @if (Route::has('owner.governance.meetings.index'))
                                        <a href="{{ route('owner.governance.meetings.index') }}" class="theme-btn-outline">
                                            <i class="ri-edit-2-line me-1"></i>{{ __('Record Minutes') }}
                                        </a>
                                    @endif
                                    @if (Route::has('owner.governance.documents.create'))
                                        <a href="{{ route('owner.governance.documents.create') }}" class="theme-btn-outline">
                                            <i class="ri-upload-2-line me-1"></i>{{ __('Upload Document') }}
                                        </a>
                                    @endif
                                    @if (Route::has('owner.noticeboard.create'))
                                        <a href="{{ route('owner.noticeboard.create') }}" class="theme-btn-outline">
                                            <i class="ri-notification-2-line me-1"></i>{{ __('Send Notice') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Upcoming Meetings + Open Resolutions --}}
                    <div class="row">
                        {{-- Upcoming Meetings --}}
                        <div class="col-lg-7">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="d-flex align-items-center justify-content-between mb-20">
                                    <h4 class="mb-0"><i class="ri-calendar-event-line me-2"></i>{{ __('Upcoming Meetings') }}</h4>
                                    @if (Route::has('owner.governance.meetings.index'))
                                        <a href="{{ route('owner.governance.meetings.index') }}" class="theme-link font-14 d-flex align-items-center">
                                            {{ __('View All') }}<i class="ri-arrow-right-line ms-1"></i>
                                        </a>
                                    @endif
                                </div>

                                @forelse ($upcomingMeetings as $meeting)
                                    <div class="d-flex align-items-start mb-15 p-15 bg-white radius-4 theme-border">
                                        {{-- Date badge --}}
                                        <div class="flex-shrink-0 text-center me-15 p-10 radius-4 d-flex flex-column align-items-center justify-content-center" style="background:#ebf4ff;min-width:52px;">
                                            <strong class="primary-color font-20 lh-1">{{ $meeting->scheduled_at ? \Carbon\Carbon::parse($meeting->scheduled_at)->format('d') : '—' }}</strong>
                                            <small class="primary-color font-11">{{ $meeting->scheduled_at ? \Carbon\Carbon::parse($meeting->scheduled_at)->format('M') : '' }}</small>
                                        </div>
                                        {{-- Details --}}
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $meeting->title }}</h6>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <span class="badge bg-info text-white">{{ __('Scheduled') }}</span>
                                                <small class="text-muted">
                                                    <i class="ri-time-line me-1"></i>
                                                    {{ $meeting->scheduled_at ? \Carbon\Carbon::parse($meeting->scheduled_at)->format('H:i') : __('TBD') }}
                                                </small>
                                                <small class="text-muted">
                                                    <i class="ri-map-pin-line me-1"></i>
                                                    {{ $meeting->location ?? __('TBD') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-30">
                                        <i class="ri-calendar-line font-40 text-muted d-block mb-10"></i>
                                        <p class="text-muted">{{ __('No upcoming meetings scheduled') }}</p>
                                        @if (Route::has('owner.governance.meetings.create'))
                                            <a href="{{ route('owner.governance.meetings.create') }}" class="theme-link">{{ __('Schedule a meeting') }}</a>
                                        @endif
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Open Resolutions --}}
                        <div class="col-lg-5">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <h4 class="mb-20"><i class="ri-check-double-line me-2"></i>{{ __('Open Resolutions') }}</h4>

                                @forelse ($openResolutions as $resolution)
                                    <div class="p-15 bg-white radius-4 theme-border mb-10">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <h6 class="mb-5 flex-grow-1 me-2">{{ Str::limit($resolution->title, 40) }}</h6>
                                            <span class="badge bg-warning text-dark flex-shrink-0">{{ __('Open') }}</span>
                                        </div>
                                        @if ($resolution->voting_closes_at)
                                            <small class="text-muted">
                                                <i class="ri-timer-line me-1"></i>
                                                {{ __('Closes') }}: {{ \Carbon\Carbon::parse($resolution->voting_closes_at)->format('d M Y') }}
                                                ({{ \Carbon\Carbon::parse($resolution->voting_closes_at)->diffForHumans() }})
                                            </small>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-20">
                                        <i class="ri-check-line font-36 text-muted d-block mb-10"></i>
                                        <p class="text-muted">{{ __('No open resolutions') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    {{-- End Row 2 --}}

                    {{-- Row 3: 30-day Calendar + Recent Documents --}}
                    <div class="row">
                        {{-- 30-day Meeting Calendar --}}
                        <div class="col-lg-7">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <h4 class="mb-20">
                                    <i class="ri-calendar-2-line me-2"></i>
                                    {{ __('Meetings — Next 30 Days') }}
                                    <small class="text-muted font-13 ms-2">({{ now()->format('d M') }} – {{ now()->addDays(30)->format('d M Y') }})</small>
                                </h4>

                                @php
                                    // Build a simple day-grid for the next 30 days
                                    $meetingDates = $calendarMeetings->map(fn($m) => \Carbon\Carbon::parse($m->scheduled_at)->format('Y-m-d'))->toArray();
                                    $meetingsByDate = [];
                                    foreach ($calendarMeetings as $m) {
                                        $d = \Carbon\Carbon::parse($m->scheduled_at)->format('Y-m-d');
                                        $meetingsByDate[$d][] = $m;
                                    }
                                    $startDay  = now()->startOfDay();
                                    $days      = [];
                                    for ($i = 0; $i < 30; $i++) {
                                        $days[] = $startDay->copy()->addDays($i);
                                    }
                                    // Group days into weeks (rows of 7)
                                    $weeks = array_chunk($days, 7);
                                @endphp

                                <div class="table-responsive">
                                    <table class="table table-bordered text-center mb-0" style="table-layout:fixed;">
                                        <thead>
                                            <tr>
                                                @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dow)
                                                    <th class="font-12 py-1">{{ $dow }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($weeks as $week)
                                                <tr>
                                                    @php
                                                        // Determine the ISO day (1=Mon…7=Sun) of the first day of the first week
                                                        $firstWeekday = $week[0]->dayOfWeekIso; // 1=Mon
                                                        $colStart = ($loop->first) ? $firstWeekday : 1;
                                                    @endphp
                                                    @if ($loop->first && $colStart > 1)
                                                        @for ($p = 1; $p < $colStart; $p++)
                                                            <td></td>
                                                        @endfor
                                                    @endif
                                                    @foreach ($week as $day)
                                                        @php
                                                            $key       = $day->format('Y-m-d');
                                                            $hasMeeting = isset($meetingsByDate[$key]);
                                                            $isToday    = $day->isToday();
                                                        @endphp
                                                        <td class="py-2 px-1"
                                                            style="
                                                                @if($isToday) background:#ebf8ff; font-weight:700; @endif
                                                                @if($hasMeeting) background:#e9d8fd; @endif
                                                                vertical-align:top; font-size:13px;">
                                                            <span @if($isToday) class="primary-color" @endif>{{ $day->format('d') }}</span>
                                                            @if ($hasMeeting)
                                                                @foreach ($meetingsByDate[$key] as $cm)
                                                                    <div class="mt-1">
                                                                        <span class="badge bg-primary text-white" style="font-size:9px;white-space:normal;word-break:break-word;">
                                                                            {{ Str::limit($cm->title, 14) }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-muted font-12 mt-10 mb-0">
                                    <span style="display:inline-block;width:12px;height:12px;background:#e9d8fd;border-radius:2px;margin-right:4px;vertical-align:middle;"></span>{{ __('Meeting scheduled') }}
                                    &nbsp;
                                    <span style="display:inline-block;width:12px;height:12px;background:#ebf8ff;border-radius:2px;margin-right:4px;vertical-align:middle;"></span>{{ __('Today') }}
                                </p>
                            </div>
                        </div>

                        {{-- Recent Documents --}}
                        <div class="col-lg-5">
                            <div class="bg-off-white theme-border radius-4 p-20 mb-25">
                                <div class="d-flex align-items-center justify-content-between mb-15">
                                    <h4 class="mb-0"><i class="ri-file-text-line me-2"></i>{{ __('Recent Documents') }}</h4>
                                    @if (Route::has('owner.governance.documents.index'))
                                        <a href="{{ route('owner.governance.documents.index') }}" class="theme-link font-14 d-flex align-items-center">
                                            {{ __('View All') }}<i class="ri-arrow-right-line ms-1"></i>
                                        </a>
                                    @endif
                                </div>

                                @forelse ($recentDocuments as $doc)
                                    <div class="d-flex align-items-center py-2 border-bottom">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="h-36 w-36 d-flex align-items-center justify-content-center bg-white radius-4 theme-border">
                                                <i class="ri-file-pdf-line orange-color font-18"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 font-14">{{ Str::limit($doc->title, 35) }}</h6>
                                            <small class="text-muted">
                                                {{ $doc->created_at ? $doc->created_at->format('d M Y') : '—' }}
                                            </small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-20">
                                        <i class="ri-folder-open-line font-36 text-muted d-block mb-10"></i>
                                        <p class="text-muted">{{ __('No documents uploaded yet') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    {{-- End Row 3 --}}

                </div>
            </div>
        </div>
    </div>
@endsection
