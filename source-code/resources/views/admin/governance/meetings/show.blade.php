@extends('admin.layouts.app')

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Page Header --}}
            <div class="page-content-wrapper bg-white p-30 radius-20 mb-4">
                <div class="d-sm-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.governance.meetings.index') }}" class="btn btn-sm btn-light">
                            <i class="ri-arrow-left-line"></i>
                        </a>
                        <div>
                            <h3 class="mb-0">{{ $meeting->title }}</h3>
                            <small class="text-muted">{{ $meeting->reference_number }}</small>
                        </div>
                        {{-- Type badge --}}
                        @if($meeting->type == MEETING_TYPE_AGM)
                            <span class="badge bg-primary ms-1">AGM</span>
                        @elseif($meeting->type == MEETING_TYPE_BOARD)
                            <span class="badge bg-info ms-1">Board</span>
                        @elseif($meeting->type == MEETING_TYPE_EGM)
                            <span class="badge bg-warning text-dark ms-1">EGM</span>
                        @else
                            <span class="badge bg-secondary ms-1">Other</span>
                        @endif
                        {{-- Mode badge --}}
                        @php $mode = $meeting->mode ?? MEETING_MODE_IN_PERSON; @endphp
                        @if($mode == MEETING_MODE_VIRTUAL)
                            <span class="badge bg-success ms-1"><i class="ri-video-line me-1"></i>Virtual</span>
                        @elseif($mode == MEETING_MODE_HYBRID)
                            <span class="badge ms-1" style="background:#0d9488!important"><i class="ri-map-pin-2-line me-1"></i>Hybrid</span>
                        @else
                            <span class="badge bg-secondary ms-1"><i class="ri-building-line me-1"></i>In-Person</span>
                        @endif
                    </div>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.governance.meetings.index') }}">{{ __('Meetings') }}</a></li>
                        <li class="breadcrumb-item active">{{ $meeting->reference_number }}</li>
                    </ol>
                </div>

                {{-- Info Row --}}
                <div class="row g-3">
                    <div class="col-md-2 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-2"><i class="ri-calendar-event-line text-primary"></i></div>
                            <div>
                                <div class="text-muted small">{{ __('Date') }}</div>
                                <div class="fw-medium">{{ $meeting->scheduled_at ? $meeting->scheduled_at->format('d M Y') : '—' }}</div>
                                <div class="text-muted small">{{ $meeting->scheduled_at ? $meeting->scheduled_at->format('H:i') : '' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-purple bg-opacity-10 p-2" style="background:rgba(111,66,193,.1)!important"><i class="ri-user-star-line" style="color:#6f42c1"></i></div>
                            <div>
                                <div class="text-muted small">{{ __('Chairman') }}</div>
                                <div class="fw-medium">{{ $meeting->chairman ?: '—' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-success bg-opacity-10 p-2"><i class="ri-group-line text-success"></i></div>
                            <div>
                                <div class="text-muted small">{{ __('Attendees') }}</div>
                                <div class="fw-medium">{{ $meeting->attendees->count() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-info bg-opacity-10 p-2"><i class="ri-file-list-3-line text-info"></i></div>
                            <div>
                                <div class="text-muted small">{{ __('Documents') }}</div>
                                <div class="fw-medium">{{ $meeting->documents->count() }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-2"><i class="ri-flag-line text-warning"></i></div>
                            <div>
                                <div class="text-muted small">{{ __('Status') }}</div>
                                <div class="fw-medium">
                                    @if($meeting->status == MEETING_STATUS_SCHEDULED)
                                        <span class="badge bg-primary">{{ __('Scheduled') }}</span>
                                    @elseif($meeting->status == MEETING_STATUS_COMPLETED)
                                        <span class="badge bg-success">{{ __('Completed') }}</span>
                                    @elseif($meeting->status == MEETING_STATUS_CANCELLED)
                                        <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($meeting->venue)
                    <div class="col-md-2 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-secondary bg-opacity-10 p-2"><i class="ri-map-pin-line text-secondary"></i></div>
                            <div>
                                <div class="text-muted small">{{ __('Venue') }}</div>
                                <div class="fw-medium text-truncate" style="max-width:130px" title="{{ $meeting->venue }}">{{ $meeting->venue }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="row g-4">

                {{-- LEFT COL --}}
                <div class="col-lg-8">

                    {{-- Google Meet / Virtual Link Card --}}
                    @if($meeting->virtual_link || in_array($mode, [MEETING_MODE_VIRTUAL, MEETING_MODE_HYBRID]))
                    <div class="card mb-4 border-success">
                        <div class="card-header d-flex align-items-center gap-2" style="background:#f0fdf4">
                            @if($meeting->google_meet_space_id)
                            <svg width="20" height="20" viewBox="0 0 48 48"><path fill="#4285F4" d="M29 20.5V17l8-8v22l-8-8v-2.5z"/><rect width="22" height="22" x="5" y="13" rx="3" fill="#00832d"/><path fill="#fff" d="M19 18h4v4h-4zm0 8h4v4h-4zm6-8h4v4h-4zm0 8h4v4h-4z"/></svg>
                            <span class="fw-semibold">{{ __('Google Meet') }}</span>
                            @else
                            <i class="ri-video-line text-success fs-5"></i>
                            <span class="fw-semibold">{{ __('Virtual Meeting Link') }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($meeting->virtual_link)
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <code class="flex-grow-1 bg-light rounded px-3 py-2 text-break" id="meetLinkText">{{ $meeting->virtual_link }}</code>
                                <button class="btn btn-outline-secondary btn-sm" onclick="copyMeetLink()" title="{{ __('Copy link') }}">
                                    <i class="ri-file-copy-line"></i>
                                </button>
                                <a href="{{ $meeting->virtual_link }}" target="_blank" class="btn btn-success btn-sm">
                                    <i class="ri-video-line me-1"></i>{{ __('Join Meeting') }}
                                </a>
                            </div>
                            @if($meeting->google_meet_space_id)
                            <div class="mt-2 text-muted small">
                                <i class="ri-server-line me-1"></i>{{ __('Space ID') }}: <code>{{ $meeting->google_meet_space_id }}</code>
                            </div>
                            @endif
                            @else
                            <div class="text-muted">{{ __('No virtual link set yet.') }}</div>
                            @endif

                            {{-- Regenerate / update link --}}
                            <form action="{{ route('admin.governance.meetings.recording.update', $meeting) }}" method="POST" class="ajax mt-3 pt-3 border-top" data-handler="getShowMessage" id="updateLinkForm">
                                @csrf
                                <div class="d-flex gap-2 align-items-end">
                                    <div class="flex-grow-1">
                                        <label class="form-label small mb-1">{{ __('Update Meeting Link') }}</label>
                                        <input type="url" name="recording_url" class="form-control form-control-sm" placeholder="https://meet.google.com/..." style="display:none">
                                        {{-- We reuse the recording update route with a virtual_link field below --}}
                                    </div>
                                </div>
                            </form>

                            @if($googleMeetReady && !$meeting->google_meet_space_id)
                            <form action="{{ route('admin.governance.meetings.store') }}" method="POST" class="mt-2" id="regenerateGoogleMeet">
                                {{-- Regenerate via a dedicated AJAX call handled in JS --}}
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="generateGoogleMeet({{ $meeting->id }})">
                                    <svg width="14" height="14" viewBox="0 0 48 48" class="me-1"><path fill="#4285F4" d="M29 20.5V17l8-8v22l-8-8v-2.5z"/><rect width="22" height="22" x="5" y="13" rx="3" fill="#00832d"/><path fill="#fff" d="M19 18h4v4h-4zm0 8h4v4h-4zm6-8h4v4h-4zm0 8h4v4h-4z"/></svg>
                                    {{ __('Generate Google Meet Link') }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Minutes Editor --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-semibold"><i class="ri-file-text-line me-2"></i>{{ __('Meeting Minutes') }}</h6>
                            <span class="badge bg-secondary small" id="minutesStatus">{{ $meeting->minutes ? __('Saved') : __('Not written') }}</span>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.governance.meetings.minutes.update', $meeting) }}" method="POST" class="ajax" data-handler="minutesSaveHandler" id="minutesForm">
                                @csrf
                                <textarea name="minutes" id="minutesEditor" class="form-control" rows="12"
                                    placeholder="{{ __('Type meeting minutes here... Record decisions, action items, and discussion summaries.') }}">{{ $meeting->minutes }}</textarea>
                                <div class="mt-2 d-flex align-items-center justify-content-between">
                                    <small class="text-muted"><i class="ri-information-line me-1"></i>{{ __('Auto-saved when you click Save.') }}</small>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="ri-save-line me-1"></i>{{ __('Save Minutes') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Documents --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-semibold"><i class="ri-attachment-2 me-2"></i>{{ __('Meeting Documents') }}</h6>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                                <i class="ri-upload-line me-1"></i>{{ __('Upload') }}
                            </button>
                        </div>
                        <div class="card-body p-0" id="documentsContainer">
                            @if($meeting->documents->isEmpty())
                                <div class="text-center text-muted py-5" id="noDocsMsg">
                                    <i class="ri-folder-open-line fs-2 d-block mb-2"></i>
                                    {{ __('No documents uploaded yet.') }}
                                </div>
                            @else
                            <div class="list-group list-group-flush">
                                @foreach($meeting->documents as $doc)
                                <div class="list-group-item d-flex align-items-center gap-3 py-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                                        @php
                                            $ext = $doc->file ? strtolower(pathinfo($doc->file->file_name ?? '', PATHINFO_EXTENSION)) : '';
                                            $icon = in_array($ext, ['pdf']) ? 'ri-file-pdf-line text-danger'
                                                  : (in_array($ext, ['doc','docx']) ? 'ri-file-word-line text-primary'
                                                  : (in_array($ext, ['xls','xlsx']) ? 'ri-file-excel-line text-success'
                                                  : (in_array($ext, ['mp4','mov','avi','mkv']) ? 'ri-video-line text-purple'
                                                  : 'ri-file-line text-secondary')));
                                        @endphp
                                        <i class="{{ $icon }} fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium">{{ $doc->title }}</div>
                                        <small class="text-muted">{{ $doc->document_type }} · {{ $doc->created_at->format('d M Y') }}</small>
                                    </div>
                                    @if($doc->file)
                                    <a href="{{ asset('storage/' . $doc->file->folder_name . '/' . $doc->file->file_name) }}"
                                       target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="ri-download-line"></i>
                                    </a>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Recordings --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-record-circle-line me-2"></i>{{ __('Recording') }}</h6>
                        </div>
                        <div class="card-body">
                            @if($meeting->recording_url)
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="ri-video-line text-danger fs-4"></i>
                                <div class="flex-grow-1">
                                    <a href="{{ $meeting->recording_url }}" target="_blank" class="text-break">{{ $meeting->recording_url }}</a>
                                </div>
                            </div>
                            @endif
                            <form action="{{ route('admin.governance.meetings.recording.update', $meeting) }}" method="POST" class="ajax" data-handler="getShowMessage">
                                @csrf
                                <div class="input-group">
                                    <input type="url" name="recording_url" class="form-control"
                                        value="{{ $meeting->recording_url }}"
                                        placeholder="https://drive.google.com/... or https://meet.google.com/...">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line"></i>
                                    </button>
                                </div>
                                <small class="text-muted mt-1 d-block">{{ __('Paste a Google Drive, Google Meet recording, or any URL.') }}</small>
                            </form>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COL --}}
                <div class="col-lg-4">

                    {{-- Status Update --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-refresh-line me-2"></i>{{ __('Update Status') }}</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.governance.meetings.status.update', $meeting) }}" method="POST" class="ajax" data-handler="getShowMessage">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small">{{ __('Status') }}</label>
                                    <select name="status" class="form-select">
                                        <option value="{{ MEETING_STATUS_SCHEDULED }}"  {{ $meeting->status == MEETING_STATUS_SCHEDULED  ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                                        <option value="{{ MEETING_STATUS_COMPLETED }}"  {{ $meeting->status == MEETING_STATUS_COMPLETED  ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                        <option value="{{ MEETING_STATUS_CANCELLED }}"  {{ $meeting->status == MEETING_STATUS_CANCELLED  ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">{{ __('Started At') }}</label>
                                    <input type="datetime-local" name="started_at" class="form-control"
                                        value="{{ $meeting->started_at ? $meeting->started_at->format('Y-m-d\TH:i') : '' }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small">{{ __('Ended At') }}</label>
                                    <input type="datetime-local" name="ended_at" class="form-control"
                                        value="{{ $meeting->ended_at ? $meeting->ended_at->format('Y-m-d\TH:i') : '' }}">
                                </div>
                                @if($meeting->duration_minutes)
                                <div class="alert alert-info py-2 mb-3">
                                    <i class="ri-time-line me-1"></i>{{ __('Duration') }}: <strong>{{ $meeting->duration_minutes }} {{ __('min') }}</strong>
                                </div>
                                @endif
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ri-save-line me-1"></i>{{ __('Update Status') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Agenda --}}
                    @if($meeting->agenda)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-list-check me-2"></i>{{ __('Agenda') }}</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 text-muted small" style="white-space:pre-wrap">{{ $meeting->agenda }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Meeting Objectives --}}
                    @if($meeting->meeting_objectives)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-target-line me-2"></i>{{ __('Meeting Objectives') }}</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 text-muted small" style="white-space:pre-wrap">{{ $meeting->meeting_objectives }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Chairman Info --}}
                    @if($meeting->chairman)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-user-star-line me-2"></i>{{ __('Chairman') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width:36px;height:36px;background:#6f42c1;font-size:14px">
                                    {{ strtoupper(substr($meeting->chairman, 0, 1)) }}
                                </div>
                                <span class="fw-medium">{{ $meeting->chairman }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Attendees --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-semibold"><i class="ri-group-line me-2"></i>{{ __('Attendees') }}</h6>
                            <span class="badge bg-info">{{ $meeting->attendees->count() }}</span>
                        </div>
                        <div class="card-body p-0">
                            @if($meeting->attendees->isEmpty())
                            <div class="text-center text-muted py-3 small">{{ __('No attendees.') }}</div>
                            @else
                            <ul class="list-group list-group-flush" style="max-height:280px;overflow-y:auto">
                                @foreach($meeting->attendees->take(20) as $att)
                                <li class="list-group-item d-flex align-items-center gap-2 py-2">
                                    <div class="avatar-xs rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:28px;height:28px;font-size:11px">
                                        {{ strtoupper(substr($att->shareholder?->full_name ?? $att->shareholder?->user?->name ?? '?', 0, 2)) }}
                                    </div>
                                    <div class="small">
                                        <div class="fw-medium">{{ $att->shareholder?->full_name ?? $att->shareholder?->user?->name ?? '—' }}</div>
                                        <div class="text-muted" style="font-size:0.7rem">{{ $att->shareholder?->user?->email ?? '' }}</div>
                                    </div>
                                    @if($att->attended)
                                        <span class="badge bg-success ms-auto" style="font-size:0.65rem">{{ __('Attended') }}</span>
                                    @else
                                        <span class="badge bg-light text-muted ms-auto" style="font-size:0.65rem">{{ __('Invited') }}</span>
                                    @endif
                                </li>
                                @endforeach
                                @if($meeting->attendees->count() > 20)
                                <li class="list-group-item text-center text-muted small py-2">
                                    + {{ $meeting->attendees->count() - 20 }} {{ __('more') }}
                                </li>
                                @endif
                            </ul>
                            @endif
                        </div>
                    </div>

                    {{-- Linked Resolutions --}}
                    @if($meeting->resolutions->count())
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-semibold"><i class="ri-git-pull-request-line me-2"></i>{{ __('Linked Resolutions') }}</h6>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @foreach($meeting->resolutions as $res)
                                <li class="list-group-item small py-2">
                                    {{ $res->title }}
                                    @if($res->status == RESOLUTION_STATUS_PASSED)
                                        <span class="badge bg-success float-end">{{ __('Passed') }}</span>
                                    @elseif($res->status == RESOLUTION_STATUS_FAILED)
                                        <span class="badge bg-danger float-end">{{ __('Failed') }}</span>
                                    @else
                                        <span class="badge bg-secondary float-end">{{ $res->status }}</span>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

{{-- Upload Document Modal --}}
<div class="modal fade" id="uploadDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-upload-line me-2"></i>{{ __('Upload Document') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.governance.meetings.document.upload', $meeting) }}"
                  method="POST" enctype="multipart/form-data" class="ajax" data-handler="docUploadHandler">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="{{ __('e.g. Board Resolution Draft') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Document Type') }} <span class="text-danger">*</span></label>
                        <select name="document_type" class="form-select" required>
                            <option value="">{{ __('Select Type') }}</option>
                            <option value="minutes">{{ __('Minutes') }}</option>
                            <option value="agenda">{{ __('Agenda') }}</option>
                            <option value="resolution">{{ __('Resolution') }}</option>
                            <option value="presentation">{{ __('Presentation') }}</option>
                            <option value="recording">{{ __('Recording') }}</option>
                            <option value="report">{{ __('Report') }}</option>
                            <option value="other">{{ __('Other') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('File') }} <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" required
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.png,.jpg,.mp4,.mov">
                        <small class="text-muted">{{ __('Max 20MB. PDF, Word, Excel, PPT, images, video.') }}</small>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="all_shareholders" id="docAllShareholders" value="1">
                        <label class="form-check-label small" for="docAllShareholders">
                            {{ __('Visible to all shareholders') }}
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-upload-line me-1"></i>{{ __('Upload') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    #minutesEditor { font-family: 'Courier New', monospace; font-size: 0.9rem; resize: vertical; min-height: 280px; }
    .text-purple { color: #6f42c1; }
</style>
@endpush

@push('script')
<script>
function copyMeetLink() {
    var text = document.getElementById('meetLinkText').textContent.trim();
    navigator.clipboard.writeText(text).then(function() {
        toastr.success('{{ __("Meeting link copied!") }}');
    }).catch(function() {
        var el = document.createElement('textarea');
        el.value = text;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        toastr.success('{{ __("Copied!") }}');
    });
}

function generateGoogleMeet(meetingId) {
    var btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>{{ __("Creating...") }}';
    $.ajax({
        url: '{{ route("admin.governance.meetings.generate-meet", $meeting) }}',
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(res) {
            if (res.status) {
                toastr.success(res.message || '{{ __("Google Meet link generated.") }}');
                if (res.data && res.data.virtual_link) {
                    toastr.info(
                        '<a href="' + res.data.virtual_link + '" target="_blank">' + res.data.virtual_link + '</a>',
                        'Meet Link', { timeOut: 8000, allowHtml: true }
                    );
                }
                setTimeout(function() { location.reload(); }, 1200);
            } else {
                toastr.error(res.message || '{{ __("Failed to generate link.") }}');
                btn.disabled = false;
                btn.innerHTML = '{{ __("Generate Google Meet Link") }}';
            }
        },
        error: function(xhr) {
            toastr.error('{{ __("Request failed.") }}');
            btn.disabled = false;
            btn.innerHTML = '{{ __("Generate Google Meet Link") }}';
        }
    });
}

// Minutes save handler
window.minutesSaveHandler = function(res) {
    if (res.status) {
        toastr.success(res.message);
        document.getElementById('minutesStatus').textContent = '{{ __("Saved") }}';
        document.getElementById('minutesStatus').className = 'badge bg-success small';
    } else {
        toastr.error(res.message);
    }
};

// Document upload handler — refresh the document list
window.docUploadHandler = function(res) {
    if (res.status) {
        toastr.success(res.message);
        bootstrap.Modal.getInstance(document.getElementById('uploadDocModal')).hide();
        setTimeout(function() { location.reload(); }, 800);
    } else {
        toastr.error(res.message);
    }
};
</script>
@endpush
