@extends('shareholder.layouts.app')
@section('content')
@php $navGovernanceRightsActiveClass = 'active'; @endphp
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page Title --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex flex-column flex-sm-row align-items-sm-center justify-content-between g-20">
                            <div class="page-title-left">
                                <h2 class="mb-sm-0">{{ __('My Governance Rights') }}</h2>
                                <p class="text-muted mb-0 mt-1">
                                    {{ __('Rights and permissions tied to your share class.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Share Class Banner --}}
                @if(!empty($rights['share_class']))
                @php $sc = $rights['share_class']; @endphp
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div class="flex-shrink-0">
                                        {!! $rights['class_code_badge'] ?? '<span class="badge bg-secondary fs-5 px-3 py-2">—</span>' !!}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="mb-1">{{ $sc['name'] ?? '—' }}</h4>
                                        <div class="d-flex gap-3 flex-wrap text-muted small">
                                            <span>
                                                <i class="ri-scales-line me-1"></i>
                                                {{ __('Voting Multiplier:') }}
                                                <strong class="text-dark">{{ $sc['voting_multiplier'] ?? '0' }}×</strong>
                                            </span>
                                            <span>
                                                <i class="ri-trophy-line me-1"></i>
                                                {{ __('Dividend Priority:') }}
                                                <strong class="text-dark">{{ $sc['dividend_priority'] ?? '—' }}</strong>
                                            </span>
                                            <span>
                                                <i class="ri-shield-star-line me-1"></i>
                                                {{ __('Governance Level:') }}
                                                <strong class="text-dark">{{ $rights['governance_level_label'] ?? '—' }}</strong>
                                            </span>
                                            @if(!empty($sc['max_ownership_cap']))
                                            <span>
                                                <i class="ri-percent-line me-1"></i>
                                                {{ __('Max Ownership Cap:') }}
                                                <strong class="text-dark">{{ $sc['max_ownership_cap'] }}%</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">{{ __('Your effective voting weight') }}</small>
                                        <span class="fs-4 fw-bold text-primary">
                                            {{ number_format($rights['effective_voting_weight'] ?? 0, 4) }}
                                        </span>
                                    </div>
                                </div>

                                @if(!empty($sc['transfer_restrictions_label']))
                                <div class="mt-3 pt-3 border-top">
                                    <small class="text-muted">
                                        <i class="ri-exchange-line me-1"></i>
                                        {{ __('Transfer Restrictions:') }}
                                        <strong>{{ $sc['transfer_restrictions_label'] }}</strong>
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row mt-4">
                    {{-- Permissions Matrix --}}
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">
                                    <i class="ri-key-2-line me-2 text-primary"></i>{{ __('Permission Matrix') }}
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('Permission') }}</th>
                                                <th class="text-center">{{ __('Status') }}</th>
                                                <th>{{ __('Limit') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $permLabels = [
                                                    'vote'                      => ['label' => 'Vote on Resolutions',         'icon' => 'ri-list-check-2'],
                                                    'approve_expenses'          => ['label' => 'Approve Expenses',             'icon' => 'ri-money-dollar-box-line'],
                                                    'view_financial_reports'    => ['label' => 'View Financial Reports',       'icon' => 'ri-bar-chart-line'],
                                                    'approve_acquisitions'      => ['label' => 'Approve Acquisitions',         'icon' => 'ri-building-2-line'],
                                                    'appoint_directors'         => ['label' => 'Appoint Directors',            'icon' => 'ri-user-star-line'],
                                                    'access_audit_logs'         => ['label' => 'Access Audit Logs',            'icon' => 'ri-file-list-3-line'],
                                                    'receive_dividends'         => ['label' => 'Receive Dividends',            'icon' => 'ri-coins-line'],
                                                    'view_board_reports'        => ['label' => 'View Board Reports',           'icon' => 'ri-file-text-line'],
                                                    'create_resolutions'        => ['label' => 'Create Resolutions',           'icon' => 'ri-add-circle-line'],
                                                    'veto_resolutions'          => ['label' => 'Veto Resolutions',             'icon' => 'ri-forbid-line'],
                                                    'trigger_emergency_vote'    => ['label' => 'Trigger Emergency Vote',       'icon' => 'ri-alarm-warning-line'],
                                                    'approve_share_transfers'   => ['label' => 'Approve Share Transfers',      'icon' => 'ri-exchange-line'],
                                                    'onboard_shareholders'      => ['label' => 'Onboard Shareholders',         'icon' => 'ri-user-add-line'],
                                                    'view_analytics'            => ['label' => 'View Analytics',               'icon' => 'ri-line-chart-line'],
                                                    'access_confidential_reports'=> ['label' => 'Access Confidential Reports', 'icon' => 'ri-lock-line'],
                                                    'access_risk_reports'       => ['label' => 'Access Risk Reports',          'icon' => 'ri-radar-line'],
                                                    'vote_acquisitions'         => ['label' => 'Vote on Acquisitions',         'icon' => 'ri-building-4-line'],
                                                    'vote_dividends'            => ['label' => 'Vote on Dividends',            'icon' => 'ri-hand-coin-line'],
                                                    'vote_liquidation'          => ['label' => 'Vote on Liquidation',          'icon' => 'ri-delete-bin-line'],
                                                    'view_esop_reports'         => ['label' => 'View ESOP Reports',            'icon' => 'ri-team-line'],
                                                    'view_investment_reports'   => ['label' => 'View Investment Reports',      'icon' => 'ri-funds-line'],
                                                ];
                                                $permissions = $rights['permissions'] ?? [];
                                            @endphp
                                            @foreach($permLabels as $key => $meta)
                                            @php
                                                $allowed = !empty($permissions[$key]);
                                                $limit   = $rights['permission_limits'][$key] ?? null;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <i class="{{ $meta['icon'] }} me-2 text-muted"></i>
                                                    {{ __($meta['label']) }}
                                                </td>
                                                <td class="text-center">
                                                    @if($allowed)
                                                        <span class="badge bg-success">
                                                            <i class="ri-check-line me-1"></i>{{ __('Granted') }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="ri-close-line me-1"></i>{{ __('Denied') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($limit !== null)
                                                        <span class="text-muted small">
                                                            {{ __('Up to') }} {{ number_format((float)$limit, 2) }}
                                                        </span>
                                                    @elseif($allowed)
                                                        <span class="text-muted small">{{ __('Unlimited') }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right column: Thresholds + Voting Stats + Vesting --}}
                    <div class="col-lg-5">

                        {{-- Voting Summary --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">
                                    <i class="ri-scales-line me-2 text-primary"></i>{{ __('Voting Summary') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 text-center">
                                    <div class="col-6">
                                        <div class="border rounded p-3">
                                            <div class="fs-4 fw-bold text-primary">{{ number_format($rights['total_shares'] ?? 0, 4) }}</div>
                                            <small class="text-muted">{{ __('Total Shares') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-3">
                                            <div class="fs-4 fw-bold text-success">{{ number_format($rights['effective_voting_weight'] ?? 0, 4) }}</div>
                                            <small class="text-muted">{{ __('Voting Weight') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-3">
                                            <div class="fs-4 fw-bold text-info">{{ number_format($rights['ownership_percentage'] ?? 0, 2) }}%</div>
                                            <small class="text-muted">{{ __('Ownership') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-3">
                                            @php
                                                $vmul = $rights['share_class']['voting_multiplier'] ?? 0;
                                            @endphp
                                            <div class="fs-4 fw-bold {{ $vmul > 0 ? 'text-warning' : 'text-secondary' }}">{{ $vmul }}×</div>
                                            <small class="text-muted">{{ __('Vote Multiplier') }}</small>
                                        </div>
                                    </div>
                                </div>

                                @if(isset($rights['can_vote']) && !$rights['can_vote'])
                                <div class="alert alert-warning py-2 mt-3 mb-0 small">
                                    <i class="ri-information-line me-1"></i>
                                    {{ __('Your share class does not carry voting rights.') }}
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Approval Thresholds --}}
                        @if(isset($thresholds) && $thresholds->count())
                        @php
                            $scCode = $rights['share_class']['class_code'] ?? null;
                            $canApproveExpenses = $rights['permissions']['approve_expenses'] ?? false;
                        @endphp
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">
                                    <i class="ri-money-dollar-box-line me-2 text-primary"></i>{{ __('Approval Thresholds') }}
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @foreach($thresholds as $threshold)
                                    @php
                                        $eligible = $canApproveExpenses
                                            && (empty($threshold->required_approver_classes)
                                                || in_array($scCode, $threshold->required_approver_classes));
                                    @endphp
                                    <div class="list-group-item d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold small">{{ $threshold->name }}</div>
                                            <div class="text-muted small">
                                                {{ number_format($threshold->min_amount, 0) }}
                                                @if($threshold->max_amount)
                                                    – {{ number_format($threshold->max_amount, 0) }}
                                                @else
                                                    +
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @if($eligible)
                                                <span class="badge bg-success">{{ __('Eligible') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Not Eligible') }}</span>
                                            @endif
                                            @if($threshold->requires_quorum)
                                            <div class="text-muted small mt-1">
                                                {{ $threshold->quorum_percentage }}% {{ __('quorum') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Vesting Schedule --}}
                        @if(!empty($rights['vesting_schedule']))
                        @php $vs = $rights['vesting_schedule']; @endphp
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">
                                    <i class="ri-timer-line me-2 text-warning"></i>{{ __('Vesting Schedule') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                {{-- Progress --}}
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span>{{ __('Vesting Progress') }}</span>
                                        <span>{{ number_format($vs['progress_percent'] ?? 0, 1) }}%</span>
                                    </div>
                                    <div class="progress" style="height:8px;">
                                        <div class="progress-bar bg-warning" style="width: {{ $vs['progress_percent'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="row g-2 text-center">
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="fw-bold small">{{ number_format($vs['total_granted'] ?? 0, 0) }}</div>
                                            <div class="text-muted" style="font-size:.7rem;">{{ __('Granted') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="fw-bold small text-success">{{ number_format($vs['shares_vested'] ?? 0, 0) }}</div>
                                            <div class="text-muted" style="font-size:.7rem;">{{ __('Vested') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2">
                                            <div class="fw-bold small text-warning">{{ number_format($vs['shares_available'] ?? 0, 0) }}</div>
                                            <div class="text-muted" style="font-size:.7rem;">{{ __('Available') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 small text-muted">
                                    @if(!empty($vs['cliff_date']))
                                        <div><i class="ri-calendar-event-line me-1"></i>{{ __('Cliff:') }} {{ $vs['cliff_date'] }}</div>
                                    @endif
                                    <div><i class="ri-calendar-check-line me-1"></i>{{ __('Vesting End:') }} {{ $vs['vesting_end_date'] ?? '—' }}</div>
                                    @if(!empty($vs['employment_linked']))
                                        <div class="text-warning mt-1"><i class="ri-alert-line me-1"></i>{{ __('Employment-linked — forfeited on exit.') }}</div>
                                    @endif
                                </div>
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
