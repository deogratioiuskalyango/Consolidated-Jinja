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

    $attendee    = $meeting->attendees->first();
    $isScheduled = $meeting->status == MEETING_STATUS_SCHEDULED;
    $isInvited   = $attendee !== null;
    $confirmed   = $attendee && $attendee->confirmed;

    // Countdown
    $daysLeft = $isScheduled && $meeting->scheduled_at ? now()->diffInDays($meeting->scheduled_at, false) : null;
@endphp

<div class="col-12 col-md-6 col-xl-4">
    <div class="card h-100 border-0 shadow-sm position-relative overflow-hidden"
         style="border-top: 4px solid {{ $tc['color'] }} !important;">

        {{-- Recurring badge --}}
        @if($meeting->is_recurring || $meeting->parent_meeting_id)
        <span class="position-absolute top-0 end-0 mt-2 me-2 badge"
              style="background:{{ $tc['bg'] }};color:{{ $tc['color'] }};font-size:10px;"
              title="{{ __('Recurring meeting') }}">
            <i class="ri-repeat-line me-1"></i>{{ __('Recurring') }}
        </span>
        @endif

        <div class="card-body d-flex flex-column gap-3 p-4">

            {{-- Type + Status --}}
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge" style="background:{{ $tc['bg'] }};color:{{ $tc['color'] }};">{{ $tc['label'] }}</span>
                <span class="badge {{ $sc['badge'] }}">{{ __($sc['label']) }}</span>
                @if($isInvited && $isScheduled)
                    @if($confirmed)
                        <span class="badge bg-success bg-opacity-10 text-success"><i class="ri-check-line me-1"></i>{{ __('Attending') }}</span>
                    @else
                        <span class="badge bg-warning bg-opacity-10 text-warning"><i class="ri-time-line me-1"></i>{{ __('RSVP Pending') }}</span>
                    @endif
                @endif
            </div>

            {{-- Title --}}
            <div>
                <h5 class="mb-1 fw-bold lh-sm">{{ $meeting->title }}</h5>
                @if($meeting->reference_number)
                <small class="text-muted">{{ $meeting->reference_number }}</small>
                @endif
            </div>

            {{-- Meta info --}}
            <div class="d-flex flex-column gap-2">
                @if($meeting->scheduled_at)
                <div class="d-flex align-items-center gap-2 text-body-secondary small">
                    <i class="ri-calendar-line text-primary"></i>
                    <span>{{ $meeting->scheduled_at->format('D, d M Y') }}</span>
                    @if($meeting->scheduled_at->format('H:i') !== '00:00')
                    <span class="text-muted">at {{ $meeting->scheduled_at->format('H:i') }}</span>
                    @endif
                </div>
                @endif

                <div class="d-flex align-items-center gap-2 text-body-secondary small">
                    <i class="{{ $modeIcon }} text-primary"></i>
                    <span>{{ $modeLabel }}</span>
                    @if($meeting->venue)
                        <span class="text-muted">— {{ Str::limit($meeting->venue, 30) }}</span>
                    @endif
                </div>

                @if($meeting->virtual_link && $meeting->status == MEETING_STATUS_SCHEDULED)
                <div class="d-flex align-items-center gap-2 small">
                    <i class="ri-link text-success"></i>
                    <a href="{{ $meeting->virtual_link }}" target="_blank" rel="noopener" class="text-success fw-medium">
                        {{ __('Join virtual meeting') }}
                    </a>
                </div>
                @endif
            </div>

            {{-- Countdown for upcoming --}}
            @if($isScheduled && $daysLeft !== null)
            <div class="rounded-3 p-2 text-center"
                 style="background:{{ $daysLeft <= 3 ? '#fef2f2' : '#eff6ff' }};
                        color:{{ $daysLeft <= 3 ? '#dc2626' : '#1d4ed8' }};">
                @if($daysLeft < 0)
                    <small><i class="ri-time-line me-1"></i>{{ __('Overdue by') }} {{ abs($daysLeft) }} {{ __('day(s)') }}</small>
                @elseif($daysLeft === 0)
                    <small class="fw-bold"><i class="ri-alarm-line me-1"></i>{{ __('Today!') }}</small>
                @elseif($daysLeft === 1)
                    <small class="fw-bold"><i class="ri-alarm-line me-1"></i>{{ __('Tomorrow') }}</small>
                @else
                    <small><i class="ri-calendar-check-line me-1"></i>{{ __('In') }} {{ $daysLeft }} {{ __('days') }}</small>
                @endif
            </div>
            @endif

            <div class="mt-auto d-flex gap-2">
                <a href="{{ route('shareholder.meetings.show', $meeting) }}"
                   class="btn btn-primary btn-sm flex-fill">
                    <i class="ri-eye-line me-1"></i>{{ __('View Details') }}
                </a>
                @if($isInvited && $isScheduled && !$confirmed)
                <a href="{{ route('shareholder.meetings.show', $meeting) }}#rsvp"
                   class="btn btn-outline-success btn-sm flex-fill">
                    <i class="ri-check-line me-1"></i>{{ __('RSVP') }}
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
