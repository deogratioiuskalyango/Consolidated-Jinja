@extends('shareholder.layouts.app')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                    <div>
                        <h2 class="mb-1">{{ __('Resolutions & Voting') }}</h2>
                        <p class="text-muted small mb-0">{{ __('Review and vote on company resolutions') }}</p>
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
                @endphp

                @forelse($resolutions as $resolution)
                    @php
                        $st           = $statusRingMap[$resolution->status] ?? ['label' => 'Unknown', 'ring' => 'status-ring-draft', 'icon' => 'ri-question-line'];
                        $totalVotes   = $resolution->votes->count();
                        $forVotes     = $resolution->votes->where('vote', 1)->count();
                        $againstVotes = $resolution->votes->where('vote', 2)->count();
                        $forPct       = $totalVotes > 0 ? round(($forVotes / $totalVotes) * 100, 1) : 0;
                        $againstPct   = $totalVotes > 0 ? round(($againstVotes / $totalVotes) * 100, 1) : 0;
                        $myVoteOnThis = $resolution->votes->where('shareholder_id', $shareholder->id ?? null)->first();

                        $now          = now();
                        $isStatusOpen = (int) $resolution->status === 1;
                        $windowOpen   = $isStatusOpen
                                        && $resolution->voting_opens_at !== null
                                        && $resolution->voting_closes_at !== null
                                        && $now->gte($resolution->voting_opens_at)
                                        && $now->lte($resolution->voting_closes_at);
                    @endphp
                    <div class="resolution-card mb-3">
                        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                            {{-- Title & meta --}}
                            <div class="flex-grow-1" style="min-width:200px;">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="resolution-status-ring {{ $st['ring'] }} py-1" style="font-size:.75rem;">
                                        <i class="{{ $st['icon'] }}"></i> {{ __($st['label']) }}
                                    </span>
                                    @if($resolution->type)
                                        <span class="badge bg-secondary" style="font-size:.7rem;">{{ $resolution->type_label ?? $resolution->type }}</span>
                                    @endif
                                    @if($myVoteOnThis)
                                        <span class="badge bg-primary" style="font-size:.7rem;"><i class="ri-check-line me-1"></i>{{ __('Voted') }}</span>
                                    @elseif($windowOpen)
                                        <span class="badge bg-warning text-dark" style="font-size:.7rem;"><i class="ri-time-line me-1"></i>{{ __('Action needed') }}</span>
                                    @endif
                                </div>
                                <h6 class="mb-1 fw-semibold">{{ $resolution->title }}</h6>
                                @if($resolution->description)
                                    <p class="text-muted small mb-2">{{ Str::limit($resolution->description, 100) }}</p>
                                @endif
                                <div class="d-flex flex-wrap gap-3 text-muted" style="font-size:.78rem;">
                                    @if($resolution->voting_closes_at)
                                    <span>
                                        <i class="ri-calendar-close-line me-1"></i>
                                        {{ __('Closes:') }} {{ $resolution->voting_closes_at->format('M d, Y') }}
                                    </span>
                                    @endif
                                    <span>
                                        <i class="ri-group-line me-1"></i>
                                        {{ $totalVotes }} {{ __('vote(s)') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Results bar + action button --}}
                            <div class="d-flex flex-column align-items-end gap-2" style="min-width:140px;">
                                @if($resolution->status != 0 && $totalVotes > 0)
                                    <div style="width:140px;">
                                        <div class="d-flex justify-content-between mb-1" style="font-size:.72rem;">
                                            <span class="text-success fw-semibold">{{ __('For') }} {{ $forPct }}%</span>
                                            <span class="text-danger fw-semibold">{{ __('Ag.') }} {{ $againstPct }}%</span>
                                        </div>
                                        <div class="progress" style="height:8px;border-radius:999px;">
                                            <div class="progress-bar bg-success" style="width:{{ $forPct }}%"></div>
                                            <div class="progress-bar bg-danger" style="width:{{ $againstPct }}%"></div>
                                        </div>
                                    </div>
                                @endif
                                <a href="{{ route('shareholder.resolutions.show', $resolution) }}"
                                   class="btn btn-sm {{ $windowOpen && !$myVoteOnThis ? 'btn-primary' : 'btn-outline-primary' }}">
                                    @if($windowOpen && !$myVoteOnThis)
                                        <i class="ri-survey-line me-1"></i>{{ __('Vote Now') }}
                                    @else
                                        <i class="ri-eye-line me-1"></i>{{ __('View') }}
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5">
                        <i class="ri-list-check-2 d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                        <h6>{{ __('No resolutions found') }}</h6>
                        <p class="small mb-0">{{ __('Resolutions will appear here once they are published.') }}</p>
                    </div>
                @endforelse

                <div class="mt-4">
                    {{ $resolutions->links() }}
                </div>

            </div>
        </div>
    </div>
</div>
@include('components.share-modal')
@endsection
