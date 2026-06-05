@extends('tenant.layouts.app')

@push('style')
<style>
/* =====================================================
   TICKET DETAILS — Theme token overrides
   Works in both light and dark mode automatically
   ===================================================== */

/* ── Page wrapper ── */
.page-content-wrapper {
    background-color: var(--t-bg-card, #fff) !important;
}
.page-title-box {
    border-bottom-color: var(--t-border, #e2e8f0) !important;
}
.page-title-box h3 {
    color: var(--t-text-primary, #1e293b) !important;
}
.breadcrumb-item,
.breadcrumb-item a,
.breadcrumb-item.active {
    color: var(--t-text-muted, #94a3b8) !important;
}
.breadcrumb-item a:hover { color: var(--t-text-primary, #1e293b) !important; }

/* ── Outer panels (off-white containers) ── */
.ticket-details-left-side,
.ticket-details-right {
    background-color: var(--t-bg-hover, #f8fafc) !important;
    border-color: var(--t-border, #e2e8f0) !important;
}

/* ── Inner white content boxes ── */
.ticket-details-content-box.bg-white,
.ticket-details-write-reply-box {
    background-color: var(--t-bg-card, #fff) !important;
    border-color: var(--t-border, #e2e8f0) !important;
}

/* ── Section dividers ── */
.ticket-details-top-bar {
    border-bottom-color: var(--t-border, #e2e8f0) !important;
}
.ticket-item-top-bar {
    border-bottom-color: var(--t-border, #e2e8f0) !important;
}

/* ── Reply items ── */
.ticket-replies-item {
    background: var(--t-bg-hover, #f8fafc);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 16px;
}
.ticket-replies-title {
    border-bottom-color: var(--t-border, #e2e8f0) !important;
}
.ticket-replies-title small {
    color: var(--t-text-muted, #94a3b8) !important;
}

/* ── Info sidebar labels ── */
.ticket-details-right .ticket-item-content-box h5 {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--t-text-muted, #94a3b8) !important;
    margin-bottom: 4px;
}
.ticket-details-right .ticket-item-content-box p:not(.status-btn) {
    color: var(--t-text-primary, #1e293b) !important;
    font-weight: 500;
}

/* ── All headings ── */
.ticket-details-area-wrap h4,
.ticket-details-area-wrap h5,
.ticket-details-area-wrap h6 {
    color: var(--t-text-primary, #1e293b) !important;
}
.ticket-details-area-wrap p:not(.status-btn),
.ticket-details-area-wrap small {
    color: var(--t-text-secondary, #475569) !important;
}

/* ── Reply form ── */
.ticket-details-write-reply-box .form-control {
    background: var(--t-bg-hover, #f8fafc);
    border-color: var(--t-border, #e2e8f0);
    color: var(--t-text-primary, #1e293b);
}
.ticket-details-write-reply-box .form-control:focus {
    background: var(--t-bg-card, #fff);
    border-color: #2d3482;
    box-shadow: 0 0 0 3px rgba(45,52,130,.1);
    color: var(--t-text-primary, #1e293b);
}
.ticket-details-write-reply-box .form-control::placeholder {
    color: var(--t-text-muted, #94a3b8);
}
.label-text-title {
    color: var(--t-text-primary, #1e293b) !important;
}

/* ── Attachment thumbnails ── */
.tickets-attachment-item img.fit-image {
    border: 1px solid var(--t-border, #e2e8f0);
    border-radius: 8px;
}
.ticket-details-area-wrap a:not(.theme-btn):not(.venobox):not([download]) {
    color: var(--t-text-primary, #1e293b);
}

/* ── Dropify dark mode ── */
body[data-bs-theme="dark"] .dropify-wrapper {
    background: var(--t-bg-hover, #1e293b);
    border-color: var(--t-border, #334155) !important;
    color: var(--t-text-muted, #94a3b8);
}
body[data-bs-theme="dark"] .dropify-wrapper .dropify-message p {
    color: var(--t-text-muted, #94a3b8);
}
body[data-bs-theme="dark"] .dropify-wrapper:hover {
    background: var(--t-bg-card, #0f172a);
}
</style>
@endpush

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="page-content-wrapper bg-white p-30 radius-20">
                    <div class="row">
                        <div class="col-12">
                            <div
                                class="page-title-box d-sm-flex align-items-center justify-content-between border-bottom mb-20">
                                <div class="page-title-left">
                                    <h3 class="mb-sm-0">{{ $pageTitle }}</h3>
                                </div>
                                <div class="page-title-right">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}"
                                                title="{{ __('Dashboard') }}">{{ __('Dashboard') }}</a></li>
                                        <li class="breadcrumb-item" aria-current="page">{{ __('Tickets') }}</li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row ticket-details-area-wrap">
                        <div class="col-md-12 col-lg-12 col-xl-8 col-xxl-9">
                            <div class="ticket-details-left-side bg-off-white theme-border radius-4 p-25 mb-25">
                                <div class="ticket-details-content-box bg-white theme-border radius-4 p-25 mb-25">
                                    <div class="ticket-details-top-bar d-flex align-items-center border-bottom pb-20 mb-20">
                                        <h4>
                                            {{ $ticket->user->name }}
                                            <span class="theme-text-color">
                                                @if ($ticket->user->role == USER_ROLE_OWNER)
                                                    {{ __('Owner') }}
                                                @elseif($ticket->user->role == USER_ROLE_TENANT)
                                                    ({{ __('Tenant') }})
                                                @elseif($ticket->user->role == USER_ROLE_MAINTAINER)
                                                    {{ __('Maintainer') }}
                                                @endif
                                            </span>
                                        </h4>
                                    </div>
                                    <div class="ticket-details-content-box mb-3">
                                        <h6 class="mb-2">{{ __('Details') }}</h6>
                                        <p>{{ $ticket->details }}</p>
                                    </div>
                                    <div class="ticket-details-content-box attachment-ticket-item-content-box">
                                        <h6 class="mb-2">{{ __('Attachments') }}</h6>
                                        <div class="tickets-attachment-gallery row">
                                            @foreach ($ticket->attachments as $attachment)
                                                <div class="col-auto">
                                                    @if (in_array(pathinfo($attachment->file_name, PATHINFO_EXTENSION), imageExtensionList()))
                                                        <a href="{{ $attachment->FileUrl }}"
                                                            class="venobox tickets-attachment-item"
                                                            data-gall="attach{{ $attachment->id }}">
                                                            <img src="{{ $attachment->FileUrl }}" alt=""
                                                                class="fit-image">
                                                        </a>
                                                    @else
                                                        <a href="{{ $attachment->FileUrl }}" class="" download>
                                                            {{ $attachment->file_name }}
                                                        </a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @if (count($replies) > 0)
                                    <div class="ticket-details-content-box bg-white theme-border radius-4 p-25 pb-0 mb-25">
                                        <div
                                            class="ticket-details-top-bar d-flex align-items-center border-bottom pb-20 mb-20">
                                            <h4>{{ __('Ticket Replies') }}</h4>
                                        </div>
                                        @foreach ($replies as $reply)
                                            <div class="ticket-replies-item radius-4 p-25 mb-25">
                                                <div
                                                    class="ticket-replies-title d-flex align-items-center border-bottom pb-20 mb-20">
                                                    <h5>
                                                        @if ($reply->user_id == auth()->id())
                                                            {{ __('You - ') }}
                                                        @endif
                                                        {{ $reply->first_name }} {{ $reply->last_name }}
                                                        <span class="theme-text-color">
                                                            @if ($reply->role == USER_ROLE_OWNER)
                                                                ({{ __('Owner') }})
                                                            @elseif ($reply->role == USER_ROLE_TENANT)
                                                                ({{ __('Tenant') }})
                                                            @elseif ($reply->role == USER_ROLE_MAINTAINER)
                                                                ({{ __('Maintainer') }})
                                                            @elseif($reply->role == USER_ROLE_TEAM_MEMBER)
                                                                ({{__('Staff')}})
                                                            @endif
                                                        </span>
                                                        <small
                                                            class="d-block font-11">{{ $reply->created_at->diffForHumans() }}</small>
                                                    </h5>
                                                </div>
                                                <p>{{ $reply->reply }}</p>
                                                @if (count($reply->attachments) > 0)
                                                    <div
                                                        class="ticket-details-content-box attachment-ticket-item-content-box mt-3">
                                                        <h6 class="mb-2">{{ __('Attachments') }}</h6>
                                                        <div class="tickets-attachment-gallery row">
                                                            @foreach ($reply->attachments as $attachment)
                                                                <div class="col-auto">
                                                                    @if (in_array(pathinfo($attachment->file_name, PATHINFO_EXTENSION), imageExtensionList()))
                                                                        <a href="{{ $attachment->FileUrl }}"
                                                                            class="venobox tickets-attachment-item"
                                                                            data-gall="gallery01">
                                                                            <img src="{{ $attachment->FileUrl }}"
                                                                                alt="" class="fit-image">
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ $attachment->FileUrl }}" class=""
                                                                            download>
                                                                            {{ $attachment->file_name }}
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <div
                                    class="ticket-details-content-box ticket-details-write-reply-box bg-white theme-border radius-4 p-25">
                                    <div class="ticket-details-top-bar d-flex align-items-center border-bottom pb-20 mb-20">
                                        <h4>{{ __('Write a Reply') }}</h4>
                                    </div>
                                    <form class="ajax" action="{{ route('tenant.ticket.reply') }}" method="POST"
                                        data-handler="getShowMessage">
                                        <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                        <div class="mb-25">
                                            <label
                                                class="label-text-title color-heading font-medium mb-2">{{ __('Reply') }}</label>
                                            <textarea class="form-control" placeholder="{{ __('Reply') }}" name="reply"></textarea>
                                        </div>
                                        <div class="mb-25">
                                            <label
                                                class="label-text-title color-heading font-medium mb-2">{{ __('Upload Attachments') }}</label>
                                            <input type="file" id="attachments" name="attachments[]" class="dropify"
                                                data-height="220" multiple />
                                        </div>
                                        <div class="ticket-reply-submit-btns">
                                            <button type="submit" class="theme-btn"
                                                title="{{ __('Submit') }}">{{ __('Submit') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12 col-xl-4 col-xxl-3">
                            <div class="ticket-details-right bg-off-white theme-border radius-4 p-25 mb-25">
                                <div class="ticket-item-top-bar d-flex align-items-center border-bottom pb-20 mb-20">
                                    <a href="#">
                                        <h4 class="mb-1">{{ __('Ticket Info') }}</h4>
                                    </a>
                                    <div class="flex-grow-1 ms-2 text-end">
                                        <h5>{{ __('ID') }}: #{{ $ticket->ticket_no }}</h5>
                                    </div>
                                </div>
                                <div class="ticket-item-content-box mb-3">
                                    <h5 class="mb-2">{{ __('Title') }}</h5>
                                    <p>{{ $ticket->title }}</p>
                                </div>
                                <div class="ticket-item-content-box mb-3">
                                    <h5 class="mb-2">{{ __('Topic') }}</h5>
                                    <p>{{ $ticket->topic->name }}</p>
                                </div>
                                <div class="ticket-item-content-box mb-3">
                                    <h5 class="mb-2">{{ __('Property') }}</h5>
                                    <p>{{ $ticket->property->name }}</p>
                                </div>
                                <div class="ticket-item-content-box mb-3">
                                    <h5 class="mb-2">{{ __('Unit') }}</h5>
                                    <p>{{ $ticket->unit->unit_name }}</p>
                                </div>
                                <div class="ticket-item-content-box mb-3">
                                    <h5 class="mb-2">{{ __('Status') }}</h5>
                                    @if ($ticket->status == TICKET_STATUS_OPEN)
                                        <p class="status-btn status-btn-orange">{{ __('Open') }}</p>
                                    @elseif ($ticket->status == TICKET_STATUS_INPROGRESS)
                                        <p class="status-btn status-btn-blue">{{ __('Inprogress') }}</p>
                                    @elseif ($ticket->status == TICKET_STATUS_REOPEN)
                                        <p class="status-btn status-btn-red">{{ __('Reopen') }}</p>
                                    @elseif ($ticket->status == TICKET_STATUS_RESOLVED)
                                        <p class="status-btn status-btn-green">{{ __('Resolved') }}</p>
                                    @else
                                        <p class="status-btn status-btn-red">{{ __('Close') }}</p>
                                    @endif
                                </div>
                                <div class="ticket-item-content-box mb-3">
                                    <h5 class="mb-2">{{ __('Opened') }}</h5>
                                    <p>{{ $ticket->created_at->format('Y-m-d') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ asset('assets/js/custom/ticket.js') }}"></script>
@endpush
