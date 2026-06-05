@extends('shareholder.layouts.app')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                    <div>
                        <h2 class="mb-1">{{ __('Financial Approvals') }}</h2>
                        <p class="text-muted small mb-0">{{ __('Review and act on pending financial approval requests') }}</p>
                    </div>
                </div>

                @php
                    $statusRingMap = [
                        0 => ['label' => 'Pending',  'ring' => 'status-ring-draft',  'icon' => 'ri-time-line'],
                        1 => ['label' => 'Approved', 'ring' => 'status-ring-passed', 'icon' => 'ri-check-double-line'],
                        2 => ['label' => 'Rejected', 'ring' => 'status-ring-failed', 'icon' => 'ri-close-line'],
                        3 => ['label' => 'Expired',  'ring' => 'status-ring-closed', 'icon' => 'ri-calendar-close-line'],
                    ];
                @endphp

                {{-- Desktop table --}}
                <div class="res-table-wrap">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Title') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Threshold') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Deadline') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($approvals as $approval)
                                            @php
                                                $st         = $statusRingMap[$approval->status] ?? ['label' => 'Unknown', 'ring' => 'status-ring-draft', 'icon' => 'ri-question-line'];
                                                $myAction   = $approval->actions->first();
                                                $isPending  = (int) $approval->status === 0;
                                                $notExpired = !$approval->deadline || now()->lte($approval->deadline);
                                                $needsAction = $isPending && !$myAction && $notExpired && $canApprove;
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $approval->title }}</strong>
                                                    @if($needsAction)
                                                        <span class="badge bg-warning text-dark ms-1" style="font-size:.68rem;">
                                                            <i class="ri-time-line me-1"></i>{{ __('Action needed') }}
                                                        </span>
                                                    @elseif($myAction)
                                                        <span class="badge bg-primary ms-1" style="font-size:.68rem;">
                                                            <i class="ri-check-line me-1"></i>{{ (int)$myAction->action === 1 ? __('Approved') : __('Rejected') }}
                                                        </span>
                                                    @endif
                                                    @if($approval->description)
                                                        <p class="text-muted small mb-0">{{ Str::limit($approval->description, 80) }}</p>
                                                    @endif
                                                </td>
                                                <td class="fw-semibold">
                                                    {{ $approval->currency ?? '' }}
                                                    {{ number_format($approval->amount, 2) }}
                                                </td>
                                                <td>{{ isset($approval->approval_threshold) ? $approval->approval_threshold . '%' : '-' }}</td>
                                                <td>
                                                    <span class="resolution-status-ring {{ $st['ring'] }}" style="font-size:.72rem; padding:4px 10px;">
                                                        <i class="{{ $st['icon'] }}"></i> {{ __($st['label']) }}
                                                    </span>
                                                </td>
                                                <td class="text-muted small">
                                                    {{ $approval->deadline ? $approval->deadline->format('M d, Y') : '-' }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('shareholder.financial-approvals.show', $approval) }}"
                                                       class="btn btn-sm {{ $needsAction ? 'btn-primary' : 'btn-outline-primary' }}">
                                                        @if($needsAction)
                                                            <i class="ri-check-double-line me-1"></i>{{ __('Review') }}
                                                        @else
                                                            <i class="ri-eye-line me-1"></i>{{ __('View') }}
                                                        @endif
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-5">
                                                    <i class="ri-money-dollar-box-line d-block mb-2 fs-2 opacity-50"></i>
                                                    {{ __('No financial approvals found.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mobile cards --}}
                <div class="res-cards-wrap">
                    @forelse($approvals as $approval)
                        @php
                            $st         = $statusRingMap[$approval->status] ?? ['label' => 'Unknown', 'ring' => 'status-ring-draft', 'icon' => 'ri-question-line'];
                            $myAction   = $approval->actions->first();
                            $isPending  = (int) $approval->status === 0;
                            $notExpired = !$approval->deadline || now()->lte($approval->deadline);
                            $needsAction = $isPending && !$myAction && $notExpired && $canApprove;
                        @endphp
                        <div class="resolution-card mb-3">
                            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                                <div class="flex-grow-1" style="min-width:200px;">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <span class="resolution-status-ring {{ $st['ring'] }}" style="font-size:.72rem;">
                                            <i class="{{ $st['icon'] }}"></i> {{ __($st['label']) }}
                                        </span>
                                        @if($needsAction)
                                            <span class="badge bg-warning text-dark" style="font-size:.68rem;">
                                                <i class="ri-time-line me-1"></i>{{ __('Action needed') }}
                                            </span>
                                        @elseif($myAction)
                                            <span class="badge bg-primary" style="font-size:.68rem;">
                                                <i class="ri-check-line me-1"></i>{{ (int)$myAction->action === 1 ? __('Approved') : __('Rejected') }}
                                            </span>
                                        @endif
                                    </div>
                                    <h6 class="mb-1 fw-semibold">{{ $approval->title }}</h6>
                                    @if($approval->description)
                                        <p class="text-muted small mb-2">{{ Str::limit($approval->description, 80) }}</p>
                                    @endif
                                    <div class="d-flex flex-wrap gap-3 text-muted" style="font-size:.78rem;">
                                        <span>
                                            <i class="ri-money-dollar-box-line me-1"></i>
                                            <strong>{{ $approval->currency ?? '' }} {{ number_format($approval->amount, 2) }}</strong>
                                        </span>
                                        @if($approval->deadline)
                                        <span>
                                            <i class="ri-calendar-close-line me-1"></i>
                                            {{ __('Due:') }} {{ $approval->deadline->format('M d, Y') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('shareholder.financial-approvals.show', $approval) }}"
                                   class="btn btn-sm {{ $needsAction ? 'btn-primary' : 'btn-outline-primary' }}">
                                    @if($needsAction)
                                        <i class="ri-check-double-line me-1"></i>{{ __('Review') }}
                                    @else
                                        <i class="ri-eye-line me-1"></i>{{ __('View') }}
                                    @endif
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="ri-money-dollar-box-line d-block mb-3" style="font-size:3rem;opacity:.4;"></i>
                            <h6>{{ __('No financial approvals found') }}</h6>
                            <p class="small mb-0">{{ __('Approval requests will appear here once they are created.') }}</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $approvals->links() }}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
