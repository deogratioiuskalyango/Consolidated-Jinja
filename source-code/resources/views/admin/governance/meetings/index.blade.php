@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Page Header --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div>
                    <h4 class="mb-0 fw-bold">{{ $pageTitle }}</h4>
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                    </ol>
                </div>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2"
                        data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="ri-calendar-add-line fs-5"></i>
                    <span>{{ __('Schedule Meeting') }}</span>
                </button>
            </div>

            @if(!$googleMeetReady)
            <div class="alert alert-warning alert-dismissible d-flex align-items-center gap-2 mb-3 py-2" role="alert">
                <i class="ri-google-line fs-5 flex-shrink-0"></i>
                <div class="small">
                    <strong>{{ __('Google Meet not configured') }}</strong> —
                    {{ __('Set') }} <code>GOOGLE_MEET_SERVICE_ACCOUNT_JSON</code> {{ __('in your') }} <code>.env</code> {{ __('to enable virtual meeting creation.') }}
                </div>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{-- Stats Row --}}
            <div class="stats-grid mb-3">
                @php
                    $stats = [
                        ['count' => $totalCount,     'label' => __('Total'),     'icon' => 'ri-calendar-event-line', 'bg' => 'rgba(13,110,253,.1)',  'color' => '#2563eb'],
                        ['count' => $scheduledCount, 'label' => __('Scheduled'), 'icon' => 'ri-time-line',           'bg' => 'rgba(13,110,253,.1)',  'color' => '#2563eb'],
                        ['count' => $completedCount, 'label' => __('Completed'), 'icon' => 'ri-checkbox-circle-line','bg' => 'rgba(25,135,84,.1)',   'color' => '#16a34a'],
                        ['count' => $cancelledCount, 'label' => __('Cancelled'), 'icon' => 'ri-close-circle-line',   'bg' => 'rgba(220,53,69,.1)',   'color' => '#dc2626'],
                    ];
                @endphp
                @foreach($stats as $stat)
                <div class="stat-card">
                    <div class="stat-icon" style="background:{{ $stat['bg'] }}">
                        <i class="{{ $stat['icon'] }}" style="color:{{ $stat['color'] }}"></i>
                    </div>
                    <div class="stat-body">
                        <div class="stat-count">{{ $stat['count'] }}</div>
                        <div class="stat-label">{{ $stat['label'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Main Card --}}
            <div class="card border-0 shadow-sm">

                {{-- Card Header: Filters + Bulk Actions --}}
                <div class="card-header bg-white border-bottom py-3">
                    {{-- Bulk action bar (visible when rows selected) --}}
                    <div class="d-none align-items-center gap-2 mb-2 p-2 rounded-3" id="bulkBar"
                         style="background:#eff6ff;border:1px solid #bfdbfe">
                        <i class="ri-checkbox-multiple-line text-primary"></i>
                        <span class="fw-medium text-primary small" id="selectedCount">0 {{ __('selected') }}</span>
                        <div class="ms-auto d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-success bulk-action-btn" data-action="complete">
                                <i class="ri-check-double-line me-1"></i>{{ __('Complete') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-warning bulk-action-btn" data-action="cancel">
                                <i class="ri-close-circle-line me-1"></i>{{ __('Cancel') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-danger bulk-action-btn" data-action="delete">
                                <i class="ri-delete-bin-line me-1"></i>{{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                    {{-- Filter row --}}
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="input-group input-group-sm" style="max-width:220px">
                            <span class="input-group-text bg-light border-end-0"><i class="ri-search-line text-muted"></i></span>
                            <input type="text" id="meetingSearch" class="form-control border-start-0 bg-light ps-0"
                                   placeholder="{{ __('Search meetings…') }}">
                        </div>
                        <select id="filterType" class="form-select form-select-sm" style="max-width:160px">
                            <option value="">{{ __('All Types') }}</option>
                            <option value="AGM">{{ __('AGM') }}</option>
                            <option value="Board">{{ __('Board') }}</option>
                            <option value="EGM">{{ __('EGM') }}</option>
                            <option value="Other">{{ __('Other') }}</option>
                        </select>
                        <select id="filterStatus" class="form-select form-select-sm" style="max-width:160px">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="Scheduled">{{ __('Scheduled') }}</option>
                            <option value="Completed">{{ __('Completed') }}</option>
                            <option value="Cancelled">{{ __('Cancelled') }}</option>
                        </select>
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('View toggle') }}">
                                <button type="button" id="viewTable" class="btn btn-outline-secondary active" title="{{ __('Table view') }}">
                                    <i class="ri-table-line"></i>
                                </button>
                                <button type="button" id="viewCards" class="btn btn-outline-secondary" title="{{ __('Card view') }}">
                                    <i class="ri-layout-grid-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TABLE VIEW --}}
                <div id="tableView">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="meetingsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" width="36">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>{{ __('Meeting') }}</th>
                                    <th class="d-none d-md-table-cell">{{ __('Type') }}</th>
                                    <th class="d-none d-lg-table-cell">{{ __('Mode') }}</th>
                                    <th>{{ __('Date & Time') }}</th>
                                    <th class="d-none d-xl-table-cell">{{ __('Chairman') }}</th>
                                    <th class="d-none d-lg-table-cell">{{ __('Venue / Link') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="d-none d-md-table-cell text-center">{{ __('Attendees') }}</th>
                                    <th class="text-end pe-3">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="meetingsTbody">
                                @forelse($meetings as $meeting)
                                @php
                                    $typeMeta = [
                                        MEETING_TYPE_AGM   => ['label'=>'AGM',   'class'=>'badge-type-agm'],
                                        MEETING_TYPE_BOARD => ['label'=>'Board', 'class'=>'badge-type-board'],
                                        MEETING_TYPE_EGM   => ['label'=>'EGM',   'class'=>'badge-type-egm'],
                                        MEETING_TYPE_OTHER => ['label'=>'Other', 'class'=>'badge-type-other'],
                                    ];
                                    $tm = $typeMeta[$meeting->type] ?? $typeMeta[MEETING_TYPE_OTHER];
                                    $statusMeta = [
                                        MEETING_STATUS_SCHEDULED => ['label'=>'Scheduled','class'=>'badge-status-scheduled'],
                                        MEETING_STATUS_COMPLETED => ['label'=>'Completed','class'=>'badge-status-completed'],
                                        MEETING_STATUS_CANCELLED => ['label'=>'Cancelled','class'=>'badge-status-cancelled'],
                                    ];
                                    $sm = $statusMeta[$meeting->status] ?? ['label'=>'Unknown','class'=>'bg-secondary'];
                                    $mode = $meeting->mode ?? MEETING_MODE_IN_PERSON;
                                @endphp
                                <tr class="meeting-row" data-title="{{ strtolower($meeting->title) }}"
                                    data-type="{{ $tm['label'] }}" data-status="{{ $sm['label'] }}">
                                    <td class="ps-3">
                                        <input type="checkbox" class="form-check-input row-check" value="{{ $meeting->id }}">
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.governance.meetings.show', $meeting) }}"
                                           class="fw-semibold text-dark text-decoration-none d-block lh-sm mb-1 meeting-title-link">
                                            {{ $meeting->title }}
                                            @if($meeting->is_recurring && !$meeting->parent_meeting_id)
                                                <span class="badge badge-recurring ms-1" title="{{ __('Series') }}"><i class="ri-repeat-line"></i></span>
                                            @elseif($meeting->parent_meeting_id)
                                                <span class="badge badge-instance ms-1" title="{{ __('Instance') }}"><i class="ri-git-branch-line"></i></span>
                                            @endif
                                        </a>
                                        <small class="text-muted">{{ $meeting->reference_number }}</small>
                                        {{-- Mobile-only extras --}}
                                        <div class="d-md-none mt-1 d-flex flex-wrap gap-1">
                                            <span class="meeting-type-badge {{ $tm['class'] }}">{{ $tm['label'] }}</span>
                                            <span class="meeting-status-badge {{ $sm['class'] }}">{{ __($sm['label']) }}</span>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        <span class="meeting-type-badge {{ $tm['class'] }}">{{ $tm['label'] }}</span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        @if($mode == MEETING_MODE_VIRTUAL)
                                            <span class="badge-mode-virtual"><i class="ri-video-line me-1"></i>{{ __('Virtual') }}</span>
                                        @elseif($mode == MEETING_MODE_HYBRID)
                                            <span class="badge-mode-hybrid"><i class="ri-git-merge-line me-1"></i>{{ __('Hybrid') }}</span>
                                        @else
                                            <span class="badge-mode-inperson"><i class="ri-building-line me-1"></i>{{ __('In-Person') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($meeting->scheduled_at)
                                            <div class="fw-medium lh-sm">{{ $meeting->scheduled_at->format('d M Y') }}</div>
                                            <small class="text-muted"><i class="ri-time-line me-1"></i>{{ $meeting->scheduled_at->format('H:i') }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-xl-table-cell">
                                        @if($meeting->chairman)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-chip">{{ strtoupper(substr($meeting->chairman, 0, 1)) }}</div>
                                                <span class="small">{{ $meeting->chairman }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        @if($meeting->virtual_link)
                                            <a href="{{ $meeting->virtual_link }}" target="_blank"
                                               class="btn btn-xs-custom btn-outline-success">
                                                <i class="ri-video-line me-1"></i>{{ __('Join') }}
                                            </a>
                                        @elseif($meeting->venue)
                                            <span class="text-muted small" title="{{ $meeting->venue }}">
                                                <i class="ri-map-pin-line me-1"></i>{{ Str::limit($meeting->venue, 22) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="meeting-status-badge {{ $sm['class'] }}">{{ __($sm['label']) }}</span>
                                    </td>
                                    <td class="d-none d-md-table-cell text-center">
                                        <span class="attendee-chip">{{ $meeting->attendees_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="action-group">
                                            <a href="{{ route('admin.governance.meetings.show', $meeting) }}"
                                               class="action-btn action-btn-primary" title="{{ __('View details') }}"
                                               data-bs-toggle="tooltip">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                            <button type="button"
                                                    class="action-btn action-btn-secondary edit-meeting-btn"
                                                    title="{{ __('Edit meeting') }}"
                                                    data-bs-toggle="tooltip"
                                                    data-meeting="{{ json_encode([
                                                        'id'           => $meeting->id,
                                                        'title'        => $meeting->title,
                                                        'type'         => $meeting->type,
                                                        'mode'         => $mode,
                                                        'date'         => $meeting->scheduled_at ? $meeting->scheduled_at->format('Y-m-d') : '',
                                                        'time'         => $meeting->scheduled_at ? $meeting->scheduled_at->format('H:i') : '09:00',
                                                        'chairman'     => $meeting->chairman ?? '',
                                                        'venue'        => $meeting->venue ?? '',
                                                        'virtual_link' => $meeting->virtual_link ?? '',
                                                        'agenda'       => $meeting->agenda ?? '',
                                                        'objectives'   => $meeting->meeting_objectives ?? '',
                                                        'notes'        => $meeting->notes ?? '',
                                                    ]) }}">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            <button type="button"
                                                    class="action-btn action-btn-warning reschedule-btn"
                                                    title="{{ __('Reschedule') }}"
                                                    data-bs-toggle="tooltip"
                                                    data-id="{{ $meeting->id }}"
                                                    data-title="{{ e($meeting->title) }}"
                                                    data-date="{{ $meeting->scheduled_at ? $meeting->scheduled_at->format('Y-m-d') : '' }}"
                                                    data-time="{{ $meeting->scheduled_at ? $meeting->scheduled_at->format('H:i') : '09:00' }}">
                                                <i class="ri-calendar-check-line"></i>
                                            </button>
                                            <button type="button"
                                                    class="action-btn action-btn-danger delete-single-btn"
                                                    title="{{ __('Delete meeting') }}"
                                                    data-bs-toggle="tooltip"
                                                    data-id="{{ $meeting->id }}"
                                                    data-url="{{ route('admin.governance.meetings.destroy', $meeting) }}">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr id="emptyRow">
                                    <td colspan="10" class="py-5">
                                        <div class="text-center">
                                            <div class="empty-state-icon mb-3">
                                                <i class="ri-calendar-event-line"></i>
                                            </div>
                                            <h6 class="text-muted mb-1">{{ __('No meetings found') }}</h6>
                                            <p class="text-muted small mb-3">{{ __('Schedule your first governance meeting to get started.') }}</p>
                                            <button type="button" class="btn btn-primary btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#addModal">
                                                <i class="ri-calendar-add-line me-1"></i>{{ __('Schedule Meeting') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                                {{-- No results row (shown by JS filter) --}}
                                <tr id="noFilterResults" style="display:none">
                                    <td colspan="10" class="text-center text-muted py-4 small">
                                        <i class="ri-search-line me-1"></i>{{ __('No meetings match your filters.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- CARD VIEW (mobile default, toggleable on desktop) --}}
                <div id="cardView" class="d-none p-3">
                    <div class="row g-3" id="meetingCardsGrid">
                        @forelse($meetings as $meeting)
                        @php
                            $mode = $meeting->mode ?? MEETING_MODE_IN_PERSON;
                            $modeLabel = $mode == MEETING_MODE_VIRTUAL ? 'Virtual' : ($mode == MEETING_MODE_HYBRID ? 'Hybrid' : 'In-Person');
                            $modeIcon  = $mode == MEETING_MODE_VIRTUAL ? 'ri-video-line' : ($mode == MEETING_MODE_HYBRID ? 'ri-git-merge-line' : 'ri-building-line');
                            $typeColors= [MEETING_TYPE_AGM=>'#2563eb',MEETING_TYPE_BOARD=>'#0891b2',MEETING_TYPE_EGM=>'#d97706',MEETING_TYPE_OTHER=>'#6b7280'];
                            $typeColor = $typeColors[$meeting->type] ?? '#6b7280';
                        @endphp
                        <div class="col-12 col-sm-6 col-xl-4 meeting-card-col"
                             data-title="{{ strtolower($meeting->title) }}"
                             data-type="{{ $tm['label'] ?? '' }}"
                             data-status="{{ $sm['label'] ?? '' }}">
                            <div class="meeting-card h-100" style="--type-color:{{ $typeColor }}">
                                <div class="meeting-card-accent"></div>
                                <div class="meeting-card-body">
                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                        <div class="flex-grow-1 min-w-0">
                                            <a href="{{ route('admin.governance.meetings.show', $meeting) }}"
                                               class="fw-semibold text-dark text-decoration-none d-block text-truncate">
                                                {{ $meeting->title }}
                                            </a>
                                            <small class="text-muted">{{ $meeting->reference_number }}</small>
                                        </div>
                                        <div class="d-flex gap-1">
                                            @if($meeting->status == MEETING_STATUS_SCHEDULED)
                                                <span class="meeting-status-badge badge-status-scheduled">{{ __('Scheduled') }}</span>
                                            @elseif($meeting->status == MEETING_STATUS_COMPLETED)
                                                <span class="meeting-status-badge badge-status-completed">{{ __('Completed') }}</span>
                                            @else
                                                <span class="meeting-status-badge badge-status-cancelled">{{ __('Cancelled') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="meeting-card-meta">
                                        @if($meeting->scheduled_at)
                                        <div class="meta-item">
                                            <i class="ri-calendar-line"></i>
                                            <span>{{ $meeting->scheduled_at->format('d M Y') }}</span>
                                            <span class="text-muted">{{ $meeting->scheduled_at->format('H:i') }}</span>
                                        </div>
                                        @endif
                                        <div class="meta-item">
                                            <i class="{{ $modeIcon }}"></i>
                                            <span>{{ __($modeLabel) }}</span>
                                        </div>
                                        @if($meeting->chairman)
                                        <div class="meta-item">
                                            <i class="ri-user-star-line"></i>
                                            <span>{{ $meeting->chairman }}</span>
                                        </div>
                                        @endif
                                        @if($meeting->venue || $meeting->virtual_link)
                                        <div class="meta-item">
                                            <i class="{{ $meeting->virtual_link ? 'ri-links-line' : 'ri-map-pin-line' }}"></i>
                                            @if($meeting->virtual_link)
                                                <a href="{{ $meeting->virtual_link }}" target="_blank" class="text-success small">{{ __('Join Meeting') }}</a>
                                            @else
                                                <span class="text-truncate" title="{{ $meeting->venue }}">{{ Str::limit($meeting->venue, 30) }}</span>
                                            @endif
                                        </div>
                                        @endif
                                        <div class="meta-item">
                                            <i class="ri-group-line"></i>
                                            <span>{{ $meeting->attendees_count ?? 0 }} {{ __('attendees') }}</span>
                                        </div>
                                    </div>
                                    <div class="meeting-card-footer">
                                        <a href="{{ route('admin.governance.meetings.show', $meeting) }}"
                                           class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="ri-eye-line me-1"></i>{{ __('View') }}
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary edit-meeting-btn"
                                                data-meeting="{{ json_encode([
                                                    'id'           => $meeting->id,
                                                    'title'        => $meeting->title,
                                                    'type'         => $meeting->type,
                                                    'mode'         => $mode,
                                                    'date'         => $meeting->scheduled_at ? $meeting->scheduled_at->format('Y-m-d') : '',
                                                    'time'         => $meeting->scheduled_at ? $meeting->scheduled_at->format('H:i') : '09:00',
                                                    'chairman'     => $meeting->chairman ?? '',
                                                    'venue'        => $meeting->venue ?? '',
                                                    'virtual_link' => $meeting->virtual_link ?? '',
                                                    'agenda'       => $meeting->agenda ?? '',
                                                    'objectives'   => $meeting->meeting_objectives ?? '',
                                                    'notes'        => $meeting->notes ?? '',
                                                ]) }}">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-warning reschedule-btn"
                                                data-id="{{ $meeting->id }}"
                                                data-title="{{ e($meeting->title) }}"
                                                data-date="{{ $meeting->scheduled_at ? $meeting->scheduled_at->format('Y-m-d') : '' }}"
                                                data-time="{{ $meeting->scheduled_at ? $meeting->scheduled_at->format('H:i') : '09:00' }}">
                                            <i class="ri-calendar-check-line"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger delete-single-btn"
                                                data-id="{{ $meeting->id }}"
                                                data-url="{{ route('admin.governance.meetings.destroy', $meeting) }}">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <div class="empty-state-icon mb-3"><i class="ri-calendar-event-line"></i></div>
                                <h6 class="text-muted">{{ __('No meetings yet') }}</h6>
                                <button type="button" class="btn btn-primary btn-sm mt-2"
                                        data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="ri-calendar-add-line me-1"></i>{{ __('Schedule Meeting') }}
                                </button>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Pagination --}}
                @if($meetings->hasPages())
                <div class="card-footer bg-white border-top py-2">
                    {{ $meetings->links() }}
                </div>
                @endif

            </div>

        </div>
    </div>
</div>

{{-- ====================== SCHEDULE MEETING MODAL ====================== --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="addModalLabel">
                        <i class="ri-calendar-add-line me-2 text-primary"></i>{{ __('Schedule a Meeting') }}
                    </h5>
                    <p class="text-muted small mb-0">{{ __('Fill in the details below to schedule a new governance meeting.') }}</p>
                </div>
                <button type="button" class="btn-close ms-3" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.governance.meetings.store') }}" method="POST"
                  class="ajax" data-handler="meetingStoreHandler" id="addMeetingForm">
                @csrf
                <div class="modal-body pt-3">

                    {{-- SECTION: Basic Info --}}
                    <div class="form-section">
                        <div class="form-section-header">
                            <i class="ri-information-line"></i> {{ __('Basic Information') }}
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('Meeting Title') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" required
                                       placeholder="{{ __('e.g. Q2 2026 Board Meeting') }}" autocomplete="off">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">{{ __('Meeting Type') }} <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="">{{ __('Select Type') }}</option>
                                    <option value="{{ MEETING_TYPE_AGM }}">{{ __('AGM — Annual General Meeting') }}</option>
                                    <option value="{{ MEETING_TYPE_BOARD }}">{{ __('Board Meeting') }}</option>
                                    <option value="{{ MEETING_TYPE_EGM }}">{{ __('EGM — Extraordinary General Meeting') }}</option>
                                    <option value="{{ MEETING_TYPE_OTHER }}">{{ __('Other') }}</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">{{ __('Chairman / Chairperson') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-user-star-line text-muted"></i></span>
                                    <input type="text" name="chairman" class="form-control"
                                           placeholder="{{ __('Full name') }}" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: Date, Time & Mode --}}
                    <div class="form-section">
                        <div class="form-section-header">
                            <i class="ri-calendar-line"></i> {{ __('Date, Time & Format') }}
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">{{ __('Meeting Date') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-calendar-line text-muted"></i></span>
                                    <input type="date" name="meeting_date" class="form-control" required id="addMeetingDate">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">{{ __('Meeting Time') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-time-line text-muted"></i></span>
                                    <input type="time" name="meeting_time" class="form-control" required
                                           id="addMeetingTime" value="09:00">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Meeting Mode') }} <span class="text-danger">*</span></label>
                                <div class="mode-selector">
                                    <label class="mode-option active" data-mode="{{ MEETING_MODE_IN_PERSON }}">
                                        <input type="radio" name="mode" value="{{ MEETING_MODE_IN_PERSON }}" checked>
                                        <div class="mode-option-inner">
                                            <i class="ri-building-line"></i>
                                            <span>{{ __('In-Person') }}</span>
                                        </div>
                                    </label>
                                    <label class="mode-option" data-mode="{{ MEETING_MODE_VIRTUAL }}">
                                        <input type="radio" name="mode" value="{{ MEETING_MODE_VIRTUAL }}">
                                        <div class="mode-option-inner">
                                            <i class="ri-video-line"></i>
                                            <span>{{ __('Virtual') }}</span>
                                        </div>
                                    </label>
                                    <label class="mode-option" data-mode="{{ MEETING_MODE_HYBRID }}">
                                        <input type="radio" name="mode" value="{{ MEETING_MODE_HYBRID }}">
                                        <div class="mode-option-inner">
                                            <i class="ri-git-merge-line"></i>
                                            <span>{{ __('Hybrid') }}</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Venue (in-person / hybrid) --}}
                            <div class="col-12" id="venueSection">
                                <label class="form-label">{{ __('Venue / Location') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-map-pin-line text-muted"></i></span>
                                    <input type="text" name="venue" class="form-control"
                                           placeholder="{{ __('e.g. Boardroom 3, Head Office') }}">
                                </div>
                            </div>

                            {{-- Virtual link --}}
                            <div class="col-12 d-none" id="virtualSection">
                                @if($googleMeetReady)
                                <div class="google-meet-banner mb-2">
                                    <svg width="18" height="18" viewBox="0 0 48 48" class="flex-shrink-0">
                                        <path fill="#4285F4" d="M29 20.5V17l8-8v22l-8-8v-2.5z"/>
                                        <rect width="22" height="22" x="5" y="13" rx="3" fill="#00832d"/>
                                        <path fill="#fff" d="M19 18h4v4h-4zm0 8h4v4h-4zm6-8h4v4h-4zm0 8h4v4h-4z"/>
                                    </svg>
                                    <div>
                                        <div class="fw-medium small">{{ __('Google Meet available') }}</div>
                                        <div class="form-check mb-0 mt-1">
                                            <input class="form-check-input" type="checkbox" name="create_google_meet"
                                                   id="createGoogleMeet" value="1" checked>
                                            <label class="form-check-label small" for="createGoogleMeet">
                                                {{ __('Auto-create a Google Meet link') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <label class="form-label">{{ __('Virtual Meeting Link') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-links-line text-muted"></i></span>
                                    <input type="url" name="virtual_link" class="form-control"
                                           placeholder="https://meet.google.com/...">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: Agenda & Objectives --}}
                    <div class="form-section">
                        <div class="form-section-header">
                            <i class="ri-list-check-2"></i> {{ __('Agenda & Objectives') }}
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('Agenda') }}</label>
                                <textarea name="agenda" class="form-control" rows="4"
                                    placeholder="{{ __("1. Call to order\n2. Approval of previous minutes\n3. Financial report\n4. Any other business") }}"></textarea>
                                <div class="form-text">{{ __('Ordered list of topics to be discussed.') }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Meeting Objectives') }}</label>
                                <textarea name="meeting_objectives" class="form-control" rows="2"
                                    placeholder="{{ __('What should be achieved? e.g. Approve Q1 budget, elect board member…') }}"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">{{ __('Internal Notes') }}</label>
                                <textarea name="notes" class="form-control" rows="2"
                                    placeholder="{{ __('Notes visible to admin only') }}"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION: Notifications & Recurring --}}
                    <div class="form-section mb-0">
                        <div class="form-section-header">
                            <i class="ri-settings-line"></i> {{ __('Settings') }}
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check form-check-inline-card">
                                    <input class="form-check-input" type="checkbox" name="invite_all_shareholders"
                                           id="inviteAllShareholders" value="1" checked>
                                    <label class="form-check-label" for="inviteAllShareholders">
                                        <i class="ri-group-line text-primary me-2"></i>
                                        <div>
                                            <div class="fw-medium small">{{ __('Invite all active shareholders') }}</div>
                                            <div class="text-muted" style="font-size:.75rem">{{ __('Send email & system notifications to all shareholders') }}</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_recurring"
                                           id="isRecurring" value="1">
                                    <label class="form-check-label fw-medium" for="isRecurring">
                                        <i class="ri-repeat-line me-1"></i>{{ __('Recurring meeting') }}
                                    </label>
                                </div>
                                <div class="d-none" id="recurringSection">
                                    <div class="recurring-panel">
                                        <p class="text-muted small mb-3">
                                            <i class="ri-information-line me-1"></i>
                                            {{ __('Individual instances will be created for each occurrence.') }}
                                        </p>
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <label class="form-label small">{{ __('Repeat') }}</label>
                                                <select name="recurrence_type" id="recurrenceType" class="form-select form-select-sm">
                                                    <option value="weekly">{{ __('Weekly — same day every week') }}</option>
                                                    <option value="monthly">{{ __('Monthly — same date every month') }}</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6" id="weekdaySection">
                                                <label class="form-label small">{{ __('Day of Week') }}</label>
                                                <select name="recurrence_day_of_week" class="form-select form-select-sm">
                                                    @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $i => $day)
                                                    <option value="{{ $i }}">{{ __($day) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-6 d-none" id="monthdaySection">
                                                <label class="form-label small">{{ __('Day of Month') }}</label>
                                                <input type="number" name="recurrence_day_of_month"
                                                       class="form-control form-control-sm" min="1" max="31"
                                                       placeholder="{{ __('e.g. 15') }}">
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label small">{{ __('Repeat Until') }}</label>
                                                <input type="date" name="recurrence_end_date"
                                                       class="form-control form-control-sm">
                                            </div>
                                            <div class="col-12" id="recurringPreviewWrap" style="display:none">
                                                <div id="recurringPreview" class="recurring-preview"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-calendar-check-line me-1"></i>{{ __('Schedule Meeting') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ====================== EDIT MEETING MODAL ====================== --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="editModalLabel">
                        <i class="ri-edit-line me-2 text-secondary"></i>{{ __('Edit Meeting') }}
                    </h5>
                    <p class="text-muted small mb-0" id="editMeetingSubtitle">{{ __('Update the meeting details below.') }}</p>
                </div>
                <button type="button" class="btn-close ms-3" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMeetingForm" method="POST" class="ajax" data-handler="meetingEditHandler">
                @csrf
                <div class="modal-body pt-3">

                    <div class="form-section">
                        <div class="form-section-header"><i class="ri-information-line"></i> {{ __('Basic Information') }}</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('Meeting Title') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="editTitle" class="form-control" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">{{ __('Meeting Type') }} <span class="text-danger">*</span></label>
                                <select name="type" id="editType" class="form-select" required>
                                    <option value="{{ MEETING_TYPE_AGM }}">{{ __('AGM') }}</option>
                                    <option value="{{ MEETING_TYPE_BOARD }}">{{ __('Board Meeting') }}</option>
                                    <option value="{{ MEETING_TYPE_EGM }}">{{ __('EGM') }}</option>
                                    <option value="{{ MEETING_TYPE_OTHER }}">{{ __('Other') }}</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">{{ __('Chairman / Chairperson') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-user-star-line text-muted"></i></span>
                                    <input type="text" name="chairman" id="editChairman" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-header"><i class="ri-calendar-line"></i> {{ __('Date, Time & Format') }}</div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">{{ __('Date') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-calendar-line text-muted"></i></span>
                                    <input type="date" name="meeting_date" id="editDate" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">{{ __('Time') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-time-line text-muted"></i></span>
                                    <input type="time" name="meeting_time" id="editTime" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Meeting Mode') }}</label>
                                <select name="mode" id="editMode" class="form-select">
                                    <option value="{{ MEETING_MODE_IN_PERSON }}">{{ __('In-Person') }}</option>
                                    <option value="{{ MEETING_MODE_VIRTUAL }}">{{ __('Virtual') }}</option>
                                    <option value="{{ MEETING_MODE_HYBRID }}">{{ __('Hybrid') }}</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">{{ __('Venue / Location') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-map-pin-line text-muted"></i></span>
                                    <input type="text" name="venue" id="editVenue" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">{{ __('Virtual Link') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-links-line text-muted"></i></span>
                                    <input type="url" name="virtual_link" id="editVirtualLink" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section mb-0">
                        <div class="form-section-header"><i class="ri-list-check-2"></i> {{ __('Agenda & Objectives') }}</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('Agenda') }}</label>
                                <textarea name="agenda" id="editAgenda" class="form-control" rows="4"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Meeting Objectives') }}</label>
                                <textarea name="meeting_objectives" id="editObjectives" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">{{ __('Internal Notes') }}</label>
                                <textarea name="notes" id="editNotes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>{{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ====================== RESCHEDULE MODAL ====================== --}}
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold">
                        <i class="ri-calendar-check-line me-2 text-warning"></i>{{ __('Reschedule Meeting') }}
                    </h5>
                    <p class="text-muted small mb-0" id="rescheduleMeetingTitle"></p>
                </div>
                <button type="button" class="btn-close ms-3" data-bs-dismiss="modal"></button>
            </div>
            <form id="rescheduleForm" method="POST" class="ajax" data-handler="rescheduleHandler">
                @csrf
                <div class="modal-body pt-3">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">{{ __('New Date') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-calendar-line text-muted"></i></span>
                                <input type="date" name="meeting_date" id="rescheduleDate" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{ __('New Time') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ri-time-line text-muted"></i></span>
                                <input type="time" name="meeting_time" id="rescheduleTime" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-check-inline-card">
                                <input class="form-check-input" type="checkbox" name="notify_shareholders"
                                       id="rescheduleNotify" value="1" checked>
                                <label class="form-check-label" for="rescheduleNotify">
                                    <i class="ri-notification-line text-primary me-2"></i>
                                    <div>
                                        <div class="fw-medium small">{{ __('Notify shareholders') }}</div>
                                        <div class="text-muted" style="font-size:.75rem">{{ __('Send rescheduling notification via email & system') }}</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ri-calendar-check-line me-1"></i>{{ __('Reschedule') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('style')
<style>
/* ─── Badge System ─────────────────────────────────────────────────────────── */
.meeting-type-badge {
    display: inline-flex; align-items: center;
    padding: 2px 8px; border-radius: 4px;
    font-size: .72rem; font-weight: 600; letter-spacing: .02em;
}
.badge-type-agm   { background: #dbeafe; color: #1d4ed8; }
.badge-type-board { background: #cffafe; color: #0e7490; }
.badge-type-egm   { background: #fef3c7; color: #92400e; }
.badge-type-other { background: #f3f4f6; color: #374151; }

.meeting-status-badge {
    display: inline-flex; align-items: center;
    padding: 2px 8px; border-radius: 20px;
    font-size: .72rem; font-weight: 600;
}
.badge-status-scheduled { background: #dbeafe; color: #1e40af; }
.badge-status-completed  { background: #dcfce7; color: #166534; }
.badge-status-cancelled  { background: #fee2e2; color: #991b1b; }

.badge-recurring { background: #e0f2fe; color: #0284c7; padding: 1px 5px; border-radius: 4px; font-size: .68rem; }
.badge-instance  { background: #f3f4f6; color: #6b7280; padding: 1px 5px; border-radius: 4px; font-size: .68rem; }

/* ─── Mode Badges ──────────────────────────────────────────────────────────── */
.badge-mode-virtual   { display:inline-flex; align-items:center; padding:2px 8px; border-radius:20px; font-size:.72rem; font-weight:600; background:#dcfce7; color:#166534; }
.badge-mode-hybrid    { display:inline-flex; align-items:center; padding:2px 8px; border-radius:20px; font-size:.72rem; font-weight:600; background:#f0fdfa; color:#0f766e; }
.badge-mode-inperson  { display:inline-flex; align-items:center; padding:2px 8px; border-radius:20px; font-size:.72rem; font-weight:600; background:#f3f4f6; color:#374151; }

/* ─── Action Buttons ───────────────────────────────────────────────────────── */
.action-group { display: flex; gap: 4px; justify-content: flex-end; flex-wrap: nowrap; }
.action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 8px;
    border: 1px solid transparent; background: transparent;
    font-size: .9rem; cursor: pointer; transition: all .15s;
    text-decoration: none;
}
.action-btn-primary   { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }
.action-btn-secondary { color: #4b5563; border-color: #e5e7eb; background: #f9fafb; }
.action-btn-warning   { color: #d97706; border-color: #fde68a; background: #fffbeb; }
.action-btn-danger    { color: #dc2626; border-color: #fecaca; background: #fef2f2; }
.action-btn:hover { filter: brightness(.92); transform: translateY(-1px); }
.action-btn:active { transform: translateY(0); }

/* ─── Misc Table Elements ──────────────────────────────────────────────────── */
.avatar-chip {
    width: 26px; height: 26px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff; font-size: .7rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.attendee-chip {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 28px; height: 28px; border-radius: 14px;
    background: #f3f4f6; color: #374151;
    font-size: .78rem; font-weight: 600; padding: 0 8px;
}
.btn-xs-custom {
    padding: 2px 10px; font-size: .75rem; border-radius: 6px;
}

/* ─── Empty State ──────────────────────────────────────────────────────────── */
.empty-state-icon {
    width: 64px; height: 64px; border-radius: 16px;
    background: linear-gradient(135deg, #e0f2fe, #dbeafe);
    display: flex; align-items: center; justify-content: center; margin: 0 auto;
}
.empty-state-icon i { font-size: 28px; color: #2563eb; }

/* ─── Mode Selector (Add Modal) ────────────────────────────────────────────── */
.mode-selector { display: flex; gap: 12px; flex-wrap: wrap; }
.mode-option {
    flex: 1; min-width: 100px; cursor: pointer;
    border: 2px solid #e5e7eb; border-radius: 12px;
    padding: 12px 8px; text-align: center; transition: all .2s;
    user-select: none;
}
.mode-option input[type=radio] { position: absolute; opacity: 0; pointer-events: none; }
.mode-option-inner { display: flex; flex-direction: column; align-items: center; gap: 6px; }
.mode-option-inner i { font-size: 1.5rem; color: #9ca3af; transition: color .2s; }
.mode-option-inner span { font-size: .8rem; font-weight: 500; color: #6b7280; }
.mode-option.active,
.mode-option:has(input:checked) {
    border-color: #2563eb; background: #eff6ff;
}
.mode-option.active .mode-option-inner i,
.mode-option:has(input:checked) .mode-option-inner i { color: #2563eb; }
.mode-option.active .mode-option-inner span,
.mode-option:has(input:checked) .mode-option-inner span { color: #1d4ed8; font-weight: 600; }
.mode-option:hover:not(.active) { border-color: #93c5fd; background: #f8faff; }

/* ─── Form Sections ────────────────────────────────────────────────────────── */
.form-section { margin-bottom: 1.5rem; }
.form-section-header {
    display: flex; align-items: center; gap: 6px;
    font-size: .8rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .06em; color: #6b7280;
    padding-bottom: 8px; margin-bottom: 12px;
    border-bottom: 1px solid #f3f4f6;
}
.form-section-header i { font-size: 1rem; }

/* ─── Inline-card checkbox ─────────────────────────────────────────────────── */
.form-check-inline-card {
    display: flex; align-items: flex-start; gap: 10px;
    border: 1px solid #e5e7eb; border-radius: 10px;
    padding: 10px 12px; cursor: pointer;
    transition: border-color .15s, background .15s;
}
.form-check-inline-card:has(input:checked) { border-color: #93c5fd; background: #f0f9ff; }
.form-check-inline-card .form-check-input { margin-top: 3px; flex-shrink: 0; }
.form-check-inline-card .form-check-label {
    cursor: pointer; display: flex; align-items: flex-start; gap: 0;
}

/* ─── Recurring Panel ──────────────────────────────────────────────────────── */
.recurring-panel {
    background: #f9fafb; border: 1px dashed #d1d5db;
    border-radius: 10px; padding: 16px; margin-top: 8px;
}
.recurring-preview {
    background: #eff6ff; border: 1px solid #bfdbfe;
    border-radius: 8px; padding: 8px 12px;
    font-size: .82rem; color: #1e40af;
}

/* ─── Google Meet Banner ───────────────────────────────────────────────────── */
.google-meet-banner {
    display: flex; align-items: flex-start; gap: 10px;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: 8px; padding: 10px 12px;
}

/* ─── Meeting Card (card view) ─────────────────────────────────────────────── */
.meeting-card {
    border-radius: 14px; border: 1px solid #e5e7eb;
    background: #fff; overflow: hidden;
    transition: box-shadow .2s, transform .15s;
}
.meeting-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); transform: translateY(-2px); }
.meeting-card-accent { height: 4px; background: var(--type-color, #2563eb); }
.meeting-card-body { padding: 16px; }
.meeting-card-meta { display: flex; flex-direction: column; gap: 6px; margin: 12px 0; }
.meta-item { display: flex; align-items: center; gap: 8px; font-size: .82rem; color: #374151; }
.meta-item i { color: #9ca3af; width: 16px; flex-shrink: 0; }
.meeting-card-footer { display: flex; gap: 6px; padding-top: 12px; border-top: 1px solid #f3f4f6; }

/* ─── Stat Cards (CSS Grid — avoids Bootstrap sub-pixel rounding bug) ──────── */
.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}
@media (min-width: 768px) {
    .stats-grid { grid-template-columns: repeat(4, 1fr); gap: 14px; }
}
.stat-card {
    display: flex; align-items: center; gap: 12px;
    background: #fff; border-radius: 12px;
    padding: 14px 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,.07); border: 1px solid #f0f0f0;
    transition: box-shadow .2s, transform .15s; overflow: hidden;
}
.stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.1); transform: translateY(-1px); }
.stat-icon {
    width: 44px; height: 44px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.stat-icon i { font-size: 1.25rem; }
.stat-body { min-width: 0; flex: 1; }
.stat-count { font-size: 1.5rem; font-weight: 700; line-height: 1.1; }
.stat-label { font-size: .75rem; color: #6b7280; margin-top: 2px; }
@media (max-width: 360px) {
    .stat-card { gap: 8px; padding: 10px 12px; }
    .stat-icon { width: 36px; height: 36px; }
    .stat-count { font-size: 1.2rem; }
}

/* ─── Generic card hover ───────────────────────────────────────────────────── */
.card { transition: box-shadow .2s; }
.card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.06); }

/* ─── Table adjustments ────────────────────────────────────────────────────── */
.table > :not(caption) > * > * { padding: .75rem .6rem; }
.table thead th { font-size: .72rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #6b7280; }
.table tbody tr:hover { background: #f8faff; }

/* ─── Bulk bar ─────────────────────────────────────────────────────────────── */
#bulkBar { border-radius: 10px; transition: all .2s; }

/* ─── Scrollable modal max-height fix ─────────────────────────────────────── */
.modal-dialog-scrollable .modal-body { max-height: calc(100vh - 220px); overflow-y: auto; }

/* ─── Responsive tweaks ────────────────────────────────────────────────────── */
@media (max-width: 575.98px) {
    .mode-selector { gap: 8px; }
    .mode-option { min-width: 80px; padding: 10px 6px; }
    .mode-option-inner i { font-size: 1.25rem; }
    .action-group { flex-wrap: wrap; }
    .action-btn { width: 30px; height: 30px; font-size: .82rem; }
    .form-section-header { font-size: .73rem; }
    .meeting-card-footer { flex-wrap: wrap; }
    .meeting-card-footer .btn { flex: 1; }
}
@media (max-width: 767.98px) {
    /* On mobile, default to card view */
    #tableView { display: none !important; }
    #cardView  { display: block !important; }
    #viewTable, #viewCards { display: none; } /* hide toggle on mobile */
}
</style>
@endpush

@push('script')
<script>
(function () {
    'use strict';
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ── Bootstrap tooltips ──────────────────────────────────────────────────
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el, { trigger: 'hover', placement: 'top' });
    });

    // ── View toggle (table ↔ card) ──────────────────────────────────────────
    var viewTableBtn = document.getElementById('viewTable');
    var viewCardsBtn = document.getElementById('viewCards');
    var tableView    = document.getElementById('tableView');
    var cardView     = document.getElementById('cardView');

    if (viewTableBtn) {
        viewTableBtn.addEventListener('click', function() {
            tableView.classList.remove('d-none');
            cardView.classList.add('d-none');
            this.classList.add('active');
            viewCardsBtn.classList.remove('active');
            localStorage.setItem('meetingView', 'table');
        });
    }
    if (viewCardsBtn) {
        viewCardsBtn.addEventListener('click', function() {
            cardView.classList.remove('d-none');
            tableView.classList.add('d-none');
            this.classList.add('active');
            viewTableBtn.classList.remove('active');
            localStorage.setItem('meetingView', 'cards');
        });
    }
    // Restore view preference
    (function() {
        var pref = localStorage.getItem('meetingView');
        if (pref === 'cards' && viewCardsBtn) viewCardsBtn.click();
    })();

    // ── Live search + filter ────────────────────────────────────────────────
    var searchInput  = document.getElementById('meetingSearch');
    var filterType   = document.getElementById('filterType');
    var filterStatus = document.getElementById('filterStatus');

    function applyFilters() {
        var q      = (searchInput ? searchInput.value.toLowerCase().trim() : '');
        var type   = (filterType   ? filterType.value   : '');
        var status = (filterStatus ? filterStatus.value : '');
        var rows   = document.querySelectorAll('#meetingsTbody .meeting-row');
        var cards  = document.querySelectorAll('#meetingCardsGrid .meeting-card-col');
        var visibleRows = 0;

        rows.forEach(function(row) {
            var titleMatch  = !q      || row.getAttribute('data-title').indexOf(q) !== -1;
            var typeMatch   = !type   || row.getAttribute('data-type')   === type;
            var statusMatch = !status || row.getAttribute('data-status') === status;
            var show = titleMatch && typeMatch && statusMatch;
            row.style.display = show ? '' : 'none';
            if (show) visibleRows++;
        });

        cards.forEach(function(col) {
            var titleMatch  = !q      || col.getAttribute('data-title').indexOf(q) !== -1;
            var typeMatch   = !type   || col.getAttribute('data-type')   === type;
            var statusMatch = !status || col.getAttribute('data-status') === status;
            col.style.display = (titleMatch && typeMatch && statusMatch) ? '' : 'none';
        });

        var noResults = document.getElementById('noFilterResults');
        var emptyRow  = document.getElementById('emptyRow');
        if (noResults) {
            noResults.style.display = (visibleRows === 0 && rows.length > 0) ? '' : 'none';
        }
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (filterType)  filterType.addEventListener('change', applyFilters);
    if (filterStatus) filterStatus.addEventListener('change', applyFilters);

    // ── Mode selector (add modal — radio + visual) ──────────────────────────
    document.querySelectorAll('#addMeetingForm .mode-option').forEach(function(opt) {
        opt.addEventListener('click', function() {
            document.querySelectorAll('#addMeetingForm .mode-option').forEach(function(o){ o.classList.remove('active'); });
            this.classList.add('active');
            this.querySelector('input[type=radio]').checked = true;
            var mode = parseInt(this.getAttribute('data-mode'));
            var VIRTUAL   = {{ MEETING_MODE_VIRTUAL }};
            var IN_PERSON = {{ MEETING_MODE_IN_PERSON }};
            document.getElementById('venueSection').classList.toggle('d-none',   mode === VIRTUAL);
            document.getElementById('virtualSection').classList.toggle('d-none', mode === IN_PERSON);
        });
    });

    // ── Recurring section ───────────────────────────────────────────────────
    var isRecChk = document.getElementById('isRecurring');
    var recSec   = document.getElementById('recurringSection');
    var recType  = document.getElementById('recurrenceType');

    if (isRecChk) {
        isRecChk.addEventListener('change', function() {
            recSec.classList.toggle('d-none', !this.checked);
        });
    }
    if (recType) {
        recType.addEventListener('change', function() {
            document.getElementById('weekdaySection').classList.toggle('d-none', this.value === 'monthly');
            document.getElementById('monthdaySection').classList.toggle('d-none', this.value !== 'monthly');
            updateRecurringPreview();
        });
    }
    ['[name="recurrence_day_of_week"]','[name="recurrence_day_of_month"]','[name="recurrence_end_date"]'].forEach(function(sel) {
        var el = document.querySelector(sel);
        if (el) el.addEventListener('change', updateRecurringPreview);
    });
    var addDateEl = document.getElementById('addMeetingDate');
    if (addDateEl) addDateEl.addEventListener('change', updateRecurringPreview);

    function updateRecurringPreview() {
        var preview = document.getElementById('recurringPreview');
        var wrap    = document.getElementById('recurringPreviewWrap');
        if (!preview) return;
        var endDateVal = document.querySelector('[name="recurrence_end_date"]')?.value;
        if (!endDateVal) { wrap.style.display = 'none'; return; }
        var type    = recType ? recType.value : 'weekly';
        var endDate = new Date(endDateVal);
        var startVal= document.getElementById('addMeetingDate')?.value;
        var startDate = startVal ? new Date(startVal) : new Date();
        var count   = 0;
        var label   = '';
        if (type === 'weekly') {
            var dow = parseInt(document.querySelector('[name="recurrence_day_of_week"]')?.value || '0');
            var cur = new Date(startDate); cur.setDate(cur.getDate() + 1);
            while (cur.getDay() !== dow) cur.setDate(cur.getDate() + 1);
            while (cur <= endDate) { count++; cur.setDate(cur.getDate() + 7); }
            var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
            label = '<strong>' + count + ' occurrence' + (count !== 1 ? 's' : '') + '</strong> &mdash; every <strong>' + days[dow] + '</strong> until ' + endDateVal;
        } else {
            var dom = parseInt(document.querySelector('[name="recurrence_day_of_month"]')?.value || '1');
            var cur2 = new Date(startDate.getFullYear(), startDate.getMonth(), dom);
            if (cur2 <= startDate) cur2 = new Date(cur2.getFullYear(), cur2.getMonth() + 1, dom);
            while (cur2 <= endDate) { count++; cur2 = new Date(cur2.getFullYear(), cur2.getMonth() + 1, dom); }
            var suf = dom === 1 ? 'st' : dom === 2 ? 'nd' : dom === 3 ? 'rd' : 'th';
            label = '<strong>' + count + ' occurrence' + (count !== 1 ? 's' : '') + '</strong> &mdash; every <strong>' + dom + suf + '</strong> of the month until ' + endDateVal;
        }
        preview.innerHTML = '<i class="ri-repeat-line me-1"></i>' + label;
        wrap.style.display = count > 0 ? '' : 'none';
    }

    // ── Select All / Bulk actions ───────────────────────────────────────────
    var selectAllChk = document.getElementById('selectAll');
    if (selectAllChk) {
        selectAllChk.addEventListener('change', function() {
            document.querySelectorAll('.row-check').forEach(function(c) { c.checked = selectAllChk.checked; });
            updateBulkBar();
        });
    }
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('row-check')) updateBulkBar();
    });

    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.row-check:checked')).map(function(c) { return c.value; });
    }
    function updateBulkBar() {
        var ids  = getSelectedIds();
        var bar  = document.getElementById('bulkBar');
        var countEl = document.getElementById('selectedCount');
        if (ids.length > 0) {
            bar.classList.remove('d-none'); bar.classList.add('d-flex');
            countEl.textContent = ids.length + ' {{ __("selected") }}';
        } else {
            bar.classList.add('d-none'); bar.classList.remove('d-flex');
        }
    }

    document.querySelectorAll('.bulk-action-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var action = this.getAttribute('data-action');
            var ids    = getSelectedIds();
            if (!ids.length) return;
            var labels = { delete: '{{ __("delete") }}', cancel: '{{ __("cancel") }}', complete: '{{ __("mark as completed") }}' };
            if (!confirm('{{ __("Are you sure you want to") }} ' + labels[action] + ' ' + ids.length + ' {{ __("meeting(s)?") }}')) return;
            $.post('{{ route("admin.governance.meetings.bulk") }}', { _token: csrf, action: action, ids: ids }, function(res) {
                if (res.status) { toastr.success(res.message); setTimeout(function() { location.reload(); }, 700); }
                else toastr.error(res.message);
            });
        });
    });

    // ── Edit meeting ────────────────────────────────────────────────────────
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.edit-meeting-btn');
        if (!btn) return;
        var m = JSON.parse(btn.getAttribute('data-meeting'));
        document.getElementById('editTitle').value        = m.title;
        document.getElementById('editType').value         = m.type;
        document.getElementById('editChairman').value     = m.chairman;
        document.getElementById('editDate').value         = m.date;
        document.getElementById('editTime').value         = m.time;
        document.getElementById('editMode').value         = m.mode;
        document.getElementById('editVenue').value        = m.venue;
        document.getElementById('editVirtualLink').value  = m.virtual_link;
        document.getElementById('editAgenda').value       = m.agenda;
        document.getElementById('editObjectives').value   = m.objectives;
        document.getElementById('editNotes').value        = m.notes;
        document.getElementById('editMeetingSubtitle').textContent = m.title;
        document.getElementById('editMeetingForm').action = '{{ url("admin/governance/meetings") }}/' + m.id + '/update';
        new bootstrap.Modal(document.getElementById('editModal')).show();
    });

    window.meetingEditHandler = function(res) {
        if (res.status) {
            toastr.success(res.message);
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            setTimeout(function() { location.reload(); }, 700);
        } else {
            toastr.error(res.message);
        }
    };

    // ── Reschedule ──────────────────────────────────────────────────────────
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.reschedule-btn');
        if (!btn) return;
        document.getElementById('rescheduleMeetingTitle').textContent = btn.getAttribute('data-title');
        document.getElementById('rescheduleDate').value = btn.getAttribute('data-date');
        document.getElementById('rescheduleTime').value = btn.getAttribute('data-time');
        document.getElementById('rescheduleForm').action = '{{ url("admin/governance/meetings") }}/' + btn.getAttribute('data-id') + '/reschedule';
        new bootstrap.Modal(document.getElementById('rescheduleModal')).show();
    });

    window.rescheduleHandler = function(res) {
        if (res.status) {
            toastr.success(res.message);
            bootstrap.Modal.getInstance(document.getElementById('rescheduleModal')).hide();
            setTimeout(function() { location.reload(); }, 700);
        } else {
            toastr.error(res.message);
        }
    };

    // ── Delete single ───────────────────────────────────────────────────────
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.delete-single-btn');
        if (!btn) return;
        if (!confirm('{{ __("Delete this meeting? This cannot be undone.") }}')) return;
        $.post(btn.getAttribute('data-url'), { _token: csrf }, function(res) {
            if (res.status) {
                toastr.success(res.message);
                // Remove row from table and card from grid
                var row  = btn.closest('tr');
                var card = btn.closest('.meeting-card-col');
                if (row)  row.remove();
                if (card) card.remove();
            } else {
                toastr.error(res.message);
            }
        });
    });

    // ── meetingStoreHandler ─────────────────────────────────────────────────
    window.meetingStoreHandler = function(res) {
        if (res.status) {
            toastr.success(res.message);
            bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
            setTimeout(function() { location.reload(); }, 700);
        } else {
            toastr.error(res.message);
        }
    };

})();
</script>
@endpush
