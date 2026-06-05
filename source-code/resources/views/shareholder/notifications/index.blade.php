@extends('shareholder.layouts.app')
@php $navNotificationsActiveClass = 'active mm-active'; @endphp

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-0 fw-bold"><i class="ri-notification-2-line me-2 text-primary"></i>{{ __('Notifications') }}</h4>
                    <small class="text-muted">{{ __('All your alerts, reminders, and announcements') }}</small>
                </div>
                <form action="{{ route('shareholder.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="ri-check-double-line me-1"></i>{{ __('Mark All Read') }}
                    </button>
                </form>
            </div>

            @if($notifications->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3" style="font-size:4rem; color:#d1d5db;">
                    <i class="ri-notification-off-line"></i>
                </div>
                <h5 class="text-muted">{{ __('All caught up!') }}</h5>
                <p class="text-muted small">{{ __('No notifications yet. You\'ll see alerts here when there\'s something that needs your attention.') }}</p>
            </div>
            @else

            <div class="d-flex flex-column gap-2">
                @foreach($notifications as $notification)
                @php
                    $typeConfig = [
                        'meeting'          => ['icon' => 'ri-calendar-event-line', 'color' => '#3b82f6', 'bg' => '#eff6ff'],
                        'approval_request' => ['icon' => 'ri-checkbox-circle-line', 'color' => '#d97706', 'bg' => '#fffbeb'],
                        'new_resolution'   => ['icon' => 'ri-survey-line',           'color' => '#7c3aed', 'bg' => '#f5f3ff'],
                        'vote_reminder'    => ['icon' => 'ri-vote-line',             'color' => '#7c3aed', 'bg' => '#f5f3ff'],
                        'dividend'         => ['icon' => 'ri-coins-line',            'color' => '#059669', 'bg' => '#ecfdf5'],
                        'document'         => ['icon' => 'ri-file-list-line',        'color' => '#0891b2', 'bg' => '#ecfeff'],
                        'share_transfer'   => ['icon' => 'ri-exchange-funds-line',   'color' => '#dc2626', 'bg' => '#fef2f2'],
                    ];
                    $tc = $typeConfig[$notification->type] ?? ['icon' => 'ri-notification-2-line', 'color' => '#6b7280', 'bg' => '#f9fafb'];
                @endphp

                <div class="card border-0 shadow-sm notification-item {{ !$notification->is_read ? 'unread' : '' }}"
                     data-id="{{ $notification->id }}"
                     style="{{ !$notification->is_read ? 'border-left:4px solid ' . $tc['color'] . ' !important;' : '' }}">
                    <div class="card-body d-flex align-items-start gap-3 py-3">

                        {{-- Icon --}}
                        <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                             style="width:44px;height:44px;background:{{ $tc['bg'] }};color:{{ $tc['color'] }};font-size:1.2rem;">
                            <i class="{{ $tc['icon'] }}"></i>
                        </div>

                        {{-- Content --}}
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
                                <h6 class="mb-1 {{ !$notification->is_read ? 'fw-bold' : 'fw-semibold' }} text-body">
                                    {{ $notification->title }}
                                </h6>
                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    @if(!$notification->is_read)
                                    <button type="button"
                                            class="btn btn-link btn-sm p-0 text-primary mark-read-btn"
                                            data-id="{{ $notification->id }}"
                                            data-url="{{ route('shareholder.notifications.read', $notification) }}">
                                        <i class="ri-check-line"></i> {{ __('Mark read') }}
                                    </button>
                                    @endif
                                </div>
                            </div>

                            <p class="mb-2 text-muted small lh-base">{{ $notification->message }}</p>

                            @if($notification->action_url)
                            <a href="{{ $notification->action_url }}" class="btn btn-sm"
                               style="background:{{ $tc['bg'] }};color:{{ $tc['color'] }};border:1px solid {{ $tc['color'] }}20;font-size:12px;">
                                @php
                                    $ctaLabels = [
                                        'meeting'          => 'View Meeting',
                                        'approval_request' => 'Review & Approve',
                                        'new_resolution'   => 'Vote Now',
                                        'vote_reminder'    => 'Cast Your Vote',
                                        'dividend'         => 'View Dividend',
                                        'document'         => 'View Document',
                                    ];
                                    $ctaLabel = $ctaLabels[$notification->type] ?? 'View Details';
                                @endphp
                                <i class="ri-arrow-right-circle-line me-1"></i>{{ __($ctaLabel) }}
                            </a>
                            @endif
                        </div>

                        {{-- Unread dot --}}
                        @if(!$notification->is_read)
                        <div class="flex-shrink-0 pt-1">
                            <span class="rounded-circle d-block"
                                  style="width:10px;height:10px;background:{{ $tc['color'] }};"></span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-4">{{ $notifications->links() }}</div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function () {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $(document).on('click', '.mark-read-btn', function (e) {
        e.preventDefault();
        var btn  = $(this);
        var url  = btn.data('url');
        var card = btn.closest('.notification-item');

        $.post(url, { _token: csrfToken }, function () {
            card.removeClass('unread').css('border-left', '');
            card.find('.rounded-circle[style*="width:10px"]').remove();
            btn.closest('.d-flex').find('.mark-read-btn').remove();
            card.find('h6').removeClass('fw-bold').addClass('fw-semibold');
        });
    });
});
</script>
@endpush
