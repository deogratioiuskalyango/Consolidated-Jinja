@extends('shareholder.layouts.app')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page header --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <h2 class="mb-1">{{ __('Resolution Details') }}</h2>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0 small">
                                        <li class="breadcrumb-item"><a href="{{ route('shareholder.resolutions.index') }}">{{ __('Resolutions') }}</a></li>
                                        <li class="breadcrumb-item active">{{ Str::limit($resolution->title, 40) }}</li>
                                    </ol>
                                </nav>
                            </div>
                            <a href="{{ route('shareholder.resolutions.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ri-arrow-left-line me-1"></i>{{ __('Back to List') }}
                            </a>
                        </div>
                    </div>
                </div>

                @php
                    $statusRingMap = [
                        0 => ['label' => 'Draft',  'ring' => 'status-ring-draft',  'icon' => 'ri-draft-line'],
                        1 => ['label' => 'Open',   'ring' => 'status-ring-open',   'icon' => 'ri-checkbox-circle-line'],
                        2 => ['label' => 'Closed', 'ring' => 'status-ring-closed', 'icon' => 'ri-close-circle-line'],
                        3 => ['label' => 'Passed', 'ring' => 'status-ring-passed', 'icon' => 'ri-check-double-line'],
                        4 => ['label' => 'Failed', 'ring' => 'status-ring-failed', 'icon' => 'ri-close-line'],
                    ];
                    $st = $statusRingMap[$resolution->status] ?? ['label' => 'Unknown', 'ring' => 'status-ring-draft', 'icon' => 'ri-question-line'];

                    $totalVotes   = $resolution->votes->count();
                    $forVotes     = $resolution->votes->where('vote', 1)->count();
                    $againstVotes = $resolution->votes->where('vote', 2)->count();
                    $abstainVotes = $resolution->votes->where('vote', 3)->count();
                    $forPct     = $totalVotes > 0 ? round(($forVotes / $totalVotes) * 100, 1) : 0;
                    $againstPct = $totalVotes > 0 ? round(($againstVotes / $totalVotes) * 100, 1) : 0;
                    $abstainPct = $totalVotes > 0 ? round(($abstainVotes / $totalVotes) * 100, 1) : 0;

                    $wr          = $weightedResults ?? [];
                    $hasWeighted = !empty($wr) && ($wr['total_weight'] ?? 0) > 0;
                    $wForPct     = $wr['for_percentage']     ?? 0;
                    $wAgainstPct = $wr['against_percentage'] ?? 0;
                    $wAbstainPct = $wr['abstain_percentage'] ?? 0;

                    // Voting window diagnostics
                    $now            = now();
                    $isStatusOpen   = (int) $resolution->status === 1;
                    $hasOpenDate    = $resolution->voting_opens_at !== null;
                    $hasCloseDate   = $resolution->voting_closes_at !== null;
                    $votingStarted  = $hasOpenDate  && $now->gte($resolution->voting_opens_at);
                    $votingNotEnded = $hasCloseDate && $now->lte($resolution->voting_closes_at);
                    $windowOpen     = $isStatusOpen && $hasOpenDate && $hasCloseDate && $votingStarted && $votingNotEnded;
                @endphp

                <div class="row g-4">

                    {{-- ── LEFT: Resolution info + results ──────────────────── --}}
                    <div class="col-lg-7">

                        {{-- Resolution info card --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                                    <h4 class="mb-0 me-2">{{ $resolution->title }}</h4>
                                    <span class="resolution-status-ring {{ $st['ring'] }} flex-shrink-0">
                                        <i class="{{ $st['icon'] }}"></i> {{ __($st['label']) }}
                                    </span>
                                </div>

                                @if($resolution->type)
                                    <span class="badge bg-secondary mb-3">{{ $resolution->type_label ?? $resolution->type }}</span>
                                @endif

                                @if($resolution->description)
                                    <p class="text-secondary mb-4">{{ $resolution->description }}</p>
                                @endif

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center gap-2 p-3 bg-light rounded-3">
                                            <i class="ri-time-line text-muted fs-5"></i>
                                            <div>
                                                <div class="text-muted" style="font-size:.75rem">{{ __('Voting Opens') }}</div>
                                                <div class="fw-semibold small">
                                                    {{ $resolution->voting_opens_at ? $resolution->voting_opens_at->format('M d, Y H:i') : __('Not set') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center gap-2 p-3 bg-light rounded-3">
                                            <i class="ri-calendar-close-line text-muted fs-5"></i>
                                            <div>
                                                <div class="text-muted" style="font-size:.75rem">{{ __('Voting Closes') }}</div>
                                                <div class="fw-semibold small">
                                                    {{ $resolution->voting_closes_at ? $resolution->voting_closes_at->format('M d, Y H:i') : __('Not set') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center gap-2 p-3 bg-light rounded-3">
                                            <i class="ri-group-line text-muted fs-5"></i>
                                            <div>
                                                <div class="text-muted" style="font-size:.75rem">{{ __('Votes Cast') }}</div>
                                                <div class="fw-semibold small">{{ $totalVotes }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(isset($resolution->quorum_percent) && $resolution->quorum_percent)
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center gap-2 p-3 bg-light rounded-3">
                                            <i class="ri-percent-line text-muted fs-5"></i>
                                            <div>
                                                <div class="text-muted" style="font-size:.75rem">{{ __('Quorum Required') }}</div>
                                                <div class="fw-semibold small">{{ $resolution->quorum_percent }}%</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if(isset($votingWeight) && $votingWeight > 0)
                                    <div class="col-sm-6">
                                        <div class="d-flex align-items-center gap-2 p-3 rounded-3" style="background:#ede9fe;">
                                            <i class="ri-scales-line fs-5" style="color:#6d28d9;"></i>
                                            <div>
                                                <div style="font-size:.75rem;color:#6d28d9;">{{ __('Your Voting Weight') }}</div>
                                                <div class="fw-bold small" style="color:#6d28d9;">{{ number_format($votingWeight, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                {{-- Countdown timer --}}
                                @if($windowOpen && $resolution->voting_closes_at)
                                <div class="mt-4 p-3 rounded-3 border border-success-subtle" style="background:#f0fdf4;">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="ri-time-line text-success"></i>
                                        <span class="text-success fw-semibold small">{{ __('Voting closes in') }}</span>
                                    </div>
                                    <div class="voting-countdown" id="votingCountdown"
                                         data-deadline="{{ $resolution->voting_closes_at->toISOString() }}">
                                        <div class="countdown-unit"><span class="cdown-val" id="cd-days">--</span><span class="cdown-lbl">{{ __('Days') }}</span></div>
                                        <div class="countdown-unit"><span class="cdown-val" id="cd-hours">--</span><span class="cdown-lbl">{{ __('Hours') }}</span></div>
                                        <div class="countdown-unit"><span class="cdown-val" id="cd-mins">--</span><span class="cdown-lbl">{{ __('Mins') }}</span></div>
                                        <div class="countdown-unit"><span class="cdown-val" id="cd-secs">--</span><span class="cdown-lbl">{{ __('Secs') }}</span></div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Results card --}}
                        @if($resolution->status != 0)
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h5 class="mb-0">{{ __('Voting Results') }}</h5>
                                @if($hasWeighted)
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary active" id="btnHeadcount">{{ __('Headcount') }}</button>
                                    <button type="button" class="btn btn-outline-primary" id="btnWeighted">{{ __('Weighted') }}</button>
                                </div>
                                @endif
                            </div>
                            <div class="card-body">
                                @if($totalVotes > 0)
                                    {{-- Headcount --}}
                                    <div id="resultsHeadcount">
                                        <p class="text-muted small mb-3">{{ __('Based on') }} {{ $totalVotes }} {{ __('vote(s) cast') }}</p>
                                        <div class="result-bar-row">
                                            <div class="result-bar-label">
                                                <span class="fw-semibold text-success"><i class="ri-thumb-up-line me-1"></i>{{ __('For') }}</span>
                                                <span class="text-success fw-bold">{{ $forVotes }} &nbsp;({{ $forPct }}%)</span>
                                            </div>
                                            <div class="progress-thick"><div class="progress-bar bg-success" style="width:{{ $forPct }}%"></div></div>
                                        </div>
                                        <div class="result-bar-row">
                                            <div class="result-bar-label">
                                                <span class="fw-semibold text-danger"><i class="ri-thumb-down-line me-1"></i>{{ __('Against') }}</span>
                                                <span class="text-danger fw-bold">{{ $againstVotes }} &nbsp;({{ $againstPct }}%)</span>
                                            </div>
                                            <div class="progress-thick"><div class="progress-bar bg-danger" style="width:{{ $againstPct }}%"></div></div>
                                        </div>
                                        <div class="result-bar-row">
                                            <div class="result-bar-label">
                                                <span class="fw-semibold text-secondary"><i class="ri-subtract-line me-1"></i>{{ __('Abstain') }}</span>
                                                <span class="text-secondary fw-bold">{{ $abstainVotes }} &nbsp;({{ $abstainPct }}%)</span>
                                            </div>
                                            <div class="progress-thick"><div class="progress-bar bg-secondary" style="width:{{ $abstainPct }}%"></div></div>
                                        </div>
                                    </div>

                                    {{-- Weighted --}}
                                    @if($hasWeighted)
                                    <div id="resultsWeighted" style="display:none;">
                                        <p class="text-muted small mb-3">
                                            {{ __('By weighted vote — total weight:') }} <strong>{{ number_format($wr['total_weight'], 4) }}</strong>
                                        </p>
                                        <div class="result-bar-row">
                                            <div class="result-bar-label">
                                                <span class="fw-semibold text-success"><i class="ri-thumb-up-line me-1"></i>{{ __('For') }}</span>
                                                <span class="text-success fw-bold">{{ number_format($wr['for_weight'], 4) }} &nbsp;({{ $wForPct }}%)</span>
                                            </div>
                                            <div class="progress-thick"><div class="progress-bar bg-success" style="width:{{ $wForPct }}%"></div></div>
                                        </div>
                                        <div class="result-bar-row">
                                            <div class="result-bar-label">
                                                <span class="fw-semibold text-danger"><i class="ri-thumb-down-line me-1"></i>{{ __('Against') }}</span>
                                                <span class="text-danger fw-bold">{{ number_format($wr['against_weight'], 4) }} &nbsp;({{ $wAgainstPct }}%)</span>
                                            </div>
                                            <div class="progress-thick"><div class="progress-bar bg-danger" style="width:{{ $wAgainstPct }}%"></div></div>
                                        </div>
                                        <div class="result-bar-row">
                                            <div class="result-bar-label">
                                                <span class="fw-semibold text-secondary"><i class="ri-subtract-line me-1"></i>{{ __('Abstain') }}</span>
                                                <span class="text-secondary fw-bold">{{ number_format($wr['abstain_weight'], 4) }} &nbsp;({{ $wAbstainPct }}%)</span>
                                            </div>
                                            <div class="progress-thick"><div class="progress-bar bg-secondary" style="width:{{ $wAbstainPct }}%"></div></div>
                                        </div>
                                    </div>
                                    @endif
                                @else
                                    <div class="text-center text-muted py-4">
                                        <i class="ri-bar-chart-line fs-2 d-block mb-2"></i>
                                        {{ __('No votes have been cast yet.') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                    </div>

                    {{-- ── RIGHT: Vote / status panel ──────────────────────── --}}
                    <div class="col-lg-5">

                        @if($myVote)
                            {{-- Already voted --}}
                            @php
                                $voteLabels = [
                                    1 => ['label' => 'For',     'pill' => 'pill-for',     'icon' => 'ri-thumb-up-fill'],
                                    2 => ['label' => 'Against', 'pill' => 'pill-against', 'icon' => 'ri-thumb-down-fill'],
                                    3 => ['label' => 'Abstain', 'pill' => 'pill-abstain', 'icon' => 'ri-subtract-fill'],
                                ];
                                $vl = $voteLabels[$myVote->vote] ?? ['label' => 'Unknown', 'pill' => 'pill-abstain', 'icon' => 'ri-question-mark'];
                            @endphp
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center py-4">
                                    <div class="mb-3">
                                        <i class="ri-checkbox-circle-fill text-success" style="font-size:3rem;"></i>
                                    </div>
                                    <h5 class="mb-1">{{ __('Your vote is recorded') }}</h5>
                                    <p class="text-muted small mb-3">{{ __('Submitted') }} {{ $myVote->created_at->format('M d, Y \a\t H:i') }}</p>
                                    <div class="d-flex justify-content-center mb-3">
                                        <span class="my-vote-pill {{ $vl['pill'] }}">
                                            <i class="{{ $vl['icon'] }}"></i> {{ __($vl['label']) }}
                                        </span>
                                    </div>
                                    @if($myVote->comment)
                                        <div class="p-3 bg-light rounded-3 text-start">
                                            <small class="text-muted d-block mb-1">{{ __('Your comment') }}</small>
                                            <p class="mb-0 small">{{ $myVote->comment }}</p>
                                        </div>
                                    @endif
                                    @if($myVote->voting_weight)
                                        <p class="text-muted small mt-3 mb-0">
                                            <i class="ri-scales-line me-1"></i>
                                            {{ __('Voting weight applied:') }} <strong>{{ number_format($myVote->voting_weight, 4) }}</strong>
                                        </p>
                                    @endif
                                </div>
                            </div>

                        @elseif(isset($canVote) && !$canVote)
                            {{-- No voting rights --}}
                            <div class="card border-0 shadow-sm">
                                <div class="card-body vote-unavailable-box">
                                    <div class="unavailable-icon" style="color:#d97706;">
                                        <i class="ri-shield-cross-line"></i>
                                    </div>
                                    <h6 class="mb-1">{{ __('No Voting Rights') }}</h6>
                                    <p class="text-muted small mb-3">{{ __('Your share class does not carry voting rights on this resolution type.') }}</p>
                                    <a href="{{ route('shareholder.governance-rights') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="ri-shield-check-line me-1"></i>{{ __('View My Rights') }}
                                    </a>
                                </div>
                            </div>

                        @elseif(!$windowOpen)
                            {{-- Voting window not open --}}
                            <div class="card border-0 shadow-sm">
                                <div class="card-body vote-unavailable-box">
                                    @if($resolution->status == 0)
                                        <div class="unavailable-icon text-secondary"><i class="ri-draft-line"></i></div>
                                        <h6 class="mb-1">{{ __('Voting Not Started') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('This resolution is still in draft. Voting has not been opened yet.') }}</p>

                                    @elseif($isStatusOpen && !$hasOpenDate)
                                        <div class="unavailable-icon" style="color:#d97706;"><i class="ri-calendar-line"></i></div>
                                        <h6 class="mb-1">{{ __('No Voting Window Set') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('Voting dates have not been configured. Please contact the administrator.') }}</p>

                                    @elseif($isStatusOpen && $hasOpenDate && !$votingStarted)
                                        <div class="unavailable-icon" style="color:#0369a1;"><i class="ri-time-line"></i></div>
                                        <h6 class="mb-1">{{ __('Voting Not Yet Open') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('Voting opens on') }} <strong>{{ $resolution->voting_opens_at->format('M d, Y H:i') }}</strong></p>

                                    @elseif($isStatusOpen && $hasCloseDate && !$votingNotEnded)
                                        <div class="unavailable-icon" style="color:#6b7280;"><i class="ri-close-circle-line"></i></div>
                                        <h6 class="mb-1">{{ __('Voting Window Closed') }}</h6>
                                        <p class="text-muted small mb-0">
                                            {{ __('Voting closed on') }} <strong>{{ $resolution->voting_closes_at->format('M d, Y H:i') }}</strong>
                                        </p>

                                    @else
                                        <div class="unavailable-icon" style="color:#6b7280;"><i class="ri-lock-line"></i></div>
                                        <h6 class="mb-1">{{ __('Voting Closed') }}</h6>
                                        <p class="text-muted small mb-0">{{ __('This resolution is') }} <strong>{{ __($st['label']) }}</strong>. {{ __('Voting is no longer available.') }}</p>
                                    @endif
                                </div>
                            </div>

                        @else
                            {{-- Vote form --}}
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-transparent border-bottom-0 pb-0 pt-4">
                                    <h5 class="mb-0">
                                        <i class="ri-survey-line me-2 text-primary"></i>{{ __('Cast Your Vote') }}
                                    </h5>
                                    @if(isset($votingWeight) && $votingWeight > 0)
                                    <p class="text-muted small mt-1 mb-0">
                                        {{ __('Your vote carries a weight of') }}
                                        <strong class="text-primary">{{ number_format($votingWeight, 2) }}</strong>
                                    </p>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('shareholder.resolutions.vote', $resolution) }}" method="POST" id="voteForm">
                                        @csrf

                                        {{-- Vote option buttons --}}
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold mb-3">
                                                {{ __('Select Your Vote') }} <span class="text-danger">*</span>
                                            </label>
                                            <div class="row g-2">
                                                <div class="col-4">
                                                    <label class="vote-option vote-for w-100" for="voteFor">
                                                        <input type="radio" name="vote" id="voteFor" value="1"
                                                            {{ old('vote') == '1' ? 'checked' : '' }} required>
                                                        <div class="vote-check"><i class="ri-check-line"></i></div>
                                                        <span class="vote-icon">👍</span>
                                                        <span class="vote-label text-success">{{ __('For') }}</span>
                                                        <span class="vote-desc">{{ __('Support') }}</span>
                                                    </label>
                                                </div>
                                                <div class="col-4">
                                                    <label class="vote-option vote-against w-100" for="voteAgainst">
                                                        <input type="radio" name="vote" id="voteAgainst" value="2"
                                                            {{ old('vote') == '2' ? 'checked' : '' }}>
                                                        <div class="vote-check"><i class="ri-check-line"></i></div>
                                                        <span class="vote-icon">👎</span>
                                                        <span class="vote-label text-danger">{{ __('Against') }}</span>
                                                        <span class="vote-desc">{{ __('Oppose') }}</span>
                                                    </label>
                                                </div>
                                                <div class="col-4">
                                                    <label class="vote-option vote-abstain w-100" for="voteAbstain">
                                                        <input type="radio" name="vote" id="voteAbstain" value="3"
                                                            {{ old('vote') == '3' ? 'checked' : '' }}>
                                                        <div class="vote-check"><i class="ri-check-line"></i></div>
                                                        <span class="vote-icon">🤐</span>
                                                        <span class="vote-label text-secondary">{{ __('Abstain') }}</span>
                                                        <span class="vote-desc">{{ __('Neutral') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                            @error('vote')
                                                <div class="text-danger small mt-2"><i class="ri-error-warning-line me-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Comment --}}
                                        <div class="mb-4">
                                            <label for="voteComment" class="form-label fw-semibold">
                                                {{ __('Comment') }}
                                                <span class="text-muted fw-normal small">({{ __('optional') }})</span>
                                            </label>
                                            <textarea class="form-control @error('comment') is-invalid @enderror"
                                                id="voteComment" name="comment" rows="3"
                                                placeholder="{{ __('Share your reasoning or concerns (optional)...') }}">{{ old('comment') }}</textarea>
                                            @error('comment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="alert alert-warning py-2 small mb-4">
                                            <i class="ri-error-warning-line me-1"></i>
                                            <strong>{{ __('Note:') }}</strong>
                                            {{ __('Your vote is final and cannot be changed once submitted.') }}
                                        </div>

                                        <button type="button" class="btn btn-primary w-100 py-2" id="btnSubmitVote" disabled>
                                            <i class="ri-check-double-line me-1"></i>{{ __('Submit My Vote') }}
                                        </button>
                                        <p class="text-center text-muted small mt-2 mb-0" id="voteHint">
                                            {{ __('Select a vote option above to continue') }}
                                        </p>
                                    </form>
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
(function () {
    // Headcount / Weighted toggle
    var btnH = document.getElementById('btnHeadcount');
    var btnW = document.getElementById('btnWeighted');
    var divH = document.getElementById('resultsHeadcount');
    var divW = document.getElementById('resultsWeighted');
    if (btnH && btnW) {
        btnH.addEventListener('click', function () {
            divH.style.display = ''; divW.style.display = 'none';
            btnH.classList.add('active'); btnW.classList.remove('active');
        });
        btnW.addEventListener('click', function () {
            divH.style.display = 'none'; divW.style.display = '';
            btnW.classList.add('active'); btnH.classList.remove('active');
        });
    }

    // Vote option visual selection
    var radios    = document.querySelectorAll('input[name="vote"]');
    var submitBtn = document.getElementById('btnSubmitVote');
    var voteHint  = document.getElementById('voteHint');

    function updateVoteUI() {
        document.querySelectorAll('.vote-option').forEach(function (el) { el.classList.remove('selected'); });
        radios.forEach(function (r) {
            if (r.checked) { r.closest('.vote-option') && r.closest('.vote-option').classList.add('selected'); }
        });
        var anyChecked = Array.from(radios).some(function (r) { return r.checked; });
        if (submitBtn) submitBtn.disabled = !anyChecked;
        if (voteHint)  voteHint.style.display = anyChecked ? 'none' : '';
    }

    radios.forEach(function (r) { r.addEventListener('change', updateVoteUI); });
    updateVoteUI();

    // Confirmation before submit
    var form = document.getElementById('voteForm');
    if (submitBtn && form) {
        submitBtn.addEventListener('click', function () {
            var selected = Array.from(radios).find(function (r) { return r.checked; });
            if (!selected) return;
            var labelMap = { '1': '{{ __("For") }}', '2': '{{ __("Against") }}', '3': '{{ __("Abstain") }}' };
            var voteLabel = labelMap[selected.value] || selected.value;
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '{{ __("Confirm Your Vote") }}',
                    html: '{{ __("You are voting:") }} <strong>' + voteLabel + '</strong><br><span class="text-muted small">{{ __("This cannot be undone.") }}</span>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '{{ __("Yes, Submit Vote") }}',
                    cancelButtonText: '{{ __("Go Back") }}',
                    confirmButtonColor: '#3686FC',
                    reverseButtons: true,
                }).then(function (result) { if (result.isConfirmed) { form.submit(); } });
            } else {
                if (confirm('{{ __("Confirm your vote:") }} ' + voteLabel + '\n{{ __("This cannot be changed.") }}')) { form.submit(); }
            }
        });
    }

    // Countdown timer
    var cdEl = document.getElementById('votingCountdown');
    if (cdEl) {
        var deadline = new Date(cdEl.dataset.deadline);
        var days  = document.getElementById('cd-days');
        var hours = document.getElementById('cd-hours');
        var mins  = document.getElementById('cd-mins');
        var secs  = document.getElementById('cd-secs');
        function pad(n) { return n < 10 ? '0' + n : n; }
        function tick() {
            var diff = deadline - new Date();
            if (diff <= 0) {
                cdEl.innerHTML = '<span class="text-danger fw-semibold small">{{ __("Voting window has closed") }}</span>';
                return;
            }
            var d = Math.floor(diff / 86400000);
            var h = Math.floor((diff % 86400000) / 3600000);
            var m = Math.floor((diff % 3600000) / 60000);
            var s = Math.floor((diff % 60000) / 1000);
            if (days)  days.textContent  = pad(d);
            if (hours) hours.textContent = pad(h);
            if (mins)  mins.textContent  = pad(m);
            if (secs)  secs.textContent  = pad(s);
        }
        tick();
        setInterval(tick, 1000);
    }
})();
</script>
@endpush
