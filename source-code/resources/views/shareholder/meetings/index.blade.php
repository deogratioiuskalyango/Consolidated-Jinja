@extends('shareholder.layouts.app')
@php $navMeetingsActiveClass = 'active mm-active'; @endphp

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Page header --}}
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-calendar-event-line me-2 text-primary"></i>{{ __('Meetings') }}</h4>
                    <small class="text-muted">{{ __('All scheduled, completed, and cancelled meetings') }}</small>
                </div>
            </div>

            @if($meetings->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3" style="font-size:4rem; color:#d1d5db;">
                    <i class="ri-calendar-event-line"></i>
                </div>
                <h5 class="text-muted">{{ __('No meetings yet') }}</h5>
                <p class="text-muted small">{{ __('Meetings scheduled by your company will appear here.') }}</p>
            </div>
            @else

            {{-- Upcoming / scheduled meetings --}}
            @php
                $scheduled = $meetings->filter(fn($m) => $m->status == MEETING_STATUS_SCHEDULED);
                $past      = $meetings->filter(fn($m) => $m->status != MEETING_STATUS_SCHEDULED);
            @endphp

            @if($scheduled->count())
            <h6 class="text-uppercase text-muted small fw-bold mb-3 letter-spacing">{{ __('Upcoming') }}</h6>
            <div class="row g-3 mb-4">
                @foreach($scheduled as $meeting)
                @include('shareholder.meetings._card', ['meeting' => $meeting])
                @endforeach
            </div>
            @endif

            @if($past->count())
            <h6 class="text-uppercase text-muted small fw-bold mb-3 letter-spacing">{{ __('Past Meetings') }}</h6>
            <div class="row g-3">
                @foreach($past as $meeting)
                @include('shareholder.meetings._card', ['meeting' => $meeting])
                @endforeach
            </div>
            @endif

            <div class="mt-4">{{ $meetings->links() }}</div>
            @endif

        </div>
    </div>
</div>
@endsection
