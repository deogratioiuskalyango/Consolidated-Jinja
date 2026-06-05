@extends('shareholder.layouts.app')
@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="page-content-wrapper bg-white p-30 radius-20">

                {{-- Page Title --}}
                <div class="row mb-1">
                    <div class="col-12">
                        <h2 class="mb-1" style="font-size:clamp(1.2rem,4vw,1.75rem);">{{ __('Shareholder Dashboard') }}</h2>
                        <p class="text-muted mb-0" style="font-size:clamp(.75rem,2.5vw,.875rem);line-height:1.5;">
                            {{ __('Welcome back,') }}
                            <strong>{{ $shareholder->full_name ?? auth()->user()->name }}</strong>
                            <span class="d-none d-sm-inline">&mdash;
                                {{ __('Shares:') }} {{ number_format($totalShares, 4) }}
                                ({{ number_format($ownershipPercent, 2) }}% {{ __('ownership') }})
                            </span>
                        </p>
                        {{-- Shares detail on its own line for xs --}}
                        <p class="text-muted mb-0 d-sm-none" style="font-size:.73rem;">
                            {{ number_format($totalShares, 4) }} {{ __('shares') }}
                            &bull; {{ number_format($ownershipPercent, 2) }}% {{ __('ownership') }}
                        </p>
                    </div>
                </div>

                {{-- Stats Row --}}
                <div class="sh-stat-grid mt-3">
                    <div class="sh-stat-card">
                        <div class="sh-stat-icon" style="background:#3b82f6;">
                            <i class="ri-stock-line"></i>
                        </div>
                        <div class="sh-stat-body">
                            <div class="sh-stat-label">{{ __('Total Shares') }}</div>
                            <div class="sh-stat-value">{{ number_format($totalShares, 0) }}</div>
                            @if(!empty($rights['share_class']['name']))
                                <div class="sh-stat-sub">{{ $rights['share_class']['name'] }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="sh-stat-card">
                        <div class="sh-stat-icon" style="background:#10b981;">
                            <i class="ri-pie-chart-line"></i>
                        </div>
                        <div class="sh-stat-body">
                            <div class="sh-stat-label">{{ __('Ownership') }}</div>
                            <div class="sh-stat-value">{{ number_format($ownershipPercent, 2) }}<span class="sh-stat-unit">%</span></div>
                        </div>
                    </div>
                    <div class="sh-stat-card">
                        <div class="sh-stat-icon" style="background:#f59e0b;">
                            <i class="ri-survey-line"></i>
                        </div>
                        <div class="sh-stat-body">
                            <div class="sh-stat-label">{{ __('Open Votes') }}</div>
                            <div class="sh-stat-value">{{ $openResolutions->count() }}</div>
                        </div>
                    </div>
                    <div class="sh-stat-card">
                        <div class="sh-stat-icon" style="background:#ef4444;">
                            <i class="ri-checkbox-circle-line"></i>
                        </div>
                        <div class="sh-stat-body">
                            <div class="sh-stat-label">{{ __('Pending Approvals') }}</div>
                            <div class="sh-stat-value">{{ $pendingApprovals->count() }}</div>
                        </div>
                    </div>
                </div>

                {{-- Governance Rights Quick-view + Vesting (if applicable) --}}
                @if(!empty($rights))
                <div class="row mt-4">
                    {{-- Governance Rights Summary --}}
                    <div class="{{ !empty($rights['vesting_schedule']) ? 'col-lg-8' : 'col-12' }}">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">
                                    <i class="ri-shield-check-line me-2 text-primary"></i>{{ __('Your Governance Rights') }}
                                </h5>
                                <a href="{{ route('shareholder.governance-rights') }}" class="btn btn-sm btn-outline-primary">
                                    {{ __('Full Detail') }}
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    @php
                                        $quickPerms = [
                                            'vote'                   => ['label' => 'Voting',            'icon' => 'ri-list-check-2',           'color' => 'primary'],
                                            'approve_expenses'       => ['label' => 'Expense Approval',  'icon' => 'ri-money-dollar-box-line',   'color' => 'success'],
                                            'receive_dividends'      => ['label' => 'Dividends',         'icon' => 'ri-coins-line',             'color' => 'warning'],
                                            'create_resolutions'     => ['label' => 'Create Resolutions','icon' => 'ri-add-circle-line',        'color' => 'info'],
                                            'veto_resolutions'       => ['label' => 'Veto Power',        'icon' => 'ri-forbid-line',            'color' => 'danger'],
                                            'view_financial_reports' => ['label' => 'Financial Reports', 'icon' => 'ri-bar-chart-line',         'color' => 'secondary'],
                                        ];
                                        $perms = $rights['permissions'] ?? [];
                                    @endphp
                                    @foreach($quickPerms as $key => $meta)
                                    <div class="col-md-4 col-6">
                                        <div class="sh-perm-chip {{ !empty($perms[$key]) ? 'sh-perm-granted' : 'sh-perm-denied' }}">
                                            <span class="sh-perm-icon text-{{ !empty($perms[$key]) ? $meta['color'] : 'secondary' }}">
                                                <i class="{{ $meta['icon'] }}"></i>
                                            </span>
                                            <div class="sh-perm-text">
                                                <span class="sh-perm-name">{{ __($meta['label']) }}</span>
                                                <span class="sh-perm-status {{ !empty($perms[$key]) ? 'text-success' : 'text-muted' }}">
                                                    {{ !empty($perms[$key]) ? __('Granted') : __('Not granted') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                {{-- Voting weight summary row --}}
                                @if(!empty($rights['effective_voting_weight']))
                                <div class="mt-3 pt-3 border-top d-flex align-items-center gap-3 flex-wrap">
                                    <small class="text-muted">
                                        <i class="ri-scales-line me-1"></i>
                                        {{ __('Effective Voting Weight:') }}
                                        <strong class="text-body">{{ number_format($rights['effective_voting_weight'], 4) }}</strong>
                                    </small>
                                    @if(!empty($rights['share_class']['voting_multiplier']))
                                    <small class="text-muted">
                                        {{ __('Multiplier:') }} <strong class="text-body">{{ $rights['share_class']['voting_multiplier'] }}×</strong>
                                    </small>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Vesting Schedule (Class E ESOP only) --}}
                    @if(!empty($rights['vesting_schedule']))
                    @php $vs = $rights['vesting_schedule']; @endphp
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">
                                    <i class="ri-timer-line me-2 text-warning"></i>{{ __('Vesting Schedule') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span>{{ __('Vested') }}</span>
                                        <span class="fw-semibold">{{ number_format($vs['progress_percent'] ?? 0, 1) }}%</span>
                                    </div>
                                    <div class="progress" style="height:8px;">
                                        <div class="progress-bar bg-warning" style="width: {{ $vs['progress_percent'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="row g-2 text-center">
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <div class="fw-bold text-success small">{{ number_format($vs['shares_vested'] ?? 0, 0) }}</div>
                                            <div class="text-muted" style="font-size:.7rem;">{{ __('Vested') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <div class="fw-bold text-warning small">{{ number_format($vs['shares_available'] ?? 0, 0) }}</div>
                                            <div class="text-muted" style="font-size:.7rem;">{{ __('Available') }}</div>
                                        </div>
                                    </div>
                                </div>
                                @if(!empty($vs['vesting_end_date']))
                                <p class="text-muted small mb-0 mt-3">
                                    <i class="ri-calendar-check-line me-1"></i>
                                    {{ __('Ends:') }} {{ $vs['vesting_end_date'] }}
                                </p>
                                @endif
                                @if(!empty($vs['employment_linked']))
                                <p class="text-warning small mb-0 mt-1">
                                    <i class="ri-alert-line me-1"></i>{{ __('Employment-linked') }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Open Resolutions + Upcoming Meetings --}}
                <div class="row mt-4 g-3">
                    <div class="col-lg-6 col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">{{ __('Open Resolutions') }}</h5>
                                <a href="{{ route('shareholder.resolutions.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Type') }}</th>
                                                <th>{{ __('Deadline') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($openResolutions as $resolution)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('shareholder.resolutions.show', $resolution) }}">
                                                            {{ $resolution->title }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $resolution->type ?? '-' }}</td>
                                                    <td>{{ $resolution->voting_closes_at ? $resolution->voting_closes_at->format('M d, Y') : '-' }}</td>
                                                    <td><span class="badge bg-success">{{ __('Open') }}</span></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">{{ __('No open resolutions.') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">{{ __('Upcoming Meetings') }}</h5>
                                <a href="{{ route('shareholder.meetings.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('Title') }}</th>
                                                <th>{{ __('Type') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Location') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($upcomingMeetings as $meeting)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('shareholder.meetings.show', $meeting) }}">
                                                            {{ $meeting->title }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $typeLabels = [1 => 'AGM', 2 => 'Board', 3 => 'EGM', 4 => 'Other'];
                                                        @endphp
                                                        {{ $typeLabels[$meeting->meeting_type] ?? '-' }}
                                                    </td>
                                                    <td>{{ $meeting->scheduled_at ? $meeting->scheduled_at->format('M d, Y') : '-' }}</td>
                                                    <td>{{ $meeting->location ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">{{ __('No upcoming meetings.') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Dividends --}}
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">{{ __('Recent Dividends') }}</h5>
                                <a href="{{ route('shareholder.dividends.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('Period') }}</th>
                                                <th>{{ __('Declared Date') }}</th>
                                                <th>{{ __('Per Share') }}</th>
                                                <th>{{ __('Your Shares') }}</th>
                                                <th>{{ __('Your Payout') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentDividends as $dividend)
                                                @php
                                                    $payout = $dividend->per_share_amount * $totalShares;
                                                    $statusMap = [DIVIDEND_STATUS_DECLARED => ['label' => 'Declared', 'class' => 'bg-info'],
                                                                  DIVIDEND_STATUS_PAID     => ['label' => 'Paid',     'class' => 'bg-success'],
                                                                  DIVIDEND_STATUS_CANCELLED=> ['label' => 'Cancelled','class' => 'bg-danger']];
                                                    $st = $statusMap[$dividend->status] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                                                @endphp
                                                <tr>
                                                    <td>{{ $dividend->title }}</td>
                                                    <td>{{ $dividend->declaration_date ? $dividend->declaration_date->format('M d, Y') : '-' }}</td>
                                                    <td>{{ number_format($dividend->per_share_amount, 4) }}</td>
                                                    <td>{{ number_format($totalShares, 4) }}</td>
                                                    <td>{{ number_format($payout, 2) }}</td>
                                                    <td><span class="badge {{ $st['class'] }}">{{ __($st['label']) }}</span></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-3">{{ __('No recent dividends.') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shareholder Analytics Charts --}}
                <div class="row mt-4 mb-4 g-3">
                    <div class="col-lg-7 sh-chart-col">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                    <h6 class="mb-0"><i class="ri-line-chart-line me-2 text-primary"></i>{{ __('Company Revenue') }} — {{ now()->year }}</h6>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ __('Monthly') }}</span>
                                </div>
                                <div id="shareholderRevenueChart"></div>
                                <div class="d-flex flex-wrap justify-content-between gap-2 mt-2 text-muted small">
                                    <span>{{ __('Annual Total:') }} <strong class="text-body">UGX {{ number_format($totalRevenueThisYear ?? 0, 0) }}</strong></span>
                                    <span>{{ __('Your Share:') }} <strong class="text-primary">{{ $ownershipPercent ?? 0 }}%</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 sh-chart-col">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                    <h6 class="mb-0"><i class="ri-coins-line me-2 text-warning"></i>{{ __('Dividends') }} — {{ now()->year }}</h6>
                                    <span class="badge bg-warning bg-opacity-10 text-warning">{{ __('Total: UGX') }} {{ number_format($totalDividendsThisYear ?? 0, 0) }}</span>
                                </div>
                                <div id="shareholderDividendChart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Laws & Guidelines ──────────────────────────────────────────── --}}
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-scales-3-line text-primary fs-5"></i>
                                    <h5 class="mb-0">{{ __('Laws, Rights & Guidelines') }}</h5>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ __('Jinja Consolidated Properties') }}</span>
                            </div>
                            <div class="card-body">

                                {{-- Tab navigation --}}
                                <ul class="nav nav-pills mb-3 gap-1 flex-wrap" id="lawsTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="tab-rights" data-bs-toggle="pill" data-bs-target="#pane-rights" type="button">
                                            <i class="ri-shield-check-line me-1"></i>{{ __('Your Rights') }}
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="tab-obligations" data-bs-toggle="pill" data-bs-target="#pane-obligations" type="button">
                                            <i class="ri-file-list-3-line me-1"></i>{{ __('Your Obligations') }}
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="tab-laws" data-bs-toggle="pill" data-bs-target="#pane-laws" type="button">
                                            <i class="ri-government-line me-1"></i>{{ __('Applicable Laws') }}
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="tab-conduct" data-bs-toggle="pill" data-bs-target="#pane-conduct" type="button">
                                            <i class="ri-user-star-line me-1"></i>{{ __('Code of Conduct') }}
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="tab-penalties" data-bs-toggle="pill" data-bs-target="#pane-penalties" type="button">
                                            <i class="ri-alert-line me-1"></i>{{ __('Penalties') }}
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content" id="lawsTabContent">

                                    {{-- Shareholder Rights --}}
                                    <div class="tab-pane fade show active" id="pane-rights" role="tabpanel">
                                        <div class="alert alert-primary py-2 mb-3">
                                            <i class="ri-information-line me-1"></i>
                                            {{ __('As a shareholder of Jinja Consolidated Properties, you are entitled to the following rights under the Companies Act, 2012 and the company governance framework.') }}
                                        </div>
                                        <div class="row g-3">
                                            @php
                                                $rights_list = [
                                                    ['icon' => 'ri-checkbox-circle-line', 'color' => 'success',
                                                     'title' => __('Voting Rights'),
                                                     'desc'  => __('Vote on company matters including resolutions, appointment or removal of directors, and approval of strategic decisions at AGMs and EGMs.')],
                                                    ['icon' => 'ri-money-dollar-circle-line', 'color' => 'success',
                                                     'title' => __('Dividend Entitlement'),
                                                     'desc'  => __('Receive dividends where declared by the Board in proportion to your shareholding.')],
                                                    ['icon' => 'ri-file-chart-line', 'color' => 'primary',
                                                     'title' => __('Access to Reports'),
                                                     'desc'  => __('Access audited financial statements, annual reports, and company performance records.')],
                                                    ['icon' => 'ri-group-line', 'color' => 'primary',
                                                     'title' => __('Meeting Participation'),
                                                     'desc'  => __('Receive notices of and participate in Annual General Meetings (AGMs) and Extraordinary General Meetings (EGMs).')],
                                                    ['icon' => 'ri-exchange-line', 'color' => 'info',
                                                     'title' => __('Share Transfer'),
                                                     'desc'  => __('Transfer shares subject to the company\'s articles of association and any applicable share transfer restrictions.')],
                                                    ['icon' => 'ri-search-eye-line', 'color' => 'info',
                                                     'title' => __('Inspect Records'),
                                                     'desc'  => __('Inspect certain statutory company records as permitted under the Companies Act, 2012.')],
                                                    ['icon' => 'ri-mail-send-line', 'color' => 'warning',
                                                     'title' => __('Meeting Notices'),
                                                     'desc'  => __('Receive adequate notice of all general meetings and resolutions to be tabled.')],
                                                    ['icon' => 'ri-scales-line', 'color' => 'danger',
                                                     'title' => __('Challenge Unlawful Actions'),
                                                     'desc'  => __('Challenge resolutions or actions that are unlawful, oppressive, or unfairly prejudicial to your interests through appropriate legal channels.')],
                                                ];
                                            @endphp
                                            @foreach($rights_list as $item)
                                            <div class="col-md-6 col-lg-3">
                                                <div class="border rounded p-3 h-100">
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <i class="{{ $item['icon'] }} text-{{ $item['color'] }} fs-5"></i>
                                                        <strong class="small">{{ $item['title'] }}</strong>
                                                    </div>
                                                    <p class="text-muted small mb-0">{{ $item['desc'] }}</p>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Shareholder Obligations --}}
                                    <div class="tab-pane fade" id="pane-obligations" role="tabpanel">
                                        <div class="alert alert-warning py-2 mb-3">
                                            <i class="ri-error-warning-line me-1"></i>
                                            {{ __('As a shareholder you carry the following obligations. Breach of these obligations may result in disciplinary action, liability, or legal proceedings.') }}
                                        </div>
                                        <div class="row g-3">
                                            @php
                                                $obligations = [
                                                    ['icon' => 'ri-thumb-up-line',        'title' => __('Act in Good Faith'),         'desc' => __('Exercise your shareholder rights honestly and in the best interest of the company, not to the detriment of other shareholders.')],
                                                    ['icon' => 'ri-building-line',         'title' => __('Protect Company Reputation'), 'desc' => __('Avoid actions, statements, or conduct that could damage the reputation or business interests of Jinja Consolidated Properties.')],
                                                    ['icon' => 'ri-file-text-line',        'title' => __('Comply with Constitution'),   'desc' => __('Adhere to the company\'s articles of association, governance policies, and resolutions passed at general meetings.')],
                                                    ['icon' => 'ri-spy-line',              'title' => __('No Insider Abuse'),           'desc' => __('Do not exploit privileged or confidential company information for personal financial gain or to benefit third parties.')],
                                                    ['icon' => 'ri-lock-password-line',    'title' => __('Maintain Confidentiality'),   'desc' => __('Keep confidential all non-public company information including financials, strategies, client lists, and board discussions.')],
                                                    ['icon' => 'ri-alert-line',            'title' => __('Declare Conflicts of Interest'), 'desc' => __('Promptly disclose any personal interest in matters being decided by the company to avoid conflicts of interest.')],
                                                ];
                                            @endphp
                                            @foreach($obligations as $item)
                                            <div class="col-md-6">
                                                <div class="d-flex gap-3 border rounded p-3 h-100">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-sm bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                            <i class="{{ $item['icon'] }} text-warning fs-5"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <strong class="small d-block mb-1">{{ $item['title'] }}</strong>
                                                        <p class="text-muted small mb-0">{{ $item['desc'] }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Applicable Laws --}}
                                    <div class="tab-pane fade" id="pane-laws" role="tabpanel">
                                        <div class="alert alert-info py-2 mb-3">
                                            <i class="ri-government-line me-1"></i>
                                            {{ __('Jinja Consolidated Properties operates under the following Ugandan laws. As a shareholder, your rights and the company obligations are governed by these statutes.') }}
                                        </div>
                                        <div class="row g-3">
                                            @php
                                                $laws = [
                                                    ['title' => __('Companies Act, 2012'),               'color' => 'primary', 'scope' => __('Governs incorporation, shareholding, directors\' duties, corporate governance, annual returns, financial reporting, and company meetings.')],
                                                    ['title' => __('Land Act, Cap 227'),                  'color' => 'success', 'scope' => __('Regulates land ownership, tenure systems, occupancy rights, leasehold arrangements, land transactions, and land disputes.')],
                                                    ['title' => __('Registration of Titles Act'),         'color' => 'success', 'scope' => __('Provides for land title registration, transfer procedures, ownership verification, caveats, and encumbrances.')],
                                                    ['title' => __('Physical Planning Act'),              'color' => 'info',    'scope' => __('Controls development approvals, building permissions, land subdivision, zoning compliance, and urban development standards.')],
                                                    ['title' => __('Condominium Property Act'),           'color' => 'info',    'scope' => __('Applies to apartment ownership, shared property management, unit ownership rights, and condominium corporations.')],
                                                    ['title' => __('Income Tax Act'),                     'color' => 'warning', 'scope' => __('Provides for corporate taxation, rental income tax, withholding taxes, and capital gains obligations on property transactions.')],
                                                    ['title' => __('Data Protection & Privacy Act, 2019'),'color' => 'danger',  'scope' => __('Requires protection of client data, consent-based data collection, secure storage of personal information, and confidentiality procedures.')],
                                                    ['title' => __('Anti-Money Laundering Act'),          'color' => 'danger',  'scope' => __('Requires customer due diligence, source-of-funds verification, suspicious transaction reporting, and transaction monitoring.')],
                                                    ['title' => __('Value Added Tax (VAT) Act'),          'color' => 'warning', 'scope' => __('Applicable to commercial property transactions, VAT obligations, and tax invoicing requirements.')],
                                                    ['title' => __('Local Government Act'),               'color' => 'secondary','scope' => __('Regulates local authority approvals, property rates, trading licenses, and municipal enforcement.')],
                                                    ['title' => __('Occupational Safety & Health Act'),  'color' => 'secondary','scope' => __('Requires safe workplaces, safety procedures, accident prevention, and protective measures at all company properties.')],
                                                    ['title' => __('National Environment Act'),           'color' => 'success', 'scope' => __('Requires environmental protection, responsible waste management, environmental impact compliance, and sustainable development.')],
                                                ];
                                            @endphp
                                            @foreach($laws as $law)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="border rounded p-3 h-100">
                                                    <span class="badge bg-{{ $law['color'] }} bg-opacity-10 text-{{ $law['color'] }} mb-2">{{ $law['title'] }}</span>
                                                    <p class="text-muted small mb-0">{{ $law['scope'] }}</p>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Code of Conduct --}}
                                    <div class="tab-pane fade" id="pane-conduct" role="tabpanel">
                                        <div class="alert alert-secondary py-2 mb-3">
                                            <i class="ri-user-star-line me-1"></i>
                                            {{ __('All shareholders and company representatives are expected to uphold the following ethical standards as set out in the Jinja Consolidated Properties Governance Framework.') }}
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="card border-0 bg-light h-100">
                                                    <div class="card-body">
                                                        <h6 class="fw-bold mb-3"><i class="ri-check-double-line text-success me-1"></i>{{ __('Expected Standards') }}</h6>
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach([__('Act honestly and with integrity at all times'), __('Avoid exploitation of tenants, clients, or counterparties'), __('Respect the rights and dignity of all clients and stakeholders'), __('Avoid discrimination on any grounds'), __('Promptly disclose any conflict of interest'), __('Promote transparency in all dealings'), __('Protect the reputation and confidentiality of the company'), __('Report any misconduct, fraud, or suspicious activity immediately')] as $item)
                                                            <li class="d-flex align-items-start gap-2 mb-2">
                                                                <i class="ri-checkbox-circle-fill text-success mt-1 flex-shrink-0"></i>
                                                                <span class="small text-muted">{{ $item }}</span>
                                                            </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card border-0 bg-light h-100">
                                                    <div class="card-body">
                                                        <h6 class="fw-bold mb-3"><i class="ri-close-circle-line text-danger me-1"></i>{{ __('Strictly Prohibited') }}</h6>
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach([__('Theft, embezzlement, or misappropriation of company funds'), __('Forgery or use of fraudulent land titles or documents'), __('Bribery or acceptance of unauthorized commissions'), __('Sexual harassment or workplace discrimination'), __('Data theft or unauthorized access to company systems'), __('Insider trading or abuse of confidential information'), __('Fake property listings or misleading marketing'), __('Money laundering or facilitating illegal transactions')] as $item)
                                                            <li class="d-flex align-items-start gap-2 mb-2">
                                                                <i class="ri-close-circle-fill text-danger mt-1 flex-shrink-0"></i>
                                                                <span class="small text-muted">{{ $item }}</span>
                                                            </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="alert alert-warning py-2 mb-0">
                                                    <i class="ri-information-line me-1"></i>
                                                    {{ __('Violations of the code of conduct may result in suspension of shareholder privileges, legal proceedings, or referral to relevant regulatory authorities under the Companies Act, 2012.') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Penalties --}}
                                    <div class="tab-pane fade" id="pane-penalties" role="tabpanel">
                                        <div class="alert alert-danger py-2 mb-3">
                                            <i class="ri-alert-line me-1"></i>
                                            {{ __('Jinja Consolidated Properties enforces a zero-tolerance policy for fraud, corruption, and misconduct. The following penalties apply to violations of company policy, contractual obligations, and applicable law.') }}
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="border border-warning rounded p-3 h-100">
                                                    <h6 class="fw-bold text-warning mb-3"><i class="ri-user-settings-line me-1"></i>{{ __('Progressive Disciplinary Steps') }}</h6>
                                                    <ol class="ps-3 mb-0">
                                                        @foreach([__('Verbal warning'), __('Written warning'), __('Suspension of privileges'), __('Salary or dividend recovery'), __('Demotion or share restriction'), __('Termination of agreement/contract'), __('Reporting to Police / URA'), __('Civil or criminal legal action')] as $step)
                                                        <li class="small text-muted mb-1">{{ $step }}</li>
                                                        @endforeach
                                                    </ol>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="border border-danger rounded p-3 h-100">
                                                    <h6 class="fw-bold text-danger mb-3"><i class="ri-skull-line me-1"></i>{{ __('Offences Attracting Immediate Dismissal') }}</h6>
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach([__('Theft of company or client funds'), __('Land fraud or fake title use'), __('Document forgery'), __('Embezzlement'), __('Bribery of officials'), __('Data theft or system breach'), __('Misappropriation of client funds'), __('Unauthorized property sales')] as $item)
                                                        <li class="d-flex align-items-start gap-2 mb-1">
                                                            <i class="ri-error-warning-fill text-danger mt-1 flex-shrink-0 small"></i>
                                                            <span class="small text-muted">{{ $item }}</span>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="border border-info rounded p-3 h-100">
                                                    <h6 class="fw-bold text-info mb-3"><i class="ri-home-gear-line me-1"></i>{{ __('Tenant / Client Violations') }}</h6>
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach([__('Formal eviction proceedings'), __('Contract or tenancy termination'), __('Financial penalties & recovery'), __('Recovery of damages through courts'), __('Blacklisting from future dealings'), __('Civil court action'), __('Referral to regulatory authorities')] as $item)
                                                        <li class="d-flex align-items-start gap-2 mb-1">
                                                            <i class="ri-arrow-right-s-fill text-info mt-1 flex-shrink-0 small"></i>
                                                            <span class="small text-muted">{{ $item }}</span>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                    <div class="mt-3 pt-2 border-top">
                                                        <p class="small text-muted mb-0"><strong>{{ __('Dispute Resolution:') }}</strong> {{ __('Disputes may be referred to courts of law, arbitration, mediation, land tribunals, or relevant regulatory authorities.') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>{{-- end tab-content --}}

                                <div class="border-top mt-4 pt-3">
                                    <p class="text-muted small mb-0">
                                        <i class="ri-book-mark-line me-1"></i>
                                        {{ __('Source: Jinja Consolidated Properties — Real Estate Governance, Compliance, Rights, Duties, Rules, Penalties & Operational Policy Manual (Uganda, v1.0). This summary is for reference only and does not replace formal legal advice or the full policy document.') }}
                                    </p>
                                </div>

                            </div>{{-- end card-body --}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
@php
    $chartMonthsData   = $chartMonths   ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $chartRevenueData  = $monthlyRevenue  ?? [0,0,0,0,0,0,0,0,0,0,0,0];
    $chartDividendData = $monthlyDividends ?? [0,0,0,0,0,0,0,0,0,0,0,0];
@endphp
<script>
(function() {
    var months      = @json($chartMonthsData);
    var revenueData = @json($chartRevenueData);
    var dividendData = @json($chartDividendData);

    // Revenue area chart
    if (document.querySelector('#shareholderRevenueChart')) {
        new ApexCharts(document.querySelector('#shareholderRevenueChart'), {
            series: [{ name: 'Revenue (UGX)', data: revenueData }],
            chart: { type: 'area', height: 220, toolbar: { show: false } },
            colors: ['#6366f1'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.05 } },
            xaxis: { categories: months },
            yaxis: { labels: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } },
            tooltip: { y: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } }
        }).render();
    }

    // Dividends bar chart
    if (document.querySelector('#shareholderDividendChart')) {
        new ApexCharts(document.querySelector('#shareholderDividendChart'), {
            series: [{ name: 'Dividends (UGX)', data: dividendData }],
            chart: { type: 'bar', height: 220, toolbar: { show: false } },
            colors: ['#f59e0b'],
            plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
            dataLabels: { enabled: false },
            xaxis: { categories: months },
            yaxis: { labels: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } },
            tooltip: { y: { formatter: function(v) { return 'UGX ' + Number(v).toLocaleString(); } } }
        }).render();
    }
})();
</script>
@endpush
