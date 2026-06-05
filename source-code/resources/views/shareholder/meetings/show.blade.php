@extends('shareholder.layouts.app')
@php $navMeetingsActiveClass = 'active mm-active'; @endphp

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <a href="{{ route('shareholder.meetings.index') }}" class="text-muted small text-decoration-none">
                        <i class="ri-arrow-left-line me-1"></i>{{ __('Back to Meetings') }}
                    </a>
                    <h4 class="mb-0 fw-bold mt-1">{{ $meeting->title }}</h4>
                </div>
                @if($meeting->virtual_link && $meeting->status == MEETING_STATUS_SCHEDULED)
                <a href="{{ $meeting->virtual_link }}" target="_blank" rel="noopener"
                   class="btn btn-success btn-sm">
                    <i class="ri-video-line me-1"></i>{{ __('Join Virtual Meeting') }}
                </a>
                @endif
            </div>

            @php
                $typeConfig = [
                    MEETING_TYPE_AGM   => ['label' => 'AGM',   'color' => '#3b82f6', 'bg' => '#eff6ff'],
                    MEETING_TYPE_BOARD => ['label' => 'Board',  'color' => '#0891b2', 'bg' => '#ecfeff'],
                    MEETING_TYPE_EGM   => ['label' => 'EGM',   'color' => '#d97706', 'bg' => '#fffbeb'],
                    MEETING_TYPE_OTHER => ['label' => 'Other',  'color' => '#6b7280', 'bg' => '#f9fafb'],
                ];
                $tc = $typeConfig[$meeting->type] ?? $typeConfig[MEETING_TYPE_OTHER];

                $statusConfig = [
                    MEETING_STATUS_SCHEDULED => ['label' => 'Scheduled', 'badge' => 'bg-primary'],
                    MEETING_STATUS_COMPLETED => ['label' => 'Completed', 'badge' => 'bg-success'],
                    MEETING_STATUS_CANCELLED => ['label' => 'Cancelled', 'badge' => 'bg-danger'],
                ];
                $sc = $statusConfig[$meeting->status] ?? ['label' => 'Unknown', 'badge' => 'bg-secondary'];

                $modeMap   = [MEETING_MODE_IN_PERSON => 'In-person', MEETING_MODE_VIRTUAL => 'Virtual', MEETING_MODE_HYBRID => 'Hybrid'];
                $modeIcons = [MEETING_MODE_IN_PERSON => 'ri-building-line', MEETING_MODE_VIRTUAL => 'ri-video-line', MEETING_MODE_HYBRID => 'ri-map-pin-2-line'];
                $modeLabel = $modeMap[$meeting->mode ?? MEETING_MODE_IN_PERSON] ?? 'In-person';
                $modeIcon  = $modeIcons[$meeting->mode ?? MEETING_MODE_IN_PERSON] ?? 'ri-building-line';

                $isScheduled = $meeting->status == MEETING_STATUS_SCHEDULED;
                $isInvited   = $myAttendance !== null;
                $isConfirmed = $myAttendance && $myAttendance->confirmed;
                $isDue       = $meeting->scheduled_at && $meeting->scheduled_at->isPast();
            @endphp

            <div class="row g-4">

                {{-- Left: Meeting info --}}
                <div class="col-lg-8">

                    {{-- Badges --}}
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <span class="badge" style="background:{{ $tc['bg'] }};color:{{ $tc['color'] }};font-size:13px;padding:6px 14px;">{{ $tc['label'] }}</span>
                        <span class="badge {{ $sc['badge'] }}" style="font-size:13px;padding:6px 14px;">{{ __($sc['label']) }}</span>
                        @if($meeting->is_recurring || $meeting->parent_meeting_id)
                        <span class="badge bg-info bg-opacity-10 text-info" style="font-size:13px;padding:6px 14px;"><i class="ri-repeat-line me-1"></i>{{ __('Recurring') }}</span>
                        @endif
                    </div>

                    {{-- Countdown banner --}}
                    @if($isScheduled && $meeting->scheduled_at && !$isDue)
                    <div class="card border-0 mb-4" style="background:linear-gradient(135deg,#1d4ed8,#2563eb);color:#fff;border-radius:16px;">
                        <div class="card-body py-3 px-4">
                            <p class="mb-1 opacity-75 small">{{ __('Meeting starts in') }}</p>
                            <div class="d-flex gap-4 align-items-end" id="meetingCountdown" data-ts="{{ $meeting->scheduled_at->timestamp }}">
                                <div class="text-center"><div class="fw-bold" style="font-size:2rem;" id="cdDays">--</div><small class="opacity-75">{{ __('Days') }}</small></div>
                                <div class="text-center"><div class="fw-bold" style="font-size:2rem;" id="cdHours">--</div><small class="opacity-75">{{ __('Hours') }}</small></div>
                                <div class="text-center"><div class="fw-bold" style="font-size:2rem;" id="cdMins">--</div><small class="opacity-75">{{ __('Mins') }}</small></div>
                                <div class="text-center"><div class="fw-bold" style="font-size:2rem;" id="cdSecs">--</div><small class="opacity-75">{{ __('Secs') }}</small></div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Meeting details card --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom">
                            <h5 class="mb-0 fw-semibold"><i class="ri-information-line me-2 text-primary"></i>{{ __('Meeting Details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background:#f8fafc;">
                                        <div class="flex-shrink-0 text-primary"><i class="ri-calendar-line fs-5"></i></div>
                                        <div>
                                            <div class="text-muted small">{{ __('Date') }}</div>
                                            <div class="fw-semibold">{{ $meeting->scheduled_at ? $meeting->scheduled_at->format('D, d M Y') : '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background:#f8fafc;">
                                        <div class="flex-shrink-0 text-primary"><i class="ri-time-line fs-5"></i></div>
                                        <div>
                                            <div class="text-muted small">{{ __('Time') }}</div>
                                            <div class="fw-semibold">
                                                {{ $meeting->scheduled_at && $meeting->scheduled_at->format('H:i') !== '00:00'
                                                    ? $meeting->scheduled_at->format('H:i')
                                                    : '—' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background:#f8fafc;">
                                        <div class="flex-shrink-0 text-primary"><i class="{{ $modeIcon }} fs-5"></i></div>
                                        <div>
                                            <div class="text-muted small">{{ __('Mode') }}</div>
                                            <div class="fw-semibold">{{ $modeLabel }}</div>
                                        </div>
                                    </div>
                                </div>
                                @if($meeting->venue)
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background:#f8fafc;">
                                        <div class="flex-shrink-0 text-primary"><i class="ri-map-pin-line fs-5"></i></div>
                                        <div>
                                            <div class="text-muted small">{{ __('Venue') }}</div>
                                            <div class="fw-semibold">{{ $meeting->venue }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($meeting->virtual_link)
                                <div class="col-12">
                                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 border border-success border-opacity-25" style="background:#f0fdf4;">
                                        <div class="flex-shrink-0 text-success"><i class="ri-video-line fs-5"></i></div>
                                        <div class="flex-grow-1">
                                            <div class="text-muted small">{{ __('Virtual Meeting Link') }}</div>
                                            <a href="{{ $meeting->virtual_link }}" target="_blank" rel="noopener" class="fw-semibold text-success text-break">
                                                {{ $meeting->virtual_link }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if($meeting->agenda)
                            <div class="mt-4">
                                <h6 class="fw-semibold text-muted mb-2"><i class="ri-list-check me-1"></i>{{ __('Agenda') }}</h6>
                                <div class="bg-light rounded-3 p-3" style="white-space:pre-wrap;font-family:inherit;font-size:.9rem;line-height:1.7;">{{ $meeting->agenda }}</div>
                            </div>
                            @endif

                            @if($meeting->minutes && $meeting->status == MEETING_STATUS_COMPLETED)
                            <div class="mt-4">
                                <h6 class="fw-semibold text-muted mb-2"><i class="ri-file-text-line me-1"></i>{{ __('Meeting Minutes') }}</h6>
                                <div class="bg-light rounded-3 p-3" style="white-space:pre-wrap;font-family:inherit;font-size:.9rem;line-height:1.7;">{{ $meeting->minutes }}</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Resolutions linked --}}
                    @if($meeting->resolutions && $meeting->resolutions->count())
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom">
                            <h5 class="mb-0 fw-semibold"><i class="ri-survey-line me-2 text-primary"></i>{{ __('Linked Resolutions') }}</h5>
                        </div>
                        <div class="card-body p-0">
                            @foreach($meeting->resolutions as $res)
                            <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                                <div>
                                    <div class="fw-medium">{{ $res->title }}</div>
                                    <small class="text-muted">{{ $res->reference_number ?? '' }}</small>
                                </div>
                                <a href="{{ route('shareholder.resolutions.show', $res) }}" class="btn btn-sm btn-outline-primary">
                                    {{ __('Vote') }} <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Documents --}}
                    @if($meeting->documents && $meeting->documents->count())
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom">
                            <h5 class="mb-0 fw-semibold"><i class="ri-file-list-line me-2 text-primary"></i>{{ __('Documents') }}</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach($meeting->documents as $doc)
                            <a href="{{ $doc->file ? asset('storage/' . $doc->file->file_name) : '#' }}"
                               target="_blank" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
                                <i class="ri-file-pdf-line fs-5 text-danger"></i>
                                <div>
                                    <div class="fw-medium">{{ $doc->title ?? $doc->file?->original_name }}</div>
                                    <small class="text-muted">{{ $doc->created_at->format('d M Y') }}</small>
                                </div>
                                <i class="ri-download-line ms-auto text-muted"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>

                {{-- Right: RSVP + Attendance --}}
                <div class="col-lg-4">

                    {{-- My attendance status --}}
                    @if($isInvited && $isConfirmed)
                    <div class="card border-0 shadow-sm mb-4 border-start border-4 {{ $myAttendance->confirmed ? 'border-success' : 'border-danger' }}">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3"><i class="ri-user-check-line me-2"></i>{{ __('My RSVP') }}</h6>
                            @if($myAttendance->confirmed)
                            <div class="d-flex align-items-center gap-2 text-success mb-2">
                                <i class="ri-check-double-line fs-5"></i>
                                <span class="fw-semibold">{{ __('Attending') }}</span>
                            </div>
                            @else
                            <div class="d-flex align-items-center gap-2 text-danger mb-2">
                                <i class="ri-close-line fs-5"></i>
                                <span class="fw-semibold">{{ __('Sent Apology') }}</span>
                            </div>
                            @endif
                            @if($myAttendance->attendance_mode)
                            <p class="mb-1 small text-muted">{{ __('Mode:') }} <strong>{{ ucfirst($myAttendance->attendance_mode) }}</strong></p>
                            @endif
                            @if($myAttendance->apology_note)
                            <p class="mb-0 small text-muted">{{ __('Note:') }} {{ $myAttendance->apology_note }}</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- RSVP form --}}
                    @if($isInvited && $isScheduled && !$isConfirmed)
                    <div class="card border-0 shadow-sm mb-4" id="rsvp">
                        <div class="card-header bg-transparent border-bottom">
                            <h5 class="mb-0 fw-semibold"><i class="ri-calendar-check-line me-2 text-primary"></i>{{ __('Confirm Attendance') }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('shareholder.meetings.confirm', $meeting) }}" method="POST">
                                @csrf
                                <input type="hidden" name="confirmed" value="1">

                                @if(in_array($meeting->mode ?? MEETING_MODE_IN_PERSON, [MEETING_MODE_IN_PERSON, MEETING_MODE_HYBRID, MEETING_MODE_VIRTUAL]))
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">{{ __('Attend via') }}</label>
                                    <div class="d-flex gap-2 flex-wrap">
                                        @if(in_array($meeting->mode ?? 0, [MEETING_MODE_IN_PERSON, MEETING_MODE_HYBRID]))
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="attendance_mode" id="modeIP" value="1"
                                                   {{ old('attendance_mode', 1) == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modeIP">
                                                <i class="ri-building-line me-1"></i>{{ __('In-person') }}
                                            </label>
                                        </div>
                                        @endif
                                        @if(in_array($meeting->mode ?? 0, [MEETING_MODE_VIRTUAL, MEETING_MODE_HYBRID]))
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="attendance_mode" id="modeVirt" value="2"
                                                   {{ old('attendance_mode') == 2 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modeVirt">
                                                <i class="ri-video-line me-1"></i>{{ __('Virtual') }}
                                            </label>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                <button type="submit" class="btn btn-success w-100 mb-2">
                                    <i class="ri-check-line me-1"></i>{{ __('Confirm Attendance') }}
                                </button>
                            </form>

                            <hr>

                            <form action="{{ route('shareholder.meetings.confirm', $meeting) }}" method="POST">
                                @csrf
                                <input type="hidden" name="confirmed" value="0">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">{{ __('Send Apology') }}</label>
                                    <textarea class="form-control" name="apology_note" rows="2"
                                              placeholder="{{ __('Reason for absence (optional)') }}">{{ old('apology_note') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="ri-close-line me-1"></i>{{ __('Send Apology') }}
                                </button>
                            </form>
                        </div>
                    </div>
                    @elseif(!$isInvited)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body text-center text-muted py-4">
                            <i class="ri-calendar-close-line fs-2 d-block mb-2"></i>
                            <p class="mb-0">{{ __('You are not on the invite list for this meeting.') }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Attendees summary --}}
                    @if($meeting->attendees->count())
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom">
                            <h5 class="mb-0 fw-semibold"><i class="ri-group-line me-2 text-primary"></i>{{ __('Attendees') }}</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $confirmed    = $meeting->attendees->where('confirmed', true)->count();
                                $unconfirmed  = $meeting->attendees->where('confirmed', false)->count();
                                $total        = $meeting->attendees->count();
                            @endphp
                            <div class="d-flex gap-3 mb-3">
                                <div class="text-center flex-fill">
                                    <div class="fw-bold fs-5 text-success">{{ $confirmed }}</div>
                                    <small class="text-muted">{{ __('Confirmed') }}</small>
                                </div>
                                <div class="text-center flex-fill">
                                    <div class="fw-bold fs-5 text-warning">{{ $unconfirmed }}</div>
                                    <small class="text-muted">{{ __('Pending') }}</small>
                                </div>
                                <div class="text-center flex-fill">
                                    <div class="fw-bold fs-5">{{ $total }}</div>
                                    <small class="text-muted">{{ __('Invited') }}</small>
                                </div>
                            </div>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar bg-success" style="width:{{ $total > 0 ? round(($confirmed/$total)*100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('script')
<script>
(function () {
    var el = document.getElementById('meetingCountdown');
    if (!el) return;
    var ts = parseInt(el.getAttribute('data-ts'), 10) * 1000;

    function update() {
        var diff = ts - Date.now();
        if (diff <= 0) { el.closest('.card').remove(); return; }
        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);
        document.getElementById('cdDays').textContent  = String(d).padStart(2,'0');
        document.getElementById('cdHours').textContent = String(h).padStart(2,'0');
        document.getElementById('cdMins').textContent  = String(m).padStart(2,'0');
        document.getElementById('cdSecs').textContent  = String(s).padStart(2,'0');
    }
    update();
    setInterval(update, 1000);
})();
</script>
@endpush
