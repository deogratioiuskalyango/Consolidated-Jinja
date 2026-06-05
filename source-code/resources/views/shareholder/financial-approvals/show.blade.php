@extends('shareholder.layouts.app')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h2 class="mb-1">{{ __('Financial Approval Details') }}</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 small">
                                <li class="breadcrumb-item"><a href="{{ route('shareholder.financial-approvals.index') }}">{{ __('Approvals') }}</a></li>
                                <li class="breadcrumb-item active">{{ Str::limit($approval->title, 40) }}</li>
                            </ol>
                        </nav>
                    </div>
                    <a href="{{ route('shareholder.financial-approvals.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ri-arrow-left-line me-1"></i>{{ __('Back') }}
                    </a>
                </div>

                @php
                    $statusMap = [
                        0 => ['label' => 'Pending',  'ring' => 'status-ring-open',   'icon' => 'ri-time-line'],
                        1 => ['label' => 'Approved', 'ring' => 'status-ring-passed', 'icon' => 'ri-check-double-line'],
                        2 => ['label' => 'Rejected', 'ring' => 'status-ring-failed', 'icon' => 'ri-close-line'],
                        3 => ['label' => 'Expired',  'ring' => 'status-ring-closed', 'icon' => 'ri-close-circle-line'],
                    ];
                    $st = $statusMap[$approval->status] ?? ['label' => 'Unknown', 'ring' => 'status-ring-draft', 'icon' => 'ri-question-line'];

                    $totalActions = $approval->actions->count();
                    $approveCount = $approval->actions->where('action', 1)->count();
                    $rejectCount  = $approval->actions->where('action', 2)->count();
                    $approvePct   = $totalActions > 0 ? round(($approveCount / $totalActions) * 100, 1) : 0;
                    $rejectPct    = $totalActions > 0 ? round(($rejectCount / $totalActions) * 100, 1) : 0;
                @endphp

                <div class="row g-4">

                    {{-- LEFT: Details + Progress --}}
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                                    <h4 class="mb-0 me-2">{{ $approval->title }}</h4>
                                    <span class="resolution-status-ring {{ $st['ring'] }} flex-shrink-0">
                                        <i class="{{ $st['icon'] }}"></i> {{ __($st['label']) }}
                                    </span>
                                </div>

                                @if($approval->description)
                                    <p class="text-secondary mb-4">{{ $approval->description }}</p>
                                @endif

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center gap-2 p-3 bg-light rounded-3">
                                            <i class="ri-money-dollar-circle-line text-muted fs-5"></i>
                                            <div>
                                                <div class="text-muted" style="font-size:.75rem;">{{ __('Amount') }}</div>
                                                <div class="fw-bold">{{ $approval->currency ?? '' }} {{ number_format($approval->amount, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center gap-2 p-3 bg-light rounded-3">
                                            <i class="ri-percent-line text-muted fs-5"></i>
                                            <div>
                                                <div class="text-muted" style="font-size:.75rem;">{{ __('Approval Threshold') }}</div>
                                                <div class="fw-semibold small">{{ isset($approval->approval_threshold) ? $approval->approval_threshold . '%' : __('N/A') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center gap-2 p-3 bg-light rounded-3">
                                            <i class="ri-user-line text-muted fs-5"></i>
                                            <div>
                                                <div class="text-muted" style="font-size:.75rem;">{{ __('Requested By') }}</div>
                                                <div class="fw-semibold small">{{ $approval->requestedBy->name ?? __('N/A') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center gap-2 p-3 bg-light rounded-3">
                                            <i class="ri-calendar-line text-muted fs-5"></i>
                                            <div>
                                                <div class="text-muted" style="font-size:.75rem;">{{ __('Deadline') }}</div>
                                                <div class="fw-semibold small">{{ $approval->deadline ? $approval->deadline->format('M d, Y') : __('N/A') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Approval progress --}}
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">{{ __('Approval Progress') }}</h5>
                            </div>
                            <div class="card-body">
                                @if($totalActions > 0)
                                    <p class="text-muted small mb-3">
                                        {{ __('Total actions cast:') }} <strong>{{ $totalActions }}</strong>
                                    </p>
                                    <div class="result-bar-row">
                                        <div class="result-bar-label">
                                            <span class="fw-semibold text-success"><i class="ri-check-line me-1"></i>{{ __('Approve') }}</span>
                                            <span class="text-success fw-bold">{{ $approveCount }} &nbsp;({{ $approvePct }}%)</span>
                                        </div>
                                        <div class="progress-thick"><div class="progress-bar bg-success" style="width:{{ $approvePct }}%"></div></div>
                                    </div>
                                    <div class="result-bar-row">
                                        <div class="result-bar-label">
                                            <span class="fw-semibold text-danger"><i class="ri-close-line me-1"></i>{{ __('Reject') }}</span>
                                            <span class="text-danger fw-bold">{{ $rejectCount }} &nbsp;({{ $rejectPct }}%)</span>
                                        </div>
                                        <div class="progress-thick"><div class="progress-bar bg-danger" style="width:{{ $rejectPct }}%"></div></div>
                                    </div>
                                @else
                                    <div class="text-center text-muted py-3">
                                        <i class="ri-inbox-line fs-2 d-block mb-2"></i>
                                        {{ __('No actions have been taken yet.') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Action panel --}}
                    <div class="col-lg-5">

                        {{-- Threshold context --}}
                        @if(isset($threshold) && $threshold)
                        <div class="card border-0 shadow-sm border-start border-4 border-info mb-3">
                            <div class="card-body py-3">
                                <div class="fw-semibold small mb-1">
                                    <i class="ri-shield-check-line me-1 text-info"></i>{{ __('Approval Rule:') }} {{ $threshold->name }}
                                </div>
                                <div class="text-muted small">
                                    {{ __('Min approvers required:') }} <strong>{{ $threshold->min_approver_count }}</strong>
                                    @if($threshold->requires_quorum)
                                        &nbsp;·&nbsp; {{ __('Quorum:') }} <strong>{{ $threshold->quorum_percentage }}%</strong>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- No permission --}}
                        @if(isset($canApprove) && !$canApprove && !$myAction)
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body vote-unavailable-box">
                                <div class="unavailable-icon" style="color:#d97706;"><i class="ri-shield-cross-line"></i></div>
                                <h6 class="mb-1">{{ __('Insufficient Permissions') }}</h6>
                                <p class="text-muted small mb-0">
                                    {{ __('Your share class does not have authority to approve this expense.') }}
                                    @if(isset($approvalLimit) && $approvalLimit !== null)
                                        {{ __('Your approval limit is') }} <strong>{{ number_format($approvalLimit, 2) }}</strong>.
                                    @endif
                                </p>
                            </div>
                        </div>

                        @elseif($myAction)
                            {{-- Already acted --}}
                            @php
                                $actionLabels = [
                                    1 => ['label' => 'Approved', 'pill' => 'pill-for',     'icon' => 'ri-check-fill'],
                                    2 => ['label' => 'Rejected', 'pill' => 'pill-against', 'icon' => 'ri-close-fill'],
                                ];
                                $al = $actionLabels[$myAction->action] ?? ['label' => 'Unknown', 'pill' => 'pill-abstain', 'icon' => 'ri-question-mark'];
                            @endphp
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center py-4">
                                    <div class="mb-3">
                                        <i class="ri-checkbox-circle-fill text-success" style="font-size:3rem;"></i>
                                    </div>
                                    <h5 class="mb-1">{{ __('Your decision is recorded') }}</h5>
                                    <p class="text-muted small mb-3">{{ $myAction->created_at->format('M d, Y \a\t H:i') }}</p>
                                    <div class="d-flex justify-content-center mb-3">
                                        <span class="my-vote-pill {{ $al['pill'] }}">
                                            <i class="{{ $al['icon'] }}"></i> {{ __($al['label']) }}
                                        </span>
                                    </div>
                                    @if($myAction->comment)
                                        <div class="p-3 bg-light rounded-3 text-start">
                                            <small class="text-muted d-block mb-1">{{ __('Your comment') }}</small>
                                            <p class="mb-0 small">{{ $myAction->comment }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        @elseif($approval->status == 0 && (!isset($canApprove) || $canApprove))
                            {{-- Decision form --}}
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent border-bottom-0 pb-0 pt-4">
                                    <h5 class="mb-0"><i class="ri-judge-line me-2 text-primary"></i>{{ __('Submit Your Decision') }}</h5>
                                    @if(isset($approvalLimit) && $approvalLimit !== null)
                                    <p class="text-muted small mt-1 mb-0">
                                        {{ __('Your approval authority:') }}
                                        <strong>{{ __('up to') }} {{ number_format($approvalLimit, 2) }}</strong>
                                    </p>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('shareholder.financial-approvals.action', $approval) }}" method="POST" id="approvalForm">
                                        @csrf
                                        <input type="hidden" name="action" id="hiddenAction" value="">

                                        <div class="mb-4">
                                            <label class="form-label fw-semibold mb-3">
                                                {{ __('Your Decision') }} <span class="text-danger">*</span>
                                            </label>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <button type="button" class="decision-btn btn-approve" id="btnApprove" onclick="selectDecision(1)">
                                                        <i class="ri-check-line fs-5"></i>
                                                        {{ __('Approve') }}
                                                    </button>
                                                </div>
                                                <div class="col-6">
                                                    <button type="button" class="decision-btn btn-reject" id="btnReject" onclick="selectDecision(2)">
                                                        <i class="ri-close-line fs-5"></i>
                                                        {{ __('Reject') }}
                                                    </button>
                                                </div>
                                            </div>
                                            @error('action')
                                                <div class="text-danger small mt-2"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="decisionComment" class="form-label fw-semibold">
                                                {{ __('Comment') }}
                                                <span class="text-muted fw-normal small">({{ __('optional') }})</span>
                                            </label>
                                            <textarea class="form-control @error('comment') is-invalid @enderror"
                                                id="decisionComment" name="comment" rows="3"
                                                placeholder="{{ __('Add your reasoning or notes (optional)...') }}">{{ old('comment') }}</textarea>
                                            @error('comment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="alert alert-warning py-2 small mb-4">
                                            <i class="ri-error-warning-line me-1"></i>
                                            <strong>{{ __('Note:') }}</strong>
                                            {{ __('Your decision is final and cannot be changed.') }}
                                        </div>

                                        <button type="button" class="btn btn-primary w-100 py-2" id="btnSubmitDecision" disabled>
                                            <i class="ri-send-plane-line me-1"></i>{{ __('Submit Decision') }}
                                        </button>
                                        <p class="text-center text-muted small mt-2 mb-0" id="decisionHint">
                                            {{ __('Select Approve or Reject above to continue') }}
                                        </p>
                                    </form>
                                </div>
                            </div>

                        @elseif($approval->status != 0)
                            <div class="card border-0 shadow-sm">
                                <div class="card-body vote-unavailable-box">
                                    <div class="unavailable-icon" style="color:#6b7280;"><i class="ri-lock-line"></i></div>
                                    <h6 class="mb-1">{{ __('No Longer Pending') }}</h6>
                                    <p class="text-muted small mb-0">{{ __('This approval is') }} <strong>{{ __($st['label']) }}</strong>.</p>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
var selectedActionValue = null;

function selectDecision(val) {
    selectedActionValue = val;
    document.getElementById('hiddenAction').value = val;

    var btnA = document.getElementById('btnApprove');
    var btnR = document.getElementById('btnReject');
    if (val === 1) {
        btnA && btnA.classList.add('selected');
        btnR && btnR.classList.remove('selected');
    } else {
        btnR && btnR.classList.add('selected');
        btnA && btnA.classList.remove('selected');
    }
    var submitBtn = document.getElementById('btnSubmitDecision');
    var hint      = document.getElementById('decisionHint');
    if (submitBtn) submitBtn.disabled = false;
    if (hint)      hint.style.display = 'none';
}

(function () {
    var form      = document.getElementById('approvalForm');
    var submitBtn = document.getElementById('btnSubmitDecision');
    if (!submitBtn || !form) return;
    submitBtn.addEventListener('click', function () {
        if (!selectedActionValue) return;
        var labelMap = { 1: '{{ __("Approve") }}', 2: '{{ __("Reject") }}' };
        var label = labelMap[selectedActionValue] || selectedActionValue;
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '{{ __("Confirm Decision") }}',
                html: '{{ __("You are about to:") }} <strong>' + label + '</strong><br><span class="text-muted small">{{ __("This cannot be undone.") }}</span>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '{{ __("Yes, Confirm") }}',
                cancelButtonText: '{{ __("Go Back") }}',
                confirmButtonColor: '#3686FC',
                reverseButtons: true,
            }).then(function (result) { if (result.isConfirmed) { form.submit(); } });
        } else {
            if (confirm('{{ __("Confirm:") }} ' + label + '\n{{ __("This cannot be changed.") }}')) { form.submit(); }
        }
    });
})();
</script>
@endpush
